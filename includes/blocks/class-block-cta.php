<?php
/**
 * CTA Block
 *
 * @package GenBlocks
 */

namespace GenBlocks\Blocks;

use GenBlocks\Block_Base;

defined('ABSPATH') || exit;

/**
 * CTA Block - Call to Action section
 */
class Block_CTA extends Block_Base {

    /**
     * Get the block name
     *
     * @return string
     */
    protected function get_name() {
        return 'cta';
    }

    /**
     * Get the block title
     *
     * @return string
     */
    protected function get_title() {
        return __('CTA Block', 'gen-blocks');
    }

    /**
     * Get the block description
     *
     * @return string
     */
    protected function get_description() {
        return __('A call-to-action block with title, description, and button.', 'gen-blocks');
    }

    /**
     * Get the block icon
     *
     * @return string
     */
    protected function get_icon() {
        return 'megaphone';
    }

    /**
     * Get block keywords
     *
     * @return array
     */
    protected function get_keywords() {
        return ['cta', 'call to action', 'button', 'genblocks', 'conversion'];
    }

    /**
     * Get block supports
     *
     * @return array
     */
    protected function get_supports() {
        return [
            'html'    => false,
            'align'   => ['wide', 'full'],
            'anchor'  => true,
            'spacing' => [
                'margin' => true,
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
                'default' => 'Ready to Get Started?',
            ],
            'description' => [
                'type'    => 'string',
                'default' => 'Join thousands of satisfied customers and take your business to the next level.',
            ],
            'buttonText' => [
                'type'    => 'string',
                'default' => 'Get Started',
            ],
            'buttonUrl' => [
                'type'    => 'string',
                'default' => '#',
            ],
            'backgroundColor' => [
                'type'    => 'string',
                'default' => '#0073aa',
            ],
            'textColor' => [
                'type'    => 'string',
                'default' => '#ffffff',
            ],
            'buttonColor' => [
                'type'    => 'string',
                'default' => '#ffffff',
            ],
            'buttonTextColor' => [
                'type'    => 'string',
                'default' => '#0073aa',
            ],
            'textAlign' => [
                'type'    => 'string',
                'default' => 'center',
            ],
            'padding' => [
                'type'    => 'number',
                'default' => 60,
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
            'title'           => __('Ready to Get Started?', 'gen-blocks'),
            'description'     => __('Join thousands of satisfied customers today.', 'gen-blocks'),
            'buttonText'      => __('Get Started', 'gen-blocks'),
            'backgroundColor' => '#0073aa',
            'textColor'       => '#ffffff',
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

        $title            = $this->sanitize_html($attributes['title']);
        $description      = $this->sanitize_html($attributes['description']);
        $button_text      = $this->sanitize_text($attributes['buttonText']);
        $button_url       = esc_url($attributes['buttonUrl']);
        $background_color = sanitize_hex_color($attributes['backgroundColor']) ?: '#0073aa';
        $text_color       = sanitize_hex_color($attributes['textColor']) ?: '#ffffff';
        $button_color     = sanitize_hex_color($attributes['buttonColor']) ?: '#ffffff';
        $button_text_color = sanitize_hex_color($attributes['buttonTextColor']) ?: '#0073aa';
        $text_align       = in_array($attributes['textAlign'], ['left', 'center', 'right']) ? $attributes['textAlign'] : 'center';
        $padding          = absint($attributes['padding']);

        $wrapper_style = sprintf(
            'background-color: %s; color: %s; padding: %dpx; text-align: %s;',
            $background_color,
            $text_color,
            $padding,
            $text_align
        );

        $button_style = sprintf(
            'background-color: %s; color: %s;',
            $button_color,
            $button_text_color
        );

        $wrapper_attributes = $this->get_wrapper_attributes($attributes);

        ob_start();
        ?>
        <div <?php echo $wrapper_attributes; ?> style="<?php echo esc_attr($wrapper_style); ?>">
            <?php if (!empty($title)) : ?>
                <h2 class="wp-block-genblocks-cta__title" style="color: <?php echo esc_attr($text_color); ?>">
                    <?php echo $title; ?>
                </h2>
            <?php endif; ?>

            <?php if (!empty($description)) : ?>
                <p class="wp-block-genblocks-cta__description" style="color: <?php echo esc_attr($text_color); ?>">
                    <?php echo $description; ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($button_text)) : ?>
                <div class="wp-block-genblocks-cta__button-wrapper">
                    <a href="<?php echo $button_url; ?>" class="wp-block-genblocks-cta__button" style="<?php echo esc_attr($button_style); ?>">
                        <?php echo esc_html($button_text); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
