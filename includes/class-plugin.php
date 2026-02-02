<?php
/**
 * Main Plugin Class
 *
 * @package GenBlocks
 */

namespace GenBlocks;

defined('ABSPATH') || exit;

/**
 * Plugin main class - Singleton pattern
 */
class Plugin {

    /**
     * Single instance of the class
     *
     * @var Plugin|null
     */
    private static $instance = null;

    /**
     * Get the singleton instance
     *
     * @return Plugin
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor - private to enforce singleton
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // REST API endpoints will be registered in Phase 2
        add_action('rest_api_init', [$this, 'register_rest_routes']);
    }

    /**
     * Register REST API routes
     * Will be fully implemented in Phase 2
     */
    public function register_rest_routes() {
        // REST routes will be added in Phase 2
    }

    /**
     * Get plugin settings
     *
     * @return array
     */
    public function get_settings() {
        $defaults = [
            'api_provider' => 'openai',
            'api_key' => '',
            'rate_limit' => 100,
            'cache_enabled' => true,
            'cache_duration' => 3600,
            'primary_color' => '#0073aa',
            'default_alignment' => 'center',
        ];

        $settings = get_option('genblocks_settings', $defaults);
        return wp_parse_args($settings, $defaults);
    }

    /**
     * Update plugin settings
     *
     * @param array $new_settings Settings to update.
     * @return bool
     */
    public function update_settings($new_settings) {
        $current = $this->get_settings();
        $updated = array_merge($current, $new_settings);
        return update_option('genblocks_settings', $updated);
    }
}
