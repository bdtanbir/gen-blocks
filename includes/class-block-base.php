<?php
/**
 * Abstract Base Block Class
 *
 * @package GenBlocks
 */

namespace GenBlocks;

defined('ABSPATH') || exit;

/**
 * Abstract base class for all GenBlocks Gutenberg blocks.
 * Extend this class to create new blocks with consistent structure.
 */
abstract class Block_Base {

    /**
     * Block namespace
     *
     * @var string
     */
    protected $namespace = 'genblocks';

    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', [$this, 'register']);
    }

    /**
     * Get the block name (without namespace)
     *
     * @return string Block name (e.g., 'simple-card')
     */
    abstract protected function get_name();

    /**
     * Get block attributes
     *
     * @return array Block attributes schema
     */
    abstract protected function get_attributes();

    /**
     * Render the block on the frontend
     *
     * @param array    $attributes Block attributes.
     * @param string   $content    Block inner content.
     * @param WP_Block $block      Block instance.
     * @return string Rendered HTML
     */
    abstract public function render($attributes, $content, $block);

    /**
     * Get the full block name with namespace
     *
     * @return string Full block name (e.g., 'genblocks/simple-card')
     */
    public function get_full_name() {
        return $this->namespace . '/' . $this->get_name();
    }

    /**
     * Get the block title
     *
     * @return string Block title for display
     */
    protected function get_title() {
        return ucwords(str_replace('-', ' ', $this->get_name()));
    }

    /**
     * Get the block description
     *
     * @return string Block description
     */
    protected function get_description() {
        return '';
    }

    /**
     * Get the block category
     *
     * @return string Block category slug
     */
    protected function get_category() {
        return 'genblocks';
    }

    /**
     * Get the block icon
     *
     * @return string Block icon (dashicon name or SVG)
     */
    protected function get_icon() {
        return 'block-default';
    }

    /**
     * Get block supports configuration
     *
     * @return array Block supports
     */
    protected function get_supports() {
        return [
            'html'  => false,
            'align' => true,
        ];
    }

    /**
     * Get block keywords for search
     *
     * @return array Keywords
     */
    protected function get_keywords() {
        return ['genblocks'];
    }

    /**
     * Get example data for block preview
     *
     * @return array|null Example attributes
     */
    protected function get_example() {
        return null;
    }

    /**
     * Check if block uses server-side rendering
     *
     * @return bool
     */
    protected function uses_server_render() {
        return true;
    }

    /**
     * Get the path to block.json file
     *
     * @return string|null Path to block.json or null if not using metadata
     */
    protected function get_block_json_path() {
        $path = GENBLOCKS_PLUGIN_DIR . 'blocks/src/' . $this->get_name() . '/block.json';
        return file_exists($path) ? $path : null;
    }

    /**
     * Register the block
     */
    public function register() {
        $block_json_path = $this->get_block_json_path();

        // If block.json exists, use it for registration
        if ($block_json_path) {
            register_block_type(
                dirname($block_json_path),
                [
                    'render_callback' => $this->uses_server_render() ? [$this, 'render'] : null,
                ]
            );
            return;
        }

        // Fallback to manual registration
        $args = [
            'attributes'      => $this->get_attributes(),
            'category'        => $this->get_category(),
            'icon'            => $this->get_icon(),
            'keywords'        => $this->get_keywords(),
            'supports'        => $this->get_supports(),
        ];

        if ($this->uses_server_render()) {
            $args['render_callback'] = [$this, 'render'];
        }

        $example = $this->get_example();
        if ($example) {
            $args['example'] = ['attributes' => $example];
        }

        register_block_type($this->get_full_name(), $args);
    }

    /**
     * Get CSS class for the block wrapper
     *
     * @param array $attributes Block attributes.
     * @return string CSS classes
     */
    protected function get_wrapper_class($attributes = []) {
        $classes = ['wp-block-' . $this->namespace . '-' . $this->get_name()];

        if (!empty($attributes['className'])) {
            $classes[] = $attributes['className'];
        }

        if (!empty($attributes['align'])) {
            $classes[] = 'align' . $attributes['align'];
        }

        return implode(' ', $classes);
    }

    /**
     * Get wrapper attributes as HTML string
     *
     * @param array $attributes Block attributes.
     * @param array $extra_attrs Additional HTML attributes.
     * @return string HTML attributes string
     */
    protected function get_wrapper_attributes($attributes = [], $extra_attrs = []) {
        $wrapper_attributes = get_block_wrapper_attributes([
            'class' => $this->get_wrapper_class($attributes),
        ]);

        // Add any extra attributes
        foreach ($extra_attrs as $key => $value) {
            $wrapper_attributes .= ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
        }

        return $wrapper_attributes;
    }

    /**
     * Merge default attributes with provided attributes
     *
     * @param array $attributes Provided attributes.
     * @return array Merged attributes with defaults
     */
    protected function parse_attributes($attributes) {
        $defaults = [];
        foreach ($this->get_attributes() as $key => $config) {
            $defaults[$key] = $config['default'] ?? null;
        }
        return wp_parse_args($attributes, $defaults);
    }

    /**
     * Sanitize text attribute
     *
     * @param string $value The value to sanitize.
     * @return string Sanitized value
     */
    protected function sanitize_text($value) {
        return sanitize_text_field($value);
    }

    /**
     * Sanitize HTML attribute (allows safe HTML)
     *
     * @param string $value The value to sanitize.
     * @return string Sanitized value
     */
    protected function sanitize_html($value) {
        return wp_kses_post($value);
    }
}
