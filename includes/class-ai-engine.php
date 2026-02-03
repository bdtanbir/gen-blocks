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
     * OpenAI API endpoint
     *
     * @var string
     */
    private $openai_endpoint = 'https://api.openai.com/v1/chat/completions';

    /**
     * System prompt for block generation
     *
     * @var string
     */
    private $system_prompt = '';

    /**
     * Constructor
     *
     * @param Settings $settings Settings instance.
     */
    public function __construct(Settings $settings) {
        $this->settings = $settings;
        $this->system_prompt = $this->get_system_prompt();
    }

    /**
     * Generate block from prompt
     *
     * @param string $prompt  User's prompt.
     * @param array  $context Additional context.
     * @return array|WP_Error Generated block data or error.
     */
    public function generate_block($prompt, $context = []) {
        // Check if API key is set
        if (!$this->settings->has_api_key()) {
            return new \WP_Error(
                'no_api_key',
                __('API key is not configured. Please add your OpenAI API key in settings.', 'gen-blocks'),
                ['status' => 400]
            );
        }

        // Check cache first
        if ($this->settings->get('cache_enabled')) {
            $cached = $this->get_cached_response($prompt, $context);
            if (false !== $cached) {
                return $cached;
            }
        }

        // Build the full prompt
        $full_prompt = $this->build_prompt($prompt, $context);

        // Call AI API
        $response = $this->call_openai($full_prompt);

        if (is_wp_error($response)) {
            return $response;
        }

        // Parse the response
        $block_data = $this->parse_response($response);

        if (is_wp_error($block_data)) {
            return $block_data;
        }

        // Cache the result
        if ($this->settings->get('cache_enabled')) {
            $this->cache_response($prompt, $context, $block_data);
        }

        return $block_data;
    }

    /**
     * Get system prompt for AI
     *
     * @return string
     */
    private function get_system_prompt() {
        $prompt = <<<'PROMPT'
You are an expert WordPress Gutenberg block generator. Your task is to convert natural language descriptions into valid Gutenberg block JSON.

RULES:
1. Always return ONLY valid JSON, no markdown code blocks, no explanations, no additional text
2. Use standard WordPress core blocks when possible
3. Include proper attributes for styling and content
4. Nest blocks logically for complex layouts
5. Generate accessible, semantic HTML
6. Include appropriate CSS classes

AVAILABLE CORE BLOCKS:
- core/group (container with inner blocks)
- core/heading (h1-h6 headings, use "content" for text, "level" for heading level)
- core/paragraph (text content, use "content" for text)
- core/button (use inside core/buttons)
- core/buttons (container for button blocks)
- core/image (images)
- core/columns (multi-column layouts)
- core/column (single column, use inside core/columns)
- core/spacer (vertical spacing, use "height" attribute)
- core/separator (horizontal line)
- core/list (unordered/ordered lists)
- core/quote (blockquotes)
- core/cover (background image with overlay)

CUSTOM BLOCKS (use for specific patterns):
- genblocks/cta (call-to-action section)
- genblocks/hero (hero section)

OUTPUT FORMAT (return ONLY this JSON structure):
{
  "blockName": "core/group",
  "attrs": {
    "className": "custom-class",
    "align": "wide",
    "style": {
      "spacing": {
        "padding": {
          "top": "40px",
          "bottom": "40px"
        }
      }
    }
  },
  "innerBlocks": []
}

IMPORTANT ATTRIBUTE NOTES:
- For headings: use "content" (string) and "level" (number 1-6)
- For paragraphs: use "content" (string)
- For buttons: wrap in core/buttons, use "text" and "url" attributes
- For columns: use "core/columns" with "core/column" innerBlocks
- For alignment: use "align" attribute with values: "left", "center", "right", "wide", "full"
- For colors: use "backgroundColor", "textColor" or "style.color.background", "style.color.text"
PROMPT;

        return apply_filters('genblocks_system_prompt', $prompt);
    }

    /**
     * Build the full prompt with context
     *
     * @param string $user_input User's prompt.
     * @param array  $context    Additional context.
     * @return string
     */
    private function build_prompt($user_input, $context = []) {
        $context_info = '';

        if (!empty($context)) {
            $context_parts = [];

            if (isset($context['existing_blocks'])) {
                $context_parts[] = 'Existing blocks on page: ' . implode(', ', $context['existing_blocks']);
            }

            if (isset($context['theme_colors'])) {
                $context_parts[] = 'Theme colors: ' . wp_json_encode($context['theme_colors']);
            }

            if (isset($context['page_type'])) {
                $context_parts[] = 'Page type: ' . $context['page_type'];
            }

            if (!empty($context_parts)) {
                $context_info = "\n\nCURRENT CONTEXT:\n" . implode("\n", $context_parts);
            }
        }

        $full_prompt = $user_input . $context_info . "\n\nGenerate the Gutenberg block JSON now:";

        return apply_filters('genblocks_user_prompt', $full_prompt, $user_input, $context);
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
                    'content' => $this->system_prompt,
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
     * Parse AI response into block data
     *
     * @param array $response AI response data.
     * @return array|WP_Error Parsed block data or error.
     */
    private function parse_response($response) {
        $content = $response['content'];

        // Clean the response - remove markdown code blocks if present
        $content = preg_replace('/^```(?:json)?\s*/m', '', $content);
        $content = preg_replace('/\s*```$/m', '', $content);
        $content = trim($content);

        // Find JSON object
        $first_brace = strpos($content, '{');
        $last_brace = strrpos($content, '}');

        if (false === $first_brace || false === $last_brace) {
            return new \WP_Error(
                'invalid_json',
                __('AI response does not contain valid JSON', 'gen-blocks'),
                ['status' => 500, 'raw_response' => $content]
            );
        }

        $json_string = substr($content, $first_brace, $last_brace - $first_brace + 1);
        $block_data = json_decode($json_string, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new \WP_Error(
                'json_parse_error',
                __('Failed to parse AI response as JSON: ', 'gen-blocks') . json_last_error_msg(),
                ['status' => 500, 'raw_response' => $content]
            );
        }

        // Validate block structure
        if (!isset($block_data['blockName'])) {
            return new \WP_Error(
                'invalid_block',
                __('AI response missing required blockName field', 'gen-blocks'),
                ['status' => 500]
            );
        }

        // Add token usage info
        $block_data['_meta'] = [
            'tokens_used'       => $response['tokens_used'],
            'prompt_tokens'     => $response['prompt_tokens'],
            'completion_tokens' => $response['completion_tokens'],
            'model'             => $response['model'],
        ];

        return $block_data;
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
