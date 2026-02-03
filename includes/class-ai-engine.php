<?php
/**
 * AI Engine Class
 *
 * @package GenBlocks
 */

namespace GenBlocks;

defined('ABSPATH') || exit;

/**
 * Handles AI API integration for block generation
 */
class AI_Engine {

    /**
     * Settings instance
     *
     * @var Settings
     */
    private $settings;

    /**
     * Prompt Templates instance
     *
     * @var Prompt_Templates
     */
    private $prompt_templates;

    /**
     * Response Parser instance
     *
     * @var Response_Parser
     */
    private $response_parser;

    /**
     * OpenAI API endpoint
     *
     * @var string
     */
    private $openai_endpoint = 'https://api.openai.com/v1/chat/completions';

    /**
     * OpenRouter API endpoint
     *
     * @var string
     */
    private $openrouter_endpoint = 'https://openrouter.ai/api/v1/chat/completions';

    /**
     * Whether dev mode is enabled (uses OpenRouter)
     *
     * @var bool
     */
    private $dev_mode = false;

    /**
     * Constructor
     *
     * @param Settings         $settings         Settings instance.
     * @param Prompt_Templates $prompt_templates Prompt Templates instance (optional).
     * @param Response_Parser  $response_parser  Response Parser instance (optional).
     */
    public function __construct(Settings $settings, Prompt_Templates $prompt_templates = null, Response_Parser $response_parser = null) {
        $this->settings = $settings;
        $this->prompt_templates = $prompt_templates ?: new Prompt_Templates();
        $this->response_parser = $response_parser ?: new Response_Parser();

        // Check if dev mode is enabled (OpenRouter with static API key)
        $this->dev_mode = defined('GENBLOCKS_DEV_MODE') && GENBLOCKS_DEV_MODE;
    }

    /**
     * Check if dev mode is enabled
     *
     * @return bool
     */
    public function is_dev_mode() {
        return $this->dev_mode;
    }

    /**
     * Get the OpenRouter API key for dev mode
     *
     * @return string|null
     */
    private function get_openrouter_api_key() {
        // First check for constant
        if (defined('GENBLOCKS_OPENROUTER_API_KEY')) {
            return GENBLOCKS_OPENROUTER_API_KEY;
        }

        // Fallback: check settings for openrouter_api_key
        return $this->settings->get('openrouter_api_key', '');
    }

    /**
     * Get the model to use based on mode
     *
     * @return string
     */
    private function get_model() {
        if ($this->dev_mode) {
            // Use a cost-effective model for dev mode via OpenRouter
            // Options: anthropic/claude-3-haiku, openai/gpt-3.5-turbo, google/gemini-flash-1.5
            return defined('GENBLOCKS_OPENROUTER_MODEL')
                ? GENBLOCKS_OPENROUTER_MODEL
                : 'anthropic/claude-3.5-sonnet';
        }

        return $this->settings->get('model', 'gpt-4');
    }

    /**
     * Generate block from prompt
     *
     * @param string $prompt  User's prompt.
     * @param array  $context Additional context.
     * @return array|WP_Error Generated block data or error.
     */
    public function generate_block($prompt, $context = []) {
        // Check if API key is set (dev mode uses OpenRouter key)
        if ($this->dev_mode) {
            $openrouter_key = $this->get_openrouter_api_key();
            if (empty($openrouter_key)) {
                return new \WP_Error(
                    'no_api_key',
                    __('Dev mode is enabled but GENBLOCKS_OPENROUTER_API_KEY is not defined.', 'gen-blocks'),
                    ['status' => 400]
                );
            }
        } elseif (!$this->settings->has_api_key()) {
            return new \WP_Error(
                'no_api_key',
                __('API key is not configured. Please add your OpenAI API key in settings.', 'gen-blocks'),
                ['status' => 400]
            );
        }

        // Validate prompt
        $validation = $this->prompt_templates->validate_prompt($prompt);
        if (is_wp_error($validation)) {
            return $validation;
        }

        // Check cache first
        if ($this->settings->get('cache_enabled')) {
            $cached = $this->get_cached_response($prompt, $context);
            if (false !== $cached) {
                return $cached;
            }
        }

        // Enhance and build the full prompt
        $enhanced_prompt = $this->prompt_templates->enhance_prompt($prompt);
        $full_prompt = $this->prompt_templates->build_user_prompt($enhanced_prompt, $context);

        // Call AI API (OpenRouter in dev mode, OpenAI in production)
        $response = $this->dev_mode
            ? $this->call_openrouter($full_prompt)
            : $this->call_openai($full_prompt);

        if (is_wp_error($response)) {
            return $response;
        }

        // Parse the response using dedicated parser
        $block_data = $this->response_parser->parse($response['content']);

        if (is_wp_error($block_data)) {
            return $block_data;
        }

        // Add token usage info
        $block_data['_meta'] = [
            'tokens_used'       => $response['tokens_used'],
            'prompt_tokens'     => $response['prompt_tokens'],
            'completion_tokens' => $response['completion_tokens'],
            'model'             => $response['model'],
        ];

        // Cache the result
        if ($this->settings->get('cache_enabled')) {
            $this->cache_response($prompt, $context, $block_data);
        }

        return $block_data;
    }

