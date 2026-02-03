<?php
/**
 * REST API Class
 *
 * @package GenBlocks
 */

namespace GenBlocks;

defined('ABSPATH') || exit;

/**
 * Handles REST API endpoints for the plugin
 */
class REST_API {

    /**
     * API namespace
     *
     * @var string
     */
    private $namespace = 'genblocks/v1';

    /**
     * AI Engine instance
     *
     * @var AI_Engine
     */
    private $ai_engine;

    /**
     * Block Generator instance
     *
     * @var Block_Generator
     */
    private $block_generator;

    /**
     * Settings instance
     *
     * @var Settings
     */
    private $settings;

    /**
     * Usage Tracker instance
     *
     * @var Usage_Tracker
     */
    private $usage_tracker;

    /**
     * Template Library instance
     *
     * @var Template_Library
     */
    private $template_library;

    /**
     * Constructor
     *
     * @param AI_Engine        $ai_engine        AI Engine instance.
     * @param Block_Generator  $block_generator  Block Generator instance.
     * @param Settings         $settings         Settings instance.
     * @param Usage_Tracker    $usage_tracker    Usage Tracker instance.
     * @param Template_Library $template_library Template Library instance.
     */
    public function __construct(AI_Engine $ai_engine, Block_Generator $block_generator, Settings $settings, Usage_Tracker $usage_tracker, Template_Library $template_library = null) {
        $this->ai_engine = $ai_engine;
        $this->block_generator = $block_generator;
        $this->settings = $settings;
        $this->usage_tracker = $usage_tracker;
        $this->template_library = $template_library ?: new Template_Library();
    }

