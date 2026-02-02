<?php
/**
 * Plugin Name: GenBlocks
 * Description: Generate beautiful Gutenberg blocks instantly using AI. Turn simple text prompts into fully customizable WordPress blocks.
 * Version: 1.0.0
 * Author: bdtanbir
 * Author URI: https://github.com/bdtanbir
 * Plugin URI: https://github.com/bdtanbir/genblocks
 * License: GPLv2 or later
 * Text Domain: get-blocks
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

defined('ABSPATH') || exit;

// Plugin Constants
define('GENBLOCKS_VERSION', '1.0.0');
define('GENBLOCKS_PLUGIN_FILE', __FILE__);
define('GENBLOCKS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GENBLOCKS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('GENBLOCKS_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'GenBlocks\\';
    $base_dir = GENBLOCKS_PLUGIN_DIR . 'includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . 'class-' . strtolower(str_replace(['_', '\\'], ['-', '/class-'], $relative_class)) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

/**
 * Plugin activation hook
 */
function genblocks_activate() {
    // Check PHP version
    if (version_compare(PHP_VERSION, '7.4', '<')) {
        deactivate_plugins(GENBLOCKS_PLUGIN_BASENAME);
        wp_die(
            esc_html__('GenBlocks requires PHP 7.4 or higher.', 'get-blocks'),
            'Plugin Activation Error',
            ['back_link' => true]
        );
    }

    // Check WordPress version
    if (version_compare(get_bloginfo('version'), '6.0', '<')) {
        deactivate_plugins(GENBLOCKS_PLUGIN_BASENAME);
        wp_die(
            esc_html__('GenBlocks requires WordPress 6.0 or higher.', 'get-blocks'),
            'Plugin Activation Error',
            ['back_link' => true]
        );
    }

    // Set default options
    $default_options = [
        'api_provider' => 'openai',
        'api_key' => '',
        'rate_limit' => 100,
        'cache_enabled' => true,
        'cache_duration' => 3600,
        'primary_color' => '#0073aa',
        'default_alignment' => 'center',
    ];

    if (!get_option('genblocks_settings')) {
        add_option('genblocks_settings', $default_options);
    }

    // Create usage tracking table
    global $wpdb;
    $table_name = $wpdb->prefix . 'genblocks_usage';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        prompt text NOT NULL,
        block_type varchar(100) DEFAULT '',
        tokens_used int(11) NOT NULL DEFAULT 0,
        cost decimal(10,6) NOT NULL DEFAULT 0,
        status varchar(20) NOT NULL DEFAULT 'success',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY user_id (user_id),
        KEY created_at (created_at),
        KEY status (status)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);

    // Store plugin version
    update_option('genblocks_version', GENBLOCKS_VERSION);

    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'genblocks_activate');

/**
 * Plugin deactivation hook
 */
function genblocks_deactivate() {
    // Clear any scheduled events
    wp_clear_scheduled_hook('genblocks_cleanup_cache');

    // Flush rewrite rules
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'genblocks_deactivate');

/**
 * Load text domain for translations
 */
function genblocks_load_textdomain() {
    load_plugin_textdomain('get-blocks', false, dirname(GENBLOCKS_PLUGIN_BASENAME) . '/languages');
}
add_action('plugins_loaded', 'genblocks_load_textdomain');

/**
 * Initialize the plugin
 */
function genblocks_init() {
    // Initialize main plugin class
    if (class_exists('GenBlocks\\Plugin')) {
        GenBlocks\Plugin::get_instance();
    }
}
add_action('plugins_loaded', 'genblocks_init', 20);

/**
 * Enqueue block editor assets
 */
function genblocks_enqueue_block_editor_assets() {
    $asset_file = GENBLOCKS_PLUGIN_DIR . 'blocks/build/index.asset.php';

    if (!file_exists($asset_file)) {
        return;
    }

    $asset = include $asset_file;

    wp_enqueue_script(
        'genblocks-editor',
        GENBLOCKS_PLUGIN_URL . 'blocks/build/index.js',
        $asset['dependencies'],
        $asset['version'],
        true
    );

    wp_localize_script('genblocks-editor', 'genBlocksData', [
        'apiUrl' => rest_url('genblocks/v1/'),
        'nonce' => wp_create_nonce('wp_rest'),
        'pluginUrl' => GENBLOCKS_PLUGIN_URL,
    ]);

    wp_enqueue_style(
        'genblocks-editor-style',
        GENBLOCKS_PLUGIN_URL . 'blocks/build/index.css',
        [],
        $asset['version']
    );
}
add_action('enqueue_block_editor_assets', 'genblocks_enqueue_block_editor_assets');

