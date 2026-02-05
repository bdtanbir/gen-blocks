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
     * Settings instance
     *
     * @var Settings|null
     */
    private $settings = null;

    /**
     * AI Engine instance
     *
     * @var AI_Engine|null
     */
    private $ai_engine = null;

    /**
     * REST API instance
     *
     * @var REST_API|null
     */
    private $rest_api = null;

    /**
     * Usage Tracker instance
     *
     * @var Usage_Tracker|null
     */
    private $usage_tracker = null;

    /**
     * Block Generator instance
     *
     * @var Block_Generator|null
     */
    private $block_generator = null;

    /**
     * Prompt Templates instance
     *
     * @var Prompt_Templates|null
     */
    private $prompt_templates = null;

    /**
     * Response Parser instance
     *
     * @var Response_Parser|null
     */
    private $response_parser = null;

    /**
     * Template Library instance
     *
     * @var Template_Library|null
     */
    private $template_library = null;

    /**
     * Registered blocks
     *
     * @var array
     */
    private $blocks = [];

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
        $this->load_dependencies();
        $this->init_components();
        $this->init_blocks();
        $this->init_hooks();
    }

    /**
     * Load required class files
     */
    private function load_dependencies() {
        $includes_dir = GENBLOCKS_PLUGIN_DIR . 'includes/';

        require_once $includes_dir . 'class-settings.php';
        require_once $includes_dir . 'class-encryption.php';
        require_once $includes_dir . 'class-prompt-templates.php';
        require_once $includes_dir . 'class-response-parser.php';
        require_once $includes_dir . 'class-template-library.php';
        require_once $includes_dir . 'class-ai-engine.php';
        require_once $includes_dir . 'class-block-generator.php';
        require_once $includes_dir . 'class-rest-api.php';
        require_once $includes_dir . 'class-usage-tracker.php';

        // Block base class and blocks
        require_once $includes_dir . 'class-block-base.php';
        require_once $includes_dir . 'blocks/class-block-simple-card.php';
        require_once $includes_dir . 'blocks/class-block-cta.php';
        require_once $includes_dir . 'blocks/class-block-hero.php';
        require_once $includes_dir . 'blocks/class-block-features.php';
        require_once $includes_dir . 'blocks/class-block-testimonial.php';
    }

    /**
     * Initialize plugin components
     */
    private function init_components() {
        $this->settings = new Settings();
        $this->usage_tracker = new Usage_Tracker();
        $this->prompt_templates = new Prompt_Templates();
        $this->response_parser = new Response_Parser();
        $this->template_library = new Template_Library();
        $this->ai_engine = new AI_Engine($this->settings, $this->prompt_templates, $this->response_parser);
        $this->block_generator = new Block_Generator();
        $this->rest_api = new REST_API(
            $this->ai_engine,
            $this->block_generator,
            $this->settings,
            $this->usage_tracker,
            $this->template_library
        );
    }

    /**
     * Initialize Gutenberg blocks
     */
    private function init_blocks() {
        $this->blocks['simple-card'] = new Blocks\Block_Simple_Card();
        $this->blocks['cta']         = new Blocks\Block_CTA();
        $this->blocks['hero']        = new Blocks\Block_Hero();
        $this->blocks['features']    = new Blocks\Block_Features();
        $this->blocks['testimonial'] = new Blocks\Block_Testimonial();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('rest_api_init', [$this->rest_api, 'register_routes']);
    }

    /**
     * Get settings instance
     *
     * @return Settings
     */
    public function get_settings() {
        return $this->settings;
    }

    /**
     * Get AI Engine instance
     *
     * @return AI_Engine
     */
    public function get_ai_engine() {
        return $this->ai_engine;
    }

    /**
     * Get Usage Tracker instance
     *
     * @return Usage_Tracker
     */
    public function get_usage_tracker() {
        return $this->usage_tracker;
    }

    /**
     * Get Block Generator instance
     *
     * @return Block_Generator
     */
    public function get_block_generator() {
        return $this->block_generator;
    }

    /**
     * Get Prompt Templates instance
     *
     * @return Prompt_Templates
     */
    public function get_prompt_templates() {
        return $this->prompt_templates;
    }

    /**
     * Get Response Parser instance
     *
     * @return Response_Parser
     */
    public function get_response_parser() {
        return $this->response_parser;
    }

    /**
     * Get Template Library instance
     *
     * @return Template_Library
     */
    public function get_template_library() {
        return $this->template_library;
    }

    /**
     * Get a registered block instance
     *
     * @param string $name Block name (without namespace).
     * @return Block_Base|null
     */
    public function get_block($name) {
        return $this->blocks[$name] ?? null;
    }

    /**
     * Get all registered blocks
     *
     * @return array
     */
    public function get_blocks() {
        return $this->blocks;
    }
}
