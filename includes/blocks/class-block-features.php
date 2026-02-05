<?php
/**
 * Features Block
 *
 * @package GenBlocks
 */

namespace GenBlocks\Blocks;

use GenBlocks\Block_Base;

defined('ABSPATH') || exit;

/**
 * Features Block - Grid of features with icons, titles, and descriptions
 */
class Block_Features extends Block_Base {

    /**
     * Get the block name
     *
     * @return string
     */
    protected function get_name() {
        return 'features';
    }

    /**
     * Get the block title
     *
     * @return string
     */
    protected function get_title() {
        return __('Features Block', 'gen-blocks');
    }

    /**
     * Get the block description
     *
     * @return string
     */
    protected function get_description() {
        return __('Display a grid of features with icons, titles, and descriptions.', 'gen-blocks');
    }

    /**
     * Get the block icon
     *
     * @return string
     */
    protected function get_icon() {
        return 'grid-view';
    }

    /**
     * Get block keywords
     *
     * @return array
     */
    protected function get_keywords() {
        return ['features', 'grid', 'services', 'genblocks', 'icons'];
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
            'columns' => [
                'type'    => 'number',
                'default' => 3,
            ],
            'features' => [
                'type'    => 'array',
                'default' => [
                    [
                        'icon'        => 'star-filled',
                        'title'       => 'Feature One',
                        'description' => 'Description of your first amazing feature.',
                    ],
                    [
                        'icon'        => 'lightbulb',
                        'title'       => 'Feature Two',
                        'description' => 'Description of your second amazing feature.',
                    ],
                    [
                        'icon'        => 'chart-line',
                        'title'       => 'Feature Three',
                        'description' => 'Description of your third amazing feature.',
                    ],
                ],
            ],
            'iconColor' => [
                'type'    => 'string',
                'default' => '#0073aa',
            ],
            'iconBackgroundColor' => [
                'type'    => 'string',
                'default' => '#f0f0f0',
            ],
            'titleColor' => [
                'type'    => 'string',
                'default' => '#1e1e1e',
            ],
            'descriptionColor' => [
                'type'    => 'string',
                'default' => '#666666',
            ],
            'backgroundColor' => [
                'type'    => 'string',
                'default' => '#ffffff',
            ],
            'iconSize' => [
                'type'    => 'number',
                'default' => 48,
            ],
            'gap' => [
                'type'    => 'number',
                'default' => 30,
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
            'columns'  => 3,
            'features' => [
                [
                    'icon'        => 'star-filled',
                    'title'       => __('Feature One', 'gen-blocks'),
                    'description' => __('Description of your first amazing feature.', 'gen-blocks'),
                ],
                [
                    'icon'        => 'lightbulb',
                    'title'       => __('Feature Two', 'gen-blocks'),
                    'description' => __('Description of your second amazing feature.', 'gen-blocks'),
                ],
                [
                    'icon'        => 'chart-line',
                    'title'       => __('Feature Three', 'gen-blocks'),
                    'description' => __('Description of your third amazing feature.', 'gen-blocks'),
                ],
            ],
        ];
    }

