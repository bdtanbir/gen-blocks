<?php
/**
 * Hero Block
 *
 * @package GenBlocks
 */

namespace GenBlocks\Blocks;

use GenBlocks\Block_Base;

defined('ABSPATH') || exit;

/**
 * Hero Block - Hero section with heading, subtitle, and buttons
 */
class Block_Hero extends Block_Base {

    /**
     * Get the block name
     *
     * @return string
     */
    protected function get_name() {
        return 'hero';
    }

    /**
     * Get the block title
     *
     * @return string
     */
    protected function get_title() {
        return __('Hero Block', 'gen-blocks');
    }

    /**
     * Get the block description
     *
     * @return string
     */
    protected function get_description() {
        return __('A hero section with heading, subtitle, and buttons.', 'gen-blocks');
    }

    /**
     * Get the block icon
     *
     * @return string
     */
    protected function get_icon() {
        return 'cover-image';
    }

    /**
     * Get block keywords
     *
     * @return array
     */
    protected function get_keywords() {
        return ['hero', 'banner', 'header', 'genblocks', 'landing'];
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
            'heading' => [
                'type'    => 'string',
                'default' => 'Welcome to Our Website',
            ],
            'subtitle' => [
                'type'    => 'string',
                'default' => 'Discover amazing features and take your experience to the next level.',
            ],
            'primaryButtonText' => [
                'type'    => 'string',
                'default' => 'Get Started',
            ],
            'primaryButtonUrl' => [
                'type'    => 'string',
                'default' => '#',
            ],
            'secondaryButtonText' => [
                'type'    => 'string',
                'default' => 'Learn More',
            ],
            'secondaryButtonUrl' => [
                'type'    => 'string',
                'default' => '#',
            ],
            'showSecondaryButton' => [
                'type'    => 'boolean',
                'default' => true,
            ],
            'backgroundColor' => [
                'type'    => 'string',
                'default' => '#1e1e1e',
            ],
            'textColor' => [
                'type'    => 'string',
                'default' => '#ffffff',
            ],
            'primaryButtonColor' => [
                'type'    => 'string',
                'default' => '#0073aa',
            ],
            'primaryButtonTextColor' => [
                'type'    => 'string',
                'default' => '#ffffff',
            ],
            'secondaryButtonColor' => [
                'type'    => 'string',
                'default' => 'transparent',
            ],
            'secondaryButtonTextColor' => [
                'type'    => 'string',
                'default' => '#ffffff',
            ],
            'textAlign' => [
                'type'    => 'string',
                'default' => 'center',
            ],
            'minHeight' => [
                'type'    => 'number',
                'default' => 500,
            ],
            'overlayOpacity' => [
                'type'    => 'number',
                'default' => 0,
            ],
            'backgroundImage' => [
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
            'heading'             => __('Welcome to Our Website', 'gen-blocks'),
            'subtitle'            => __('Discover amazing features and take your experience to the next level.', 'gen-blocks'),
            'primaryButtonText'   => __('Get Started', 'gen-blocks'),
            'secondaryButtonText' => __('Learn More', 'gen-blocks'),
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

        $heading                   = $this->sanitize_html($attributes['heading']);
        $subtitle                  = $this->sanitize_html($attributes['subtitle']);
        $primary_button_text       = $this->sanitize_text($attributes['primaryButtonText']);
        $primary_button_url        = esc_url($attributes['primaryButtonUrl']);
        $secondary_button_text     = $this->sanitize_text($attributes['secondaryButtonText']);
        $secondary_button_url      = esc_url($attributes['secondaryButtonUrl']);
        $show_secondary_button     = (bool) $attributes['showSecondaryButton'];
        $background_color          = sanitize_hex_color($attributes['backgroundColor']) ?: '#1e1e1e';
        $text_color                = sanitize_hex_color($attributes['textColor']) ?: '#ffffff';
        $primary_button_color      = sanitize_hex_color($attributes['primaryButtonColor']) ?: '#0073aa';
        $primary_button_text_color = sanitize_hex_color($attributes['primaryButtonTextColor']) ?: '#ffffff';
        $secondary_button_color    = $attributes['secondaryButtonColor'] === 'transparent' ? 'transparent' : (sanitize_hex_color($attributes['secondaryButtonColor']) ?: 'transparent');
        $secondary_button_text_color = sanitize_hex_color($attributes['secondaryButtonTextColor']) ?: '#ffffff';
        $text_align                = in_array($attributes['textAlign'], ['left', 'center', 'right']) ? $attributes['textAlign'] : 'center';
        $min_height                = absint($attributes['minHeight']);
        $overlay_opacity           = absint($attributes['overlayOpacity']);
        $background_image          = esc_url($attributes['backgroundImage']);

        // Build wrapper styles
        $wrapper_styles = [
            sprintf('background-color: %s', $background_color),
            sprintf('color: %s', $text_color),
            sprintf('min-height: %dpx', $min_height),
            sprintf('text-align: %s', $text_align),
            'position: relative',
            'display: flex',
            'align-items: center',
            'justify-content: center',
        ];

        if (!empty($background_image)) {
            $wrapper_styles[] = sprintf('background-image: url(%s)', $background_image);
            $wrapper_styles[] = 'background-size: cover';
            $wrapper_styles[] = 'background-position: center';
        }

        $wrapper_style = implode('; ', $wrapper_styles);

        $primary_button_style = sprintf(
            'background-color: %s; color: %s;',
            $primary_button_color,
            $primary_button_text_color
        );

        $secondary_button_style = sprintf(
            'background-color: %s; color: %s; border: 2px solid %s;',
            $secondary_button_color,
            $secondary_button_text_color,
            $secondary_button_text_color
        );

        $wrapper_attributes = $this->get_wrapper_attributes($attributes);

        ob_start();
        ?>
        <div <?php echo $wrapper_attributes; ?> style="<?php echo esc_attr($wrapper_style); ?>">
            <?php if (!empty($background_image) && $overlay_opacity > 0) : ?>
                <div class="wp-block-genblocks-hero__overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, <?php echo esc_attr($overlay_opacity / 100); ?>); pointer-events: none;"></div>
            <?php endif; ?>

            <div class="wp-block-genblocks-hero__content" style="position: relative; z-index: 1; padding: 40px 20px; max-width: 800px;">
                <?php if (!empty($heading)) : ?>
                    <h1 class="wp-block-genblocks-hero__heading" style="color: <?php echo esc_attr($text_color); ?>">
                        <?php echo $heading; ?>
                    </h1>
                <?php endif; ?>

                <?php if (!empty($subtitle)) : ?>
                    <p class="wp-block-genblocks-hero__subtitle" style="color: <?php echo esc_attr($text_color); ?>">
                        <?php echo $subtitle; ?>
                    </p>
                <?php endif; ?>

                <div class="wp-block-genblocks-hero__buttons">
                    <?php if (!empty($primary_button_text)) : ?>
                        <a href="<?php echo $primary_button_url; ?>" class="wp-block-genblocks-hero__button wp-block-genblocks-hero__button--primary" style="<?php echo esc_attr($primary_button_style); ?>">
                            <?php echo esc_html($primary_button_text); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($show_secondary_button && !empty($secondary_button_text)) : ?>
                        <a href="<?php echo $secondary_button_url; ?>" class="wp-block-genblocks-hero__button wp-block-genblocks-hero__button--secondary" style="<?php echo esc_attr($secondary_button_style); ?>">
                            <?php echo esc_html($secondary_button_text); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