    /**
     * Get prompt templates instance
     *
     * @return Prompt_Templates
     */
    public function get_prompt_templates() {
        return $this->prompt_templates;
    }

    /**
     * Get response parser instance
     *
     * @return Response_Parser
     */
    public function get_response_parser() {
        return $this->response_parser;
    }

    /**
     * Call OpenAI API
     *
     * @param string $prompt Full prompt to send.
     * @return array|WP_Error Response data or error.
     */
    private function call_openai($prompt) {
        $api_key = $this->settings->get('api_key');
        $model = $this->settings->get('model', 'gpt-4');
        $max_tokens = $this->settings->get('max_tokens', 2000);
        $temperature = $this->settings->get('temperature', 0.7);

        $body = [
            'model'       => $model,
            'messages'    => [
                [
                    'role'    => 'system',
                    'content' => $this->prompt_templates->get_system_prompt(),
                ],
                [
                    'role'    => 'user',
                    'content' => $prompt,
                ],
            ],
            'max_tokens'  => $max_tokens,
            'temperature' => $temperature,
        ];

        $response = wp_remote_post(
            $this->openai_endpoint,
            [
                'timeout' => 60,
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type'  => 'application/json',
                ],
                'body'    => wp_json_encode($body),
            ]
        );

        if (is_wp_error($response)) {
            return new \WP_Error(
                'api_request_failed',
                __('Failed to connect to OpenAI API: ', 'gen-blocks') . $response->get_error_message(),
                ['status' => 500]
            );
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if ($status_code !== 200) {
            $error_message = isset($data['error']['message'])
                ? $data['error']['message']
                : __('Unknown API error', 'gen-blocks');

            return new \WP_Error(
                'api_error',
                $error_message,
                ['status' => $status_code]
            );
        }

        if (!isset($data['choices'][0]['message']['content'])) {
            return new \WP_Error(
                'invalid_response',
                __('Invalid response from OpenAI API', 'gen-blocks'),
                ['status' => 500]
            );
        }

        return [
            'content'      => $data['choices'][0]['message']['content'],
            'tokens_used'  => $data['usage']['total_tokens'] ?? 0,
            'prompt_tokens' => $data['usage']['prompt_tokens'] ?? 0,
            'completion_tokens' => $data['usage']['completion_tokens'] ?? 0,
            'model'        => $data['model'] ?? $model,
        ];
    }

    /**
     * Call OpenRouter API (for dev mode)
     *
     * @param string $prompt Full prompt to send.
     * @return array|WP_Error Response data or error.
     */
    private function call_openrouter($prompt) {
        $api_key = $this->get_openrouter_api_key();
        $model = $this->get_model();
        $max_tokens = $this->settings->get('max_tokens', 2000);
        $temperature = $this->settings->get('temperature', 0.7);

        $body = [
            'model'       => $model,
            'messages'    => [
                [
                    'role'    => 'system',
                    'content' => $this->prompt_templates->get_system_prompt(),
                ],
                [
                    'role'    => 'user',
                    'content' => $prompt,
                ],
            ],
            'max_tokens'  => $max_tokens,
            'temperature' => $temperature,
        ];

        $response = wp_remote_post(
            $this->openrouter_endpoint,
            [
                'timeout' => 120,
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type'  => 'application/json',
                    'HTTP-Referer'  => home_url(),
                    'X-Title'       => 'GenBlocks WordPress Plugin',
                ],
                'body'    => wp_json_encode($body),
            ]
        );

        if (is_wp_error($response)) {
            return new \WP_Error(
                'api_request_failed',
                __('Failed to connect to OpenRouter API: ', 'gen-blocks') . $response->get_error_message(),
                ['status' => 500]
            );
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if ($status_code !== 200) {
            $error_message = isset($data['error']['message'])
                ? $data['error']['message']
                : __('Unknown API error', 'gen-blocks');

            return new \WP_Error(
                'api_error',
                $error_message,
                ['status' => $status_code]
            );
        }

        if (!isset($data['choices'][0]['message']['content'])) {
            return new \WP_Error(
                'invalid_response',
                __('Invalid response from OpenRouter API', 'gen-blocks'),
                ['status' => 500]
            );
        }

        return [
            'content'           => $data['choices'][0]['message']['content'],
            'tokens_used'       => $data['usage']['total_tokens'] ?? 0,
            'prompt_tokens'     => $data['usage']['prompt_tokens'] ?? 0,
            'completion_tokens' => $data['usage']['completion_tokens'] ?? 0,
            'model'             => $data['model'] ?? $model,
        ];
    }

    /**
     * Get cached response
     *
     * @param string $prompt  User prompt.
     * @param array  $context Context data.
     * @return array|false Cached data or false.
     */
    private function get_cached_response($prompt, $context) {
        $cache_key = $this->get_cache_key($prompt, $context);
        return get_transient($cache_key);
    }

