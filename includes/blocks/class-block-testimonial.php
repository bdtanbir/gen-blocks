<?php
/**
 * Testimonial Block
 *
 * @package GenBlocks
 */

namespace GenBlocks\Blocks;

use GenBlocks\Block_Base;

defined('ABSPATH') || exit;

/**
 * Testimonial Block - Testimonial with quote, author, and optional avatar
 */
class Block_Testimonial extends Block_Base {

    /**
     * Get the block name
     *
     * @return string
     */
    protected function get_name() {
        return 'testimonial';
    }

    /**
     * Get the block title
     *
     * @return string
     */
    protected function get_title() {
        return __('Testimonial Block', 'gen-blocks');
    }

    /**
     * Get the block description
     *
     * @return string
     */
    protected function get_description() {
        return __('Display a testimonial with quote, author, and optional avatar.', 'gen-blocks');
    }

    /**
     * Get the block icon
     *
     * @return string
     */
    protected function get_icon() {
        return 'format-quote';
    }

    /**
     * Get block keywords
     *
     * @return array
     */
    protected function get_keywords() {
        return ['testimonial', 'quote', 'review', 'genblocks', 'feedback'];
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
                'margin'  => true,
                'padding' => true,
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
            'quote' => [
                'type'    => 'string',
                'default' => 'This product has completely transformed our workflow. The team is incredibly responsive and the features are exactly what we needed.',
            ],
            'authorName' => [
                'type'    => 'string',
                'default' => 'Jane Smith',
            ],
            'authorRole' => [
                'type'    => 'string',
                'default' => 'CEO, Company Name',
            ],
            'authorImage' => [
                'type'    => 'string',
                'default' => '',
            ],
            'authorImageId' => [
                'type'    => 'number',
                'default' => 0,
            ],
            'backgroundColor' => [
                'type'    => 'string',
                'default' => '#f8f9fa',
            ],
            'quoteColor' => [
                'type'    => 'string',
                'default' => '#1e1e1e',
            ],
            'authorNameColor' => [
                'type'    => 'string',
                'default' => '#1e1e1e',
            ],
            'authorRoleColor' => [
                'type'    => 'string',
                'default' => '#666666',
            ],
            'quoteIconColor' => [
                'type'    => 'string',
                'default' => '#0073aa',
            ],
            'borderRadius' => [
                'type'    => 'number',
                'default' => 8,
            ],
            'showQuoteIcon' => [
                'type'    => 'boolean',
                'default' => true,
            ],
            'textAlign' => [
                'type'    => 'string',
                'default' => 'center',
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
            'quote'      => __('This product has completely transformed our workflow. The team is incredibly responsive and the features are exactly what we needed.', 'gen-blocks'),
            'authorName' => __('Jane Smith', 'gen-blocks'),
            'authorRole' => __('CEO, Company Name', 'gen-blocks'),
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

        $quote             = $this->sanitize_html($attributes['quote']);
        $author_name       = $this->sanitize_text($attributes['authorName']);
        $author_role       = $this->sanitize_text($attributes['authorRole']);
        $author_image      = esc_url($attributes['authorImage']);
        $background_color  = sanitize_hex_color($attributes['backgroundColor']) ?: '#f8f9fa';
        $quote_color       = sanitize_hex_color($attributes['quoteColor']) ?: '#1e1e1e';
        $author_name_color = sanitize_hex_color($attributes['authorNameColor']) ?: '#1e1e1e';
        $author_role_color = sanitize_hex_color($attributes['authorRoleColor']) ?: '#666666';
        $quote_icon_color  = sanitize_hex_color($attributes['quoteIconColor']) ?: '#0073aa';
        $border_radius     = absint($attributes['borderRadius']);
        $show_quote_icon   = (bool) $attributes['showQuoteIcon'];
        $text_align        = in_array($attributes['textAlign'], ['left', 'center', 'right']) ? $attributes['textAlign'] : 'center';

        $wrapper_style = sprintf(
            'background-color: %s; border-radius: %dpx; text-align: %s;',
            $background_color,
            $border_radius,
            $text_align
        );

        $justify_content = 'center';
        if ($text_align === 'left') {
            $justify_content = 'flex-start';
        } elseif ($text_align === 'right') {
            $justify_content = 'flex-end';
        }

        $wrapper_attributes = $this->get_wrapper_attributes($attributes);

        ob_start();
        ?>
        <div <?php echo $wrapper_attributes; ?> style="<?php echo esc_attr($wrapper_style); ?>">
            <div class="wp-block-genblocks-testimonial__content" style="padding: 40px 30px; max-width: 800px; margin: 0 auto;">
                <?php if ($show_quote_icon) : ?>
                    <div class="wp-block-genblocks-testimonial__quote-icon" style="margin-bottom: 20px;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="<?php echo esc_attr($quote_icon_color); ?>" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z"/>
                        </svg>
                    </div>
                <?php endif; ?>

                <?php if (!empty($quote)) : ?>
                    <blockquote class="wp-block-genblocks-testimonial__quote" style="color: <?php echo esc_attr($quote_color); ?>; font-size: 1.25rem; line-height: 1.7; font-style: italic; margin: 0 0 24px 0; border: none; padding: 0;">
                        <?php echo $quote; ?>
                    </blockquote>
                <?php endif; ?>

                <div class="wp-block-genblocks-testimonial__author" style="display: flex; align-items: center; justify-content: <?php echo esc_attr($justify_content); ?>; gap: 16px;">
                    <?php if (!empty($author_image)) : ?>
                        <img
                            src="<?php echo $author_image; ?>"
                            alt="<?php echo esc_attr($author_name); ?>"
                            class="wp-block-genblocks-testimonial__avatar"
                            style="width: 56px; height: 56px; border-radius: 50%; object-fit: cover;"
                        />
                    <?php endif; ?>

                    <div class="wp-block-genblocks-testimonial__author-info">
                        <?php if (!empty($author_name)) : ?>
                            <cite class="wp-block-genblocks-testimonial__author-name" style="color: <?php echo esc_attr($author_name_color); ?>; font-weight: 600; font-style: normal; display: block;">
                                <?php echo esc_html($author_name); ?>
                            </cite>
                        <?php endif; ?>

                        <?php if (!empty($author_role)) : ?>
                            <span class="wp-block-genblocks-testimonial__author-role" style="color: <?php echo esc_attr($author_role_color); ?>; font-size: 0.875rem; display: block; margin-top: 4px;">
                                <?php echo esc_html($author_role); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
