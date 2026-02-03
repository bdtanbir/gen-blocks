<?php
/**
 * Simple Card Block
 *
 * @package GenBlocks
 */

namespace GenBlocks\Blocks;

use GenBlocks\Block_Base;

defined('ABSPATH') || exit;

/**
 * Simple Card Block - Displays a title and description
 */
class Block_Simple_Card extends Block_Base {

    /**
     * Get the block name
     *
     * @return string
     */
    protected function get_name() {
        return 'simple-card';
    }

    /**
     * Get the block title
     *
     * @return string
     */
    protected function get_title() {
        return __('Simple Card', 'gen-blocks');
    }

    /**
     * Get the block description
     *
     * @return string
     */
    protected function get_description() {
        return __('A simple card with title and description.', 'gen-blocks');
    }

    /**
     * Get the block icon
     *
     * @return string
     */
    protected function get_icon() {
        return 'text-page';
    }

    /**
     * Get block keywords
     *
     * @return array
     */
    protected function get_keywords() {
        return ['card', 'title', 'description', 'genblocks', 'simple'];
    }

    /**
     * Get block supports
     *
     * @return array
     */
    protected function get_supports() {
        return [
            'html'       => false,
            'align'      => ['wide', 'full'],
            'anchor'     => true,
            'color'      => [
                'background' => true,
                'text'       => true,
            ],
            'spacing'    => [
                'margin'  => true,
                'padding' => true,
            ],
            'typography' => [
                'fontSize' => true,
            ],
        ];
    }

    /**
     * Get block attributes
     *
     * @return array
     */
    protected function get_attributes() {
        return [
            'title' => [
                'type'    => 'string',
                'default' => '',
            ],
            'description' => [
                'type'    => 'string',
                'default' => '',
            ],
        ];
    }

    /**
     * Get example attributes for preview
     *
     * @return array
     */
    protected function get_example() {
        return [
            'title'       => __('Welcome to GenBlocks', 'gen-blocks'),
            'description' => __('This is a simple card block with a title and description. You can customize the content in the editor.', 'gen-blocks'),
        ];
    }

    /**
     * Render the block
     *
     * @param array    $attributes Block attributes.
     * @param string   $content    Block inner content.
     * @param WP_Block $block      Block instance.
     * @return string
     */
    public function render($attributes, $content, $block) {
        $attributes = $this->parse_attributes($attributes);

        $title       = $this->sanitize_html($attributes['title']);
        $description = $this->sanitize_html($attributes['description']);

        // Don't render if both fields are empty
        if (empty($title) && empty($description)) {
            return '';
        }

        $wrapper_attributes = $this->get_wrapper_attributes($attributes);

        ob_start();
        ?>
        <div <?php echo $wrapper_attributes; ?>>
            <?php if (!empty($title)) : ?>
                <h3 class="wp-block-genblocks-simple-card__title">
                    <?php echo $title; ?>
                </h3>
            <?php endif; ?>
            <?php if (!empty($description)) : ?>
                <p class="wp-block-genblocks-simple-card__description">
                    <?php echo $description; ?>
                </p>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