    /**
     * Cache response
     *
     * @param string $prompt     User prompt.
     * @param array  $context    Context data.
     * @param array  $block_data Block data to cache.
     */
    private function cache_response($prompt, $context, $block_data) {
        $cache_key = $this->get_cache_key($prompt, $context);
        $duration = $this->settings->get('cache_duration', 3600);
        set_transient($cache_key, $block_data, $duration);
    }

    /**
     * Generate cache key
     *
     * @param string $prompt  User prompt.
     * @param array  $context Context data.
     * @return string
     */
    private function get_cache_key($prompt, $context) {
        return 'genblocks_' . md5($prompt . serialize($context));
    }

    /**
     * Clear all cached responses
     *
     * @return bool
     */
    public function clear_cache() {
        global $wpdb;

        $result = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                '_transient_genblocks_%'
            )
        );

        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                '_transient_timeout_genblocks_%'
            )
        );

        return false !== $result;
    }

    /**
     * Test API connection
     *
     * @return array|WP_Error Test result or error.
     */
    public function test_connection() {
        // Dev mode: test OpenRouter connection
        if ($this->dev_mode) {
            return $this->test_openrouter_connection();
        }

        // Production: test OpenAI connection
        if (!$this->settings->has_api_key()) {
            return new \WP_Error(
                'no_api_key',
                __('API key is not configured', 'gen-blocks'),
                ['status' => 400]
            );
        }

        $api_key = $this->settings->get('api_key');

        $response = wp_remote_get(
            'https://api.openai.com/v1/models',
            [
                'timeout' => 30,
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_key,
                ],
            ]
        );

        if (is_wp_error($response)) {
            return new \WP_Error(
                'connection_failed',
                __('Failed to connect to OpenAI API: ', 'gen-blocks') . $response->get_error_message(),
                ['status' => 500]
            );
        }

        $status_code = wp_remote_retrieve_response_code($response);

        if ($status_code === 200) {
            return [
                'success' => true,
                'message' => __('Successfully connected to OpenAI API', 'gen-blocks'),
            ];
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        $error_message = isset($data['error']['message'])
            ? $data['error']['message']
            : __('Invalid API key or connection error', 'gen-blocks');

        return new \WP_Error(
            'api_error',
            $error_message,
            ['status' => $status_code]
        );
    }

    /**
     * Test OpenRouter API connection
     *
     * @return array|WP_Error Test result or error.
     */
    private function test_openrouter_connection() {
        $api_key = $this->get_openrouter_api_key();

        if (empty($api_key)) {
            return new \WP_Error(
                'no_api_key',
                __('OpenRouter API key is not configured. Define GENBLOCKS_OPENROUTER_API_KEY in wp-config.php', 'gen-blocks'),
                ['status' => 400]
            );
        }

        // OpenRouter doesn't have a models endpoint like OpenAI
        // So we'll make a minimal chat completion request to test
        $response = wp_remote_post(
            $this->openrouter_endpoint,
            [
                'timeout' => 30,
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type'  => 'application/json',
                    'HTTP-Referer'  => home_url(),
                    'X-Title'       => 'GenBlocks WordPress Plugin',
                ],
                'body'    => wp_json_encode([
                    'model'      => $this->get_model(),
                    'messages'   => [
                        ['role' => 'user', 'content' => 'Say "OK" if you can hear me.'],
                    ],
                    'max_tokens' => 10,
                ]),
            ]
        );

        if (is_wp_error($response)) {
            return new \WP_Error(
                'connection_failed',
                __('Failed to connect to OpenRouter API: ', 'gen-blocks') . $response->get_error_message(),
                ['status' => 500]
            );
        }

        $status_code = wp_remote_retrieve_response_code($response);

        if ($status_code === 200) {
            return [
                'success'  => true,
                'message'  => __('Successfully connected to OpenRouter API (Dev Mode)', 'gen-blocks'),
                'dev_mode' => true,
                'model'    => $this->get_model(),
            ];
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        $error_message = isset($data['error']['message'])
            ? $data['error']['message']
            : __('Invalid API key or connection error', 'gen-blocks');

        return new \WP_Error(
            'api_error',
            $error_message,
            ['status' => $status_code]
        );
    }

    /**
     * Calculate estimated cost for tokens
     *
     * @param int    $tokens Token count.
     * @param string $type   Token type (prompt or completion).
     * @return float
     */
    public function calculate_cost($tokens, $type = 'total') {
        $model = $this->settings->get('model', 'gpt-4');

        // Pricing per 1K tokens (approximate as of 2024)
        $pricing = [
            'gpt-4' => [
                'prompt'     => 0.03,
                'completion' => 0.06,
            ],
            'gpt-4-turbo' => [
                'prompt'     => 0.01,
                'completion' => 0.03,
            ],
            'gpt-3.5-turbo' => [
                'prompt'     => 0.0005,
                'completion' => 0.0015,
            ],
        ];

        $model_pricing = $pricing[$model] ?? $pricing['gpt-4'];
        $rate = $type === 'prompt' ? $model_pricing['prompt'] : $model_pricing['completion'];

        return ($tokens / 1000) * $rate;
    }
}