/**
 * Enqueue block assets (frontend + editor)
 */
function genblocks_enqueue_block_assets() {
    $style_file = GENBLOCKS_PLUGIN_DIR . 'blocks/build/style-index.css';

    if (!file_exists($style_file)) {
        return;
    }

    wp_enqueue_style(
        'genblocks-blocks',
        GENBLOCKS_PLUGIN_URL . 'blocks/build/style-index.css',
        [],
        filemtime($style_file)
    );
}
add_action('enqueue_block_assets', 'genblocks_enqueue_block_assets');

/**
 * Enqueue admin assets
 */
function genblocks_enqueue_admin_assets($hook) {
    if ('toplevel_page_genblocks' !== $hook) {
        return;
    }

    $asset_file = GENBLOCKS_PLUGIN_DIR . 'admin/dist/assets/index.php';
    $js_file = GENBLOCKS_PLUGIN_DIR . 'admin/dist/assets/index.js';
    $css_file = GENBLOCKS_PLUGIN_DIR . 'admin/dist/assets/index.css';
    $css_url = GENBLOCKS_PLUGIN_URL . 'admin/dist/assets/index.css';
    if (!file_exists($css_file)) {
        $css_file = GENBLOCKS_PLUGIN_DIR . 'admin/dist/assets/main.css';
        $css_url = GENBLOCKS_PLUGIN_URL . 'admin/dist/assets/main.css';
    }

    $version = GENBLOCKS_VERSION;
    $dependencies = [];

    if (file_exists($asset_file)) {
        $asset = include $asset_file;
        $version = $asset['version'] ?? GENBLOCKS_VERSION;
        $dependencies = $asset['dependencies'] ?? [];
    }

    if (file_exists($js_file)) {
        wp_enqueue_script(
            'genblocks-admin',
            GENBLOCKS_PLUGIN_URL . 'admin/dist/assets/index.js',
            $dependencies,
            $version,
            true
        );

        wp_localize_script('genblocks-admin', 'genBlocksAdmin', [
            'apiUrl' => rest_url('genblocks/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'pluginUrl' => GENBLOCKS_PLUGIN_URL,
            'version' => GENBLOCKS_VERSION,
        ]);
    }

    if (file_exists($css_file)) {
        wp_enqueue_style(
            'genblocks-admin-style',
            $css_url,
            ['wp-components'],
            $version
        );
    }

    // WordPress components style
    wp_enqueue_style('wp-components');
}
add_action('admin_enqueue_scripts', 'genblocks_enqueue_admin_assets');

/**
 * Register admin menu
 */
function genblocks_admin_menu() {
    add_menu_page(
        __('GenBlocks', 'get-blocks'),
        __('GenBlocks', 'get-blocks'),
        'manage_options',
        'genblocks',
        'genblocks_render_admin_page',
        'dashicons-lightbulb',
        30
    );
}
add_action('admin_menu', 'genblocks_admin_menu');

/**
 * Render admin page
 */
function genblocks_render_admin_page() {
    echo '<div id="genblocks-admin-app"></div>';
}

/**
 * Register custom block category
 */
function genblocks_register_block_category($categories) {
    return array_merge(
        [
            [
                'slug' => 'genblocks',
                'title' => __('GenBlocks - AI Generated', 'get-blocks'),
                'icon' => 'lightbulb',
            ],
        ],
        $categories
    );
}
add_filter('block_categories_all', 'genblocks_register_block_category', 10, 1);

/**
 * Add plugin action links
 */
function genblocks_action_links($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=genblocks') . '">' . __('Settings', 'get-blocks') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . GENBLOCKS_PLUGIN_BASENAME, 'genblocks_action_links');