    /**
     * Register REST API routes
     */
    public function register_routes() {
        // Generate block from prompt
        register_rest_route(
            $this->namespace,
            '/generate',
            [
                'methods'             => 'POST',
                'callback'            => [$this, 'generate_block'],
                'permission_callback' => [$this, 'check_edit_permission'],
                'args'                => [
                    'prompt'  => [
                        'required'          => true,
                        'type'              => 'string',
                        'sanitize_callback' => 'sanitize_textarea_field',
                        'validate_callback' => function ($value) {
                            return !empty(trim($value)) && strlen($value) <= 2000;
                        },
                    ],
                    'context' => [
                        'required' => false,
                        'type'     => 'object',
                        'default'  => [],
                    ],
                ],
            ]
        );

        // Get settings
        register_rest_route(
            $this->namespace,
            '/settings',
            [
                [
                    'methods'             => 'GET',
                    'callback'            => [$this, 'get_settings'],
                    'permission_callback' => [$this, 'check_admin_permission'],
                ],
                [
                    'methods'             => 'POST',
                    'callback'            => [$this, 'update_settings'],
                    'permission_callback' => [$this, 'check_admin_permission'],
                    'args'                => [
                        'api_provider'      => [
                            'type' => 'string',
                        ],
                        'api_key'           => [
                            'type' => 'string',
                        ],
                        'model'             => [
                            'type' => 'string',
                        ],
                        'rate_limit'        => [
                            'type' => 'integer',
                        ],
                        'cache_enabled'     => [
                            'type' => 'boolean',
                        ],
                        'cache_duration'    => [
                            'type' => 'integer',
                        ],
                        'primary_color'     => [
                            'type' => 'string',
                        ],
                        'default_alignment' => [
                            'type' => 'string',
                        ],
                        'max_tokens'        => [
                            'type' => 'integer',
                        ],
                        'temperature'       => [
                            'type' => 'number',
                        ],
                    ],
                ],
            ]
        );

        // Test API connection
        register_rest_route(
            $this->namespace,
            '/test-connection',
            [
                'methods'             => 'POST',
                'callback'            => [$this, 'test_connection'],
                'permission_callback' => [$this, 'check_admin_permission'],
            ]
        );

        // Clear cache
        register_rest_route(
            $this->namespace,
            '/cache/clear',
            [
                'methods'             => 'POST',
                'callback'            => [$this, 'clear_cache'],
                'permission_callback' => [$this, 'check_admin_permission'],
            ]
        );

        // Get usage statistics
        register_rest_route(
            $this->namespace,
            '/usage',
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'get_usage'],
                'permission_callback' => [$this, 'check_admin_permission'],
                'args'                => [
                    'period' => [
                        'required' => false,
                        'type'     => 'string',
                        'default'  => 'month',
                        'enum'     => ['day', 'week', 'month', 'year', 'all'],
                    ],
                ],
            ]
        );

        // Get generation history
        register_rest_route(
            $this->namespace,
            '/history',
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'get_history'],
                'permission_callback' => [$this, 'check_edit_permission'],
                'args'                => [
                    'page'     => [
                        'required' => false,
                        'type'     => 'integer',
                        'default'  => 1,
                    ],
                    'per_page' => [
                        'required' => false,
                        'type'     => 'integer',
                        'default'  => 20,
                    ],
                ],
            ]
        );

        // Get templates
        register_rest_route(
            $this->namespace,
            '/templates',
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'get_templates'],
                'permission_callback' => [$this, 'check_edit_permission'],
                'args'                => [
                    'category' => [
                        'required' => false,
                        'type'     => 'string',
                    ],
                ],
            ]
        );

        // Get single template
        register_rest_route(
            $this->namespace,
            '/templates/(?P<id>[a-z0-9-]+)',
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'get_template'],
                'permission_callback' => [$this, 'check_edit_permission'],
                'args'                => [
                    'id' => [
                        'required' => true,
                        'type'     => 'string',
                    ],
                ],
            ]
        );

        // Apply template
        register_rest_route(
            $this->namespace,
            '/templates/(?P<id>[a-z0-9-]+)/apply',
            [
                'methods'             => 'POST',
                'callback'            => [$this, 'apply_template'],
                'permission_callback' => [$this, 'check_edit_permission'],
                'args'                => [
                    'id'        => [
                        'required' => true,
                        'type'     => 'string',
                    ],
                    'variables' => [
                        'required' => false,
                        'type'     => 'object',
                        'default'  => [],
                    ],
                ],
            ]
        );

        // Get example prompts
        register_rest_route(
            $this->namespace,
            '/prompts/examples',
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'get_example_prompts'],
                'permission_callback' => [$this, 'check_edit_permission'],
            ]
        );
    }

    /**
     * Check if user can edit posts (for block generation)
     *
     * @return bool
     */
    public function check_edit_permission() {
        return current_user_can('edit_posts');
    }

    /**
     * Check if user has admin permissions
     *
     * @return bool
     */
    public function check_admin_permission() {
        return current_user_can('manage_options');
    }

    /**
     * Generate block from prompt
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response|\WP_Error
     */
    public function generate_block($request) {
        $prompt = $request->get_param('prompt');
        $context = $request->get_param('context');
        $user_id = get_current_user_id();

        // Check rate limit
        if (!$this->check_rate_limit($user_id)) {
            return new \WP_Error(
                'rate_limit_exceeded',
                __('Daily rate limit exceeded. Please try again tomorrow or upgrade your plan.', 'gen-blocks'),
                ['status' => 429]
            );
        }

        // Generate block using AI
        $ai_response = $this->ai_engine->generate_block($prompt, $context);

        if (is_wp_error($ai_response)) {
            // Track failed request
            $this->usage_tracker->track($user_id, $prompt, '', 0, 0, 'failed');
            return $ai_response;
        }

        try {
            // Process and validate the block
            $result = $this->block_generator->process($ai_response);

            // Track successful request
            $tokens_used = $result['meta']['tokens_used'] ?? 0;
            $cost = $this->ai_engine->calculate_cost($tokens_used);
            $block_type = $result['block_json']['blockName'] ?? 'unknown';

            $this->usage_tracker->track($user_id, $prompt, $block_type, $tokens_used, $cost, 'success');

            return rest_ensure_response([
                'success'    => true,
                'block'      => $result['block_json'],
                'serialized' => $result['block'],
                'usage'      => [
                    'tokens_used' => $tokens_used,
                    'cost'        => $cost,
                    'remaining'   => $this->get_remaining_requests($user_id),
                ],
            ]);
        } catch (\Exception $e) {
            $this->usage_tracker->track($user_id, $prompt, '', 0, 0, 'failed');

            return new \WP_Error(
                'generation_failed',
                $e->getMessage(),
                ['status' => 500]
            );
        }
    }

    /**
     * Get settings
     *
     * @return \WP_REST_Response
     */
    public function get_settings() {
        return rest_ensure_response([
            'success'  => true,
            'settings' => $this->settings->get_public(),
        ]);
    }

    /**
     * Update settings
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response
     */
    public function update_settings($request) {
        $params = $request->get_params();

        // Remove non-setting params
        unset($params['_locale']);

        $this->settings->update($params);

        return rest_ensure_response([
            'success'  => true,
            'settings' => $this->settings->get_public(),
            'message'  => __('Settings saved successfully', 'gen-blocks'),
        ]);
    }

    /**
     * Test API connection
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function test_connection() {
        $result = $this->ai_engine->test_connection();

        if (is_wp_error($result)) {
            return $result;
        }

        return rest_ensure_response($result);
    }

    /**
     * Clear cache
     *
     * @return \WP_REST_Response
     */
    public function clear_cache() {
        $this->ai_engine->clear_cache();

        return rest_ensure_response([
            'success' => true,
            'message' => __('Cache cleared successfully', 'gen-blocks'),
        ]);
    }

    /**
     * Get usage statistics
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response
     */
    public function get_usage($request) {
        $period = $request->get_param('period');

        $stats = $this->usage_tracker->get_stats($period);
        $chart_data = $this->usage_tracker->get_chart_data($period);
        $block_types = $this->usage_tracker->get_block_type_stats($period);
        $recent = $this->usage_tracker->get_recent(10);

        return rest_ensure_response([
            'success'        => true,
            'stats'          => $stats,
            'chartData'      => $chart_data,
            'blockTypes'     => $block_types,
            'recentActivity' => $recent,
        ]);
    }

    /**
     * Get generation history
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response
     */
    public function get_history($request) {
        $page = $request->get_param('page');
        $per_page = $request->get_param('per_page');
        $user_id = get_current_user_id();

        // Admins can see all history, others only their own
        if (!current_user_can('manage_options')) {
            $history = $this->usage_tracker->get_user_history($user_id, $page, $per_page);
            $total = $this->usage_tracker->get_user_history_count($user_id);
        } else {
            $history = $this->usage_tracker->get_all_history($page, $per_page);
            $total = $this->usage_tracker->get_total_count();
        }

        return rest_ensure_response([
            'success'    => true,
            'history'    => $history,
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $per_page,
            'total_pages' => ceil($total / $per_page),
        ]);
    }

    /**
     * Get templates
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response
     */
    public function get_templates($request = null) {
        $category = $request ? $request->get_param('category') : null;

        if ($category) {
            $templates = $this->template_library->get_by_category($category);
        } else {
            $templates = $this->template_library->get_all();
        }

        $categories = $this->template_library->get_categories();

        return rest_ensure_response([
            'success'    => true,
            'templates'  => $templates,
            'categories' => $categories,
        ]);
    }

    /**
     * Get single template by ID
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response|\WP_Error
     */
    public function get_template($request) {
        $id = $request->get_param('id');
        $template = $this->template_library->get_by_id($id);

        if (!$template) {
            return new \WP_Error(
                'template_not_found',
                __('Template not found', 'get-blocks'),
                ['status' => 404]
            );
        }

        return rest_ensure_response([
            'success'  => true,
            'template' => $template,
        ]);
    }

    /**
     * Apply template with variables
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response|\WP_Error
     */
    public function apply_template($request) {
        $id = $request->get_param('id');
        $variables = $request->get_param('variables') ?: [];

        $structure = $this->template_library->apply($id, $variables);

        if (!$structure) {
            return new \WP_Error(
                'template_not_found',
                __('Template not found', 'get-blocks'),
                ['status' => 404]
            );
        }

        // Process through block generator for serialization
        try {
            $result = $this->block_generator->process($structure);

            return rest_ensure_response([
                'success'    => true,
                'block'      => $result['block_json'],
                'serialized' => $result['block'],
            ]);
        } catch (\Exception $e) {
            return new \WP_Error(
                'template_error',
                $e->getMessage(),
                ['status' => 500]
            );
        }
    }

    /**
     * Get example prompts
     *
     * @return \WP_REST_Response
     */
    public function get_example_prompts() {
        $prompt_templates = $this->ai_engine->get_prompt_templates();
        $examples = $prompt_templates->get_example_prompts();

        return rest_ensure_response([
            'success'  => true,
            'examples' => $examples,
        ]);
    }

    /**
     * Check rate limit for user
     *
     * @param int $user_id User ID.
     * @return bool
     */
    private function check_rate_limit($user_id) {
        $rate_limit = $this->settings->get('rate_limit', 100);
        $today_count = $this->usage_tracker->get_today_count($user_id);

        return $today_count < $rate_limit;
    }

    /**
     * Get remaining requests for user
     *
     * @param int $user_id User ID.
     * @return int
     */
    private function get_remaining_requests($user_id) {
        $rate_limit = $this->settings->get('rate_limit', 100);
        $today_count = $this->usage_tracker->get_today_count($user_id);

        return max(0, $rate_limit - $today_count);
    }
}