    /**
     * Get dashicon SVG path for rendering
     *
     * @param string $icon Icon name.
     * @return string SVG path data
     */
    private function get_dashicon_svg($icon) {
        $icons = [
            'star-filled'  => 'M10 1l3 6 6 .75-4.12 4.62L16 19l-6-3-6 3 1.13-6.63L1 7.75 7 7z',
            'lightbulb'    => 'M10 1c-3.87 0-7 3.13-7 7 0 2.38 1.19 4.47 3 5.74V17c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-3.26c1.81-1.27 3-3.36 3-5.74 0-3.87-3.13-7-7-7zM9 21c0 .55.45 1 1 1s1-.45 1-1h-2z',
            'chart-line'   => 'M18 3.5c0 .62-.38 1.16-.92 1.38l-2.22 3.32 2.12 5.3 1.02-.41v4.41h-2v-2.59l-.74.3-2.4-6 2.26-3.39c-.08-.13-.12-.28-.12-.44 0-.28.22-.5.5-.5s.5.22.5.5zm-8.14 7.43l2.14-.86 2.4 6.02L12 17l-2.14-5.07zM4 17l3-7 2 4-3 3H4z',
            'shield'       => 'M10 2s3 2 7 2c0 11-7 14-7 14S3 15 3 4c4 0 7-2 7-2z',
            'admin-users'  => 'M10 9.26l-2-1.54V5c0-2 4-2 4 0v2.72z M10 10c-3 0-7 2-7 4v2h14v-2c0-2-4-4-7-4z',
            'admin-site'   => 'M9 0C4.03 0 0 4.03 0 9s4.03 9 9 9 9-4.03 9-9-4.03-9-9-9zM1.11 9.68h2.51c.04.91.167 1.814.38 2.7H1.84c-.403-.86-.655-1.78-.73-2.7zm8.57-5.4V1.19c.964.366 1.756 1.08 2.22 2 .205.347.386.708.54 1.08l-2.76.01zm3.22 1.35c.232.883.37 1.788.41 2.7h-3.63V5.98l3.22.01-.01-.36zM8.32 1.19v3.09H5.56c.154-.372.335-.733.54-1.08.464-.92 1.256-1.634 2.22-2zm0 4.44v2.35h-3.63c.04-.912.178-1.817.41-2.7h3.22v.35zm-4.7 2.69H1.11c.075-.92.327-1.84.73-2.7H4c-.213.886-.34 1.79-.38 2.7zm0 1.35c.04.91.167 1.814.38 2.7H1.84c-.403-.86-.655-1.78-.73-2.7h2.51zm1.07 0h3.63v2.35c-1.2.27-2.38.75-3.22 1.35-.232-.88-.37-1.78-.41-2.7v-1zm3.63 3.7v3.09c-.964-.366-1.756-1.08-2.22-2-.205-.347-.386-.708-.54-1.08l2.76-.01zm1.36 3.09v-3.09h2.76c-.154.372-.335.733-.54 1.08-.464.92-1.256 1.63-2.22 2zm0-4.44v-2.35h3.63c-.04.912-.178 1.82-.41 2.7H9.68v-.35zm4.7-2.69h2.51c-.075.92-.327 1.84-.73 2.7H14c.213-.886.34-1.79.38-2.7zm0-1.35c-.04-.91-.167-1.81-.38-2.7h2.16c.403.86.655 1.78.73 2.7h-2.51z',
            'dashboard'    => 'M3.76 16h12.48c1.1-1.37 1.76-3.11 1.76-5 0-4.42-3.58-8-8-8s-8 3.58-8 8c0 1.89.66 3.63 1.76 5zM10 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM6 6c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm8 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm-1.49 2.51c.39-.39 1.02-.39 1.41 0 .39.39.39 1.02 0 1.41l-3.34 3.34c-.2.12-.43.19-.68.19-.73 0-1.33-.6-1.33-1.33 0-.25.07-.48.19-.68l3.34-3.34c.19-.18.41-.29.66-.33l.15-.03c.13 0 .26.03.38.08l-.31.31c-.78.78-.78 2.05 0 2.83s2.05.78 2.83 0l.31-.31c.05.12.08.25.08.38 0 .2-.08.39-.22.53l-.21.21-3.34 3.34-.53.22c-.73 0-1.33-.6-1.33-1.33 0-.25.07-.48.19-.68l2.84-2.84zM4 10c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm12 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1z',
            'heart'        => 'M10 17.12c-.22 0-.43-.08-.59-.25C9.1 16.58 3 10.83 3 7c0-2.21 1.79-4 4-4 1.1 0 2.09.45 2.81 1.17l.19.21.19-.21C10.91 3.45 11.9 3 13 3c2.21 0 4 1.79 4 4 0 3.83-6.1 9.58-6.41 9.87-.16.17-.37.25-.59.25z',
            'flag'         => 'M5 3v15h2V3H5zm3 0v8l9-4-9-4z',
            'calendar'     => 'M15 4h3v14H2V4h3V3c0-.55.45-1 1-1s1 .45 1 1v1h6V3c0-.55.45-1 1-1s1 .45 1 1v1zM6 11v2h2v-2H6zm0 3v2h2v-2H6zm3-3v2h2v-2H9zm0 3v2h2v-2H9zm3-3v2h2v-2h-2zm0 3v2h2v-2h-2zm-9 3v-8h14v8H3z',
            'clock'        => 'M10 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6zm.5-6V5.5c0-.28-.22-.5-.5-.5s-.5.22-.5.5v5c0 .14.05.27.15.35l3 2.5c.11.09.24.14.35.14.16 0 .31-.07.41-.2.17-.21.14-.52-.06-.7l-2.85-2.29z',
            'email'        => 'M10 9L3 4h14zM2 5v10h16V5L10 10 2 5z',
            'phone'        => 'M12.06 6l-.21-.2c-.52-.54-.43-.79.08-1.3l2.72-2.75c.81-.82.96-1.21 1.73-.48l.21.2zm.53.45l4.4-4.4c.7.94 2.34 3.47 1.37 5.84-.99 2.42-2.95 4.27-4.17 5.26-.08.07-.57.52-.57.52-1.27 1.07-2.77 2.23-5.33 3.22-2.43 1.03-5.1-.64-6.06-1.36l4.4-4.4 1.18 1.62c.36.5 1.45 1.14 2.64.05l.67-.67c.33-.37 2.04-2.2 2.04-4.39 0-.29-.03-.52-.07-.67l-.5-.62z',
            'location'     => 'M10 2C6.69 2 4 4.69 4 8c0 2.02 1.17 3.71 2.53 4.89.68.6 1.42 1.03 2.03 1.32.31.14.57.25.78.32.26.1.5.15.66.15s.4-.05.66-.15c.21-.07.47-.18.78-.32.61-.29 1.35-.72 2.03-1.32C14.83 11.71 16 10.02 16 8c0-3.31-2.69-6-6-6zm0 8c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z',
            'cart'         => 'M6 13h9c.55 0 1 .45 1 1s-.45 1-1 1H5c-.55 0-1-.45-1-1V4H2V2h3c.55 0 1 .45 1 1v2h13l-4 7H6v1zm0 3c-.55 0-1 .45-1 1s.45 1 1 1 1-.45 1-1-.45-1-1-1zm8 0c-.55 0-1 .45-1 1s.45 1 1 1 1-.45 1-1-.45-1-1-1z',
            'money-alt'    => 'M10 6c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zm-6 2c1.1 0 2-.9 2-2H2v8h4c0-1.1-.9-2-2-2V8zm12 4c-1.1 0-2 .9-2 2h4V6h-4c0 1.1.9 2 2 2v4zM2 4h16c1.1 0 2 .9 2 2v10c0 1.1-.9 2-2 2H2c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z',
            'awards'       => 'M4 0v11l4 2 4-2V0H4zm6 7.38l-2 1-2-1V2h4v5.38zM10 15l-2 5 2-2 2 2-2-5zm0-3c-2.21 0-4-1.79-4-4V6h2v2c0 1.1.9 2 2 2s2-.9 2-2V6h2v2c0 2.21-1.79 4-4 4z',
            'megaphone'    => 'M18.15 5.94c.46 1.62.38 3.21-.02 4.67-.12.45-.6.66-1 .46l-8.14-4.17L7 7.41c-.17.59-.76.93-1.36.82l-.99-.18c-.47-.09-.8-.5-.8-.98V5.72c0-.22.06-.43.17-.61L5.1 3.54c.29-.45.8-.63 1.28-.41l.39.18 8.63-2.1c.42-.11.86.14.96.57.22.98.14 2.03-.26 3.02l2.95 1.5c.21.1.28.38.16.58l-.06.06zM6.08 5l-.7 1.16.7.13V5zm8.79 4.72L7 5.97v3.93l5.46 2.8 1.41.49c.85-1.11 1.27-2.4 1-3.47zM2.5 17.5c0-.83.67-1.5 1.5-1.5h7c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5H4c-.83 0-1.5-.67-1.5-1.5z',
            'performance'  => 'M3.08 9H2V6h1.08c.12-.85.44-1.63.93-2.31L3.3 3 5.32.97l.69.69c.68-.49 1.46-.81 2.31-.93V0h3v.73c.85.12 1.63.44 2.31.93l.69-.69 2.02 2.02-.69.69c.49.68.81 1.46.93 2.31H18v3h-1.08c-.12.85-.44 1.63-.93 2.31l.69.69-.02.02-2 2.02-.69-.69c-.68.49-1.46.81-2.31.93V15h-3v-.73c-.85-.12-1.63-.44-2.31-.93l-.69.69-2.02-2.02.69-.69c-.49-.68-.81-1.46-.93-2.31zM11 6.5c0-1.38-1.12-2.5-2.5-2.5S6 5.12 6 6.5 7.12 9 8.5 9 11 7.88 11 6.5z',
            'visibility'   => 'M10 4c2.9 0 5.53 1.64 7.6 4-2.07 2.36-4.7 4-7.6 4s-5.53-1.64-7.6-4c2.07-2.36 4.7-4 7.6-4m0-2C5.6 2 1.8 5.42 0 8c1.8 2.58 5.6 6 10 6s8.2-3.42 10-6c-1.8-2.58-5.6-6-10-6zm0 4c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2m0-2c-2.2 0-4 1.8-4 4s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4z',
        ];

        return $icons[$icon] ?? $icons['star-filled'];
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

        $columns              = absint($attributes['columns']) ?: 3;
        $features             = (array) $attributes['features'];
        $icon_color           = sanitize_hex_color($attributes['iconColor']) ?: '#0073aa';
        $icon_background_color = sanitize_hex_color($attributes['iconBackgroundColor']) ?: '#f0f0f0';
        $title_color          = sanitize_hex_color($attributes['titleColor']) ?: '#1e1e1e';
        $description_color    = sanitize_hex_color($attributes['descriptionColor']) ?: '#666666';
        $background_color     = sanitize_hex_color($attributes['backgroundColor']) ?: '#ffffff';
        $icon_size            = absint($attributes['iconSize']) ?: 48;
        $gap                  = absint($attributes['gap']) ?: 30;

        $wrapper_attributes = $this->get_wrapper_attributes($attributes);

        $grid_style = sprintf(
            'display: grid; grid-template-columns: repeat(%d, 1fr); gap: %dpx; padding: 40px 20px;',
            $columns,
            $gap
        );

        $icon_wrapper_style = sprintf(
            'width: %dpx; height: %dpx; background-color: %s; border-radius: 50%%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;',
            $icon_size,
            $icon_size,
            $icon_background_color
        );

        $icon_svg_size = intval($icon_size * 0.5);

        ob_start();
        ?>
        <div <?php echo $wrapper_attributes; ?> style="background-color: <?php echo esc_attr($background_color); ?>">
            <div class="wp-block-genblocks-features__grid" style="<?php echo esc_attr($grid_style); ?>">
                <?php foreach ($features as $feature) :
                    $icon = sanitize_text_field($feature['icon'] ?? 'star-filled');
                    $title = $this->sanitize_html($feature['title'] ?? '');
                    $description = $this->sanitize_html($feature['description'] ?? '');
                    $svg_path = $this->get_dashicon_svg($icon);
                ?>
                    <div class="wp-block-genblocks-features__item" style="text-align: center; padding: 20px;">
                        <div class="wp-block-genblocks-features__icon" style="<?php echo esc_attr($icon_wrapper_style); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="<?php echo esc_attr($icon_svg_size); ?>" height="<?php echo esc_attr($icon_svg_size); ?>">
                                <path d="<?php echo esc_attr($svg_path); ?>" fill="<?php echo esc_attr($icon_color); ?>"/>
                            </svg>
                        </div>
                        <?php if (!empty($title)) : ?>
                            <h3 class="wp-block-genblocks-features__title" style="color: <?php echo esc_attr($title_color); ?>; margin-bottom: 8px; font-size: 1.25rem; font-weight: 600;">
                                <?php echo $title; ?>
                            </h3>
                        <?php endif; ?>
                        <?php if (!empty($description)) : ?>
                            <p class="wp-block-genblocks-features__description" style="color: <?php echo esc_attr($description_color); ?>; margin: 0; line-height: 1.6;">
                                <?php echo $description; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
