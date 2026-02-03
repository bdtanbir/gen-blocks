<?php
/**
 * Template Library Class
 *
 * @package GenBlocks
 */

namespace GenBlocks;

defined('ABSPATH') || exit;

/**
 * Manages pre-built block templates
 */
class Template_Library {

    /**
     * Get all templates
     *
     * @return array
     */
    public function get_all() {
        $templates = [
            // CTA Templates
            $this->cta_simple(),
            $this->cta_with_background(),
            $this->cta_split(),

            // Hero Templates
            $this->hero_centered(),
            $this->hero_split(),
            $this->hero_with_cover(),

            // Features Templates
            $this->features_three_column(),
            $this->features_two_column(),
            $this->features_list(),

            // Testimonial Templates
            $this->testimonial_simple(),
            $this->testimonial_with_image(),

            // Content Templates
            $this->about_section(),
            $this->stats_section(),
            $this->team_member(),
        ];

        return apply_filters('genblocks_templates', $templates);
    }

    /**
     * Get templates by category
     *
     * @param string $category Category slug.
     * @return array
     */
    public function get_by_category($category) {
        return array_filter($this->get_all(), function ($template) use ($category) {
            return $template['category'] === $category;
        });
    }

    /**
     * Get template by ID
     *
     * @param string $id Template ID.
     * @return array|null
     */
    public function get_by_id($id) {
        foreach ($this->get_all() as $template) {
            if ($template['id'] === $id) {
                return $template;
            }
        }
        return null;
    }

    /**
     * Apply template with variables
     *
     * @param string $id        Template ID.
     * @param array  $variables Variables to replace.
     * @return array|null
     */
    public function apply($id, $variables = []) {
        $template = $this->get_by_id($id);

        if (!$template) {
            return null;
        }

        $structure = $template['structure'];

        if (!empty($variables)) {
            $structure = $this->replace_variables($structure, $variables);
        }

        return $structure;
    }

    /**
     * Replace variables in template
     *
     * @param array $structure Template structure.
     * @param array $variables Variables to replace.
     * @return array
     */
    private function replace_variables($structure, $variables) {
        $json = wp_json_encode($structure);

        foreach ($variables as $key => $value) {
            $json = str_replace('{{' . $key . '}}', addslashes($value), $json);
        }

        return json_decode($json, true);
    }

    /**
     * Get available categories
     *
     * @return array
     */
    public function get_categories() {
        return [
            [
                'slug'  => 'cta',
                'name'  => __('Call to Action', 'get-blocks'),
                'icon'  => 'megaphone',
            ],
            [
                'slug'  => 'hero',
                'name'  => __('Hero Sections', 'get-blocks'),
                'icon'  => 'star-filled',
            ],
            [
                'slug'  => 'features',
                'name'  => __('Features', 'get-blocks'),
                'icon'  => 'grid-view',
            ],
            [
                'slug'  => 'testimonials',
                'name'  => __('Testimonials', 'get-blocks'),
                'icon'  => 'format-quote',
            ],
            [
                'slug'  => 'content',
                'name'  => __('Content Sections', 'get-blocks'),
                'icon'  => 'text-page',
            ],
        ];
    }

    // =========================================================================
    // CTA TEMPLATES
    // =========================================================================

    /**
     * Simple CTA template
     *
     * @return array
     */
    private function cta_simple() {
        return [
            'id'          => 'cta-simple',
            'name'        => __('Simple CTA', 'get-blocks'),
            'description' => __('Centered call-to-action with heading, text, and button', 'get-blocks'),
            'category'    => 'cta',
            'keywords'    => ['cta', 'call to action', 'button', 'conversion'],
            'variables'   => ['title', 'description', 'button_text', 'button_url'],
            'structure'   => [
                'blockName'   => 'core/group',
                'attrs'       => [
                    'align' => 'full',
                    'style' => [
                        'spacing' => [
                            'padding' => [
                                'top'    => '60px',
                                'bottom' => '60px',
                                'left'   => '20px',
                                'right'  => '20px',
                            ],
                        ],
                        'color'   => [
                            'background' => '#f8f9fa',
                        ],
                    ],
                ],
                'innerBlocks' => [
                    [
                        'blockName'   => 'core/heading',
                        'attrs'       => [
                            'level'     => 2,
                            'textAlign' => 'center',
                            'content'   => '{{title}}',
                        ],
                        'innerBlocks' => [],
                    ],
                    [
                        'blockName'   => 'core/paragraph',
                        'attrs'       => [
                            'align'   => 'center',
                            'content' => '{{description}}',
                        ],
                        'innerBlocks' => [],
                    ],
                    [
                        'blockName'   => 'core/buttons',
                        'attrs'       => [
                            'layout' => [
                                'type'           => 'flex',
                                'justifyContent' => 'center',
                            ],
                        ],
                        'innerBlocks' => [
                            [
                                'blockName'   => 'core/button',
                                'attrs'       => [
                                    'text' => '{{button_text}}',
                                    'url'  => '{{button_url}}',
                                ],
                                'innerBlocks' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * CTA with background color
     *
     * @return array
     */
    private function cta_with_background() {
        return [
            'id'          => 'cta-background',
            'name'        => __('CTA with Background', 'get-blocks'),
            'description' => __('Bold CTA with colored background', 'get-blocks'),
            'category'    => 'cta',
            'keywords'    => ['cta', 'colored', 'bold', 'prominent'],
            'variables'   => ['title', 'description', 'button_text', 'button_url'],
            'structure'   => [
                'blockName'   => 'core/group',
                'attrs'       => [
                    'align'           => 'full',
                    'backgroundColor' => 'primary',
                    'textColor'       => 'white',
                    'style'           => [
                        'spacing' => [
                            'padding' => [
                                'top'    => '80px',
                                'bottom' => '80px',
                                'left'   => '20px',
                                'right'  => '20px',
                            ],
                        ],
                    ],
                ],
                'innerBlocks' => [
                    [
                        'blockName'   => 'core/heading',
                        'attrs'       => [
                            'level'     => 2,
                            'textAlign' => 'center',
                            'content'   => '{{title}}',
                        ],
                        'innerBlocks' => [],
                    ],
                    [
                        'blockName'   => 'core/paragraph',
                        'attrs'       => [
                            'align'   => 'center',
                            'content' => '{{description}}',
                        ],
                        'innerBlocks' => [],
                    ],
                    [
                        'blockName'   => 'core/buttons',
                        'attrs'       => [
                            'layout' => [
                                'type'           => 'flex',
                                'justifyContent' => 'center',
                            ],
                        ],
                        'innerBlocks' => [
                            [
                                'blockName'   => 'core/button',
                                'attrs'       => [
                                    'text'            => '{{button_text}}',
                                    'url'             => '{{button_url}}',
                                    'backgroundColor' => 'white',
                                    'textColor'       => 'primary',
                                ],
                                'innerBlocks' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Split CTA template
     *
     * @return array
     */
    private function cta_split() {
        return [
            'id'          => 'cta-split',
            'name'        => __('Split CTA', 'get-blocks'),
            'description' => __('Two-column CTA with text and image', 'get-blocks'),
            'category'    => 'cta',
            'keywords'    => ['cta', 'split', 'two column', 'image'],
            'variables'   => ['title', 'description', 'button_text', 'button_url', 'image_url'],
            'structure'   => [
                'blockName'   => 'core/columns',
                'attrs'       => [
                    'align' => 'wide',
                ],
                'innerBlocks' => [
                    [
                        'blockName'   => 'core/column',
                        'attrs'       => [
                            'verticalAlignment' => 'center',
                        ],
                        'innerBlocks' => [
                            [
                                'blockName'   => 'core/heading',
                                'attrs'       => [
                                    'level'   => 2,
                                    'content' => '{{title}}',
                                ],
                                'innerBlocks' => [],
                            ],
                            [
                                'blockName'   => 'core/paragraph',
                                'attrs'       => [
                                    'content' => '{{description}}',
                                ],
                                'innerBlocks' => [],
                            ],
                            [
                                'blockName'   => 'core/buttons',
                                'attrs'       => [],
                                'innerBlocks' => [
                                    [
                                        'blockName'   => 'core/button',
                                        'attrs'       => [
                                            'text' => '{{button_text}}',
                                            'url'  => '{{button_url}}',
                                        ],
                                        'innerBlocks' => [],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'blockName'   => 'core/column',
                        'attrs'       => [],
                        'innerBlocks' => [
                            [
                                'blockName'   => 'core/image',
                                'attrs'       => [
                                    'url' => '{{image_url}}',
                                    'alt' => '',
                                ],
                                'innerBlocks' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    // =========================================================================
    // HERO TEMPLATES
    // =========================================================================

    /**
     * Centered hero template
     *
     * @return array
     */
    private function hero_centered() {
        return [
            'id'          => 'hero-centered',
            'name'        => __('Centered Hero', 'get-blocks'),
            'description' => __('Large centered headline with subtitle and buttons', 'get-blocks'),
            'category'    => 'hero',
            'keywords'    => ['hero', 'banner', 'header', 'centered'],
            'variables'   => ['title', 'subtitle', 'button_text', 'button_url', 'secondary_button_text', 'secondary_button_url'],
            'structure'   => [
                'blockName'   => 'core/group',
                'attrs'       => [
                    'align' => 'full',
                    'style' => [
                        'spacing' => [
                            'padding' => [
                                'top'    => '100px',
                                'bottom' => '100px',
                                'left'   => '20px',
                                'right'  => '20px',
                            ],
                        ],
                    ],
                ],
                'innerBlocks' => [
                    [
                        'blockName'   => 'core/heading',
                        'attrs'       => [
                            'level'     => 1,
                            'textAlign' => 'center',
                            'content'   => '{{title}}',
                            'style'     => [
                                'typography' => [
                                    'fontSize' => '48px',
                                ],
                            ],
                        ],
                        'innerBlocks' => [],
                    ],
                    [
                        'blockName'   => 'core/paragraph',
                        'attrs'       => [
                            'align'   => 'center',
                            'content' => '{{subtitle}}',
                            'style'   => [
                                'typography' => [
                                    'fontSize' => '20px',
                                ],
                            ],
                        ],
                        'innerBlocks' => [],
                    ],
                    [
                        'blockName'   => 'core/spacer',
                        'attrs'       => [
                            'height' => '20px',
                        ],
                        'innerBlocks' => [],
                    ],
                    [
                        'blockName'   => 'core/buttons',
                        'attrs'       => [
                            'layout' => [
                                'type'           => 'flex',
                                'justifyContent' => 'center',
                            ],
                        ],
                        'innerBlocks' => [
                            [
                                'blockName'   => 'core/button',
                                'attrs'       => [
                                    'text' => '{{button_text}}',
                                    'url'  => '{{button_url}}',
                                ],
                                'innerBlocks' => [],
                            ],
                            [
                                'blockName'   => 'core/button',
                                'attrs'       => [
                                    'text'      => '{{secondary_button_text}}',
                                    'url'       => '{{secondary_button_url}}',
                                    'className' => 'is-style-outline',
                                ],
                                'innerBlocks' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Split hero template
     *
     * @return array
     */
    private function hero_split() {
        return [
            'id'          => 'hero-split',
            'name'        => __('Split Hero', 'get-blocks'),
            'description' => __('Hero with content on left and image on right', 'get-blocks'),
            'category'    => 'hero',
            'keywords'    => ['hero', 'split', 'image', 'two column'],
            'variables'   => ['title', 'subtitle', 'button_text', 'button_url', 'image_url'],
            'structure'   => [
                'blockName'   => 'core/columns',
                'attrs'       => [
                    'align' => 'full',
                    'style' => [
                        'spacing' => [
                            'padding' => [
                                'top'    => '60px',
                                'bottom' => '60px',
                            ],
                        ],
                    ],
                ],
                'innerBlocks' => [
                    [
                        'blockName'   => 'core/column',
                        'attrs'       => [
                            'verticalAlignment' => 'center',
                            'width'             => '50%',
                        ],
                        'innerBlocks' => [
                            [
                                'blockName'   => 'core/heading',
                                'attrs'       => [
                                    'level'   => 1,
                                    'content' => '{{title}}',
                                ],
                                'innerBlocks' => [],
                            ],
                            [
                                'blockName'   => 'core/paragraph',
                                'attrs'       => [
                                    'content' => '{{subtitle}}',
                                ],
                                'innerBlocks' => [],
                            ],
                            [
                                'blockName'   => 'core/buttons',
                                'attrs'       => [],
                                'innerBlocks' => [
                                    [
                                        'blockName'   => 'core/button',
                                        'attrs'       => [
                                            'text' => '{{button_text}}',
                                            'url'  => '{{button_url}}',
                                        ],
                                        'innerBlocks' => [],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'blockName'   => 'core/column',
                        'attrs'       => [
                            'width' => '50%',
                        ],
                        'innerBlocks' => [
                            [
                                'blockName'   => 'core/image',
                                'attrs'       => [
                                    'url'   => '{{image_url}}',
                                    'alt'   => '',
                                    'align' => 'center',
                                ],
                                'innerBlocks' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Hero with cover image
     *
     * @return array
     */
    private function hero_with_cover() {
        return [
            'id'          => 'hero-cover',
            'name'        => __('Hero with Cover Image', 'get-blocks'),
            'description' => __('Hero section with background image and overlay', 'get-blocks'),
            'category'    => 'hero',
            'keywords'    => ['hero', 'cover', 'background', 'image'],
            'variables'   => ['title', 'subtitle', 'button_text', 'button_url', 'image_url'],
            'structure'   => [
                'blockName'   => 'core/cover',
                'attrs'       => [
                    'url'             => '{{image_url}}',
                    'dimRatio'        => 60,
                    'overlayColor'    => 'black',
                    'minHeight'       => 500,
                    'align'           => 'full',
                    'contentPosition' => 'center center',
                ],
                'innerBlocks' => [
                    [
                        'blockName'   => 'core/heading',
                        'attrs'       => [
                            'level'     => 1,
                            'textAlign' => 'center',
                            'content'   => '{{title}}',
                            'textColor' => 'white',
                        ],
                        'innerBlocks' => [],
                    ],
                    [
                        'blockName'   => 'core/paragraph',
                        'attrs'       => [
                            'align'     => 'center',
                            'content'   => '{{subtitle}}',
                            'textColor' => 'white',
                        ],
                        'innerBlocks' => [],
                    ],
                    [
                        'blockName'   => 'core/buttons',
                        'attrs'       => [
                            'layout' => [
                                'type'           => 'flex',
                                'justifyContent' => 'center',
                            ],
                        ],
                        'innerBlocks' => [
                            [
                                'blockName'   => 'core/button',
                                'attrs'       => [
                                    'text' => '{{button_text}}',
                                    'url'  => '{{button_url}}',
                                ],
                                'innerBlocks' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    // =========================================================================
    // FEATURES TEMPLATES
    // =========================================================================

    /**
     * Three column features
     *
     * @return array
     */
    private function features_three_column() {
        return [
            'id'          => 'features-3col',
            'name'        => __('3-Column Features', 'get-blocks'),
            'description' => __('Three column layout for features or services', 'get-blocks'),
            'category'    => 'features',
            'keywords'    => ['features', 'services', 'three column', 'grid'],
            'variables'   => ['title1', 'desc1', 'title2', 'desc2', 'title3', 'desc3'],
            'structure'   => [
                'blockName'   => 'core/group',
                'attrs'       => [
                    'align' => 'wide',
                    'style' => [
                        'spacing' => [
                            'padding' => [
                                'top'    => '60px',
                                'bottom' => '60px',
                            ],
                        ],
                    ],
                ],
                'innerBlocks' => [
                    [
                        'blockName'   => 'core/columns',
                        'attrs'       => [],
                        'innerBlocks' => [
                            [
                                'blockName'   => 'core/column',
                                'attrs'       => [],
                                'innerBlocks' => [
                                    [
                                        'blockName'   => 'core/heading',
                                        'attrs'       => [
                                            'level'     => 3,
                                            'textAlign' => 'center',
                                            'content'   => '{{title1}}',
                                        ],
                                        'innerBlocks' => [],
                                    ],
                                    [
                                        'blockName'   => 'core/paragraph',
                                        'attrs'       => [
                                            'align'   => 'center',
                                            'content' => '{{desc1}}',
                                        ],
                                        'innerBlocks' => [],
                                    ],
                                ],
                            ],
                            [
                                'blockName'   => 'core/column',
                                'attrs'       => [],
                                'innerBlocks' => [
                                    [
                                        'blockName'   => 'core/heading',
                                        'attrs'       => [
                                            'level'     => 3,
                                            'textAlign' => 'center',
                                            'content'   => '{{title2}}',
                                        ],
                                        'innerBlocks' => [],
                                    ],
                                    [
                                        'blockName'   => 'core/paragraph',
                                        'attrs'       => [
                                            'align'   => 'center',
                                            'content' => '{{desc2}}',
                                        ],
                                        'innerBlocks' => [],
                                    ],
                                ],
                            ],
                            [
                                'blockName'   => 'core/column',
                                'attrs'       => [],
                                'innerBlocks' => [
                                    [
                                        'blockName'   => 'core/heading',
                                        'attrs'       => [
                                            'level'     => 3,
                                            'textAlign' => 'center',
                                            'content'   => '{{title3}}',
                                        ],
                                        'innerBlocks' => [],
                                    ],
                                    [
                                        'blockName'   => 'core/paragraph',
                                        'attrs'       => [
                                            'align'   => 'center',
                                            'content' => '{{desc3}}',
                                        ],
                                        'innerBlocks' => [],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Two column features
     *
     * @return array
     */
    private function features_two_column() {
        return [
            'id'          => 'features-2col',
            'name'        => __('2-Column Features', 'get-blocks'),
            'description' => __('Two column layout for features', 'get-blocks'),
            'category'    => 'features',
            'keywords'    => ['features', 'two column', 'benefits'],
            'variables'   => ['title1', 'desc1', 'title2', 'desc2'],
            'structure'   => [
                'blockName'   => 'core/columns',
                'attrs'       => [
                    'align' => 'wide',
                ],
                'innerBlocks' => [
                    [
                        'blockName'   => 'core/column',
                        'attrs'       => [],
                        'innerBlocks' => [
                            [
                                'blockName'   => 'core/heading',
                                'attrs'       => [
                                    'level'   => 3,
                                    'content' => '{{title1}}',
                                ],
                                'innerBlocks' => [],
                            ],
                            [
                                'blockName'   => 'core/paragraph',
                                'attrs'       => [
                                    'content' => '{{desc1}}',
                                ],
                                'innerBlocks' => [],
                            ],
                        ],
                    ],
                    [
                        'blockName'   => 'core/column',
                        'attrs'       => [],
                        'innerBlocks' => [
                            [
                                'blockName'   => 'core/heading',
                                'attrs'       => [
                                    'level'   => 3,
                                    'content' => '{{title2}}',
                                ],
                                'innerBlocks' => [],
                            ],
                            [
                                'blockName'   => 'core/paragraph',
                                'attrs'       => [
                                    'content' => '{{desc2}}',
                                ],
                                'innerBlocks' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Features list
     *
     * @return array
     */
    private function features_list() {
        return [
            'id'          => 'features-list',
            'name'        => __('Features List', 'get-blocks'),
            'description' => __('Simple list of features with heading', 'get-blocks'),
            'category'    => 'features',
            'keywords'    => ['features', 'list', 'checklist', 'benefits'],
            'variables'   => ['title', 'features'],
            'structure'   => [
                'blockName'   => 'core/group',
                'attrs'       => [
                    'style' => [
                        'spacing' => [
                            'padding' => [
                                'top'    => '40px',
                                'bottom' => '40px',
                            ],
                        ],
                    ],
                ],
                'innerBlocks' => [
                    [
                        'blockName'   => 'core/heading',
                        'attrs'       => [
                            'level'   => 2,
                            'content' => '{{title}}',
                        ],
                        'innerBlocks' => [],
                    ],
                    [
                        'blockName'   => 'core/list',
                        'attrs'       => [
                            'values' => '{{features}}',
                        ],
                        'innerBlocks' => [],
                    ],
                ],
            ],
        ];
    }

    // =========================================================================
    // TESTIMONIAL TEMPLATES
    // =========================================================================

    /**
     * Simple testimonial
     *
     * @return array
     */
    private function testimonial_simple() {
        return [
            'id'          => 'testimonial-simple',
            'name'        => __('Simple Testimonial', 'get-blocks'),
            'description' => __('Quote with author attribution', 'get-blocks'),
            'category'    => 'testimonials',
            'keywords'    => ['testimonial', 'quote', 'review'],
            'variables'   => ['quote', 'author', 'role'],
            'structure'   => [
                'blockName'   => 'core/group',
                'attrs'       => [
                    'style' => [
                        'spacing' => [
                            'padding' => [
                                'top'    => '40px',
                                'bottom' => '40px',
                                'left'   => '40px',
                                'right'  => '40px',
                            ],
                        ],
                        'color'   => [
                            'background' => '#f8f9fa',
                        ],
                        'border'  => [
                            'radius' => '8px',
                        ],
                    ],
                ],
                'innerBlocks' => [
                    [
                        'blockName'   => 'core/quote',
                        'attrs'       => [
                            'value'    => '{{quote}}',
                            'citation' => '{{author}}, {{role}}',
                        ],
                        'innerBlocks' => [],
                    ],
                ],
            ],
        ];
    }

    /**
     * Testimonial with image
     *
     * @return array
     */
    private function testimonial_with_image() {
        return [
            'id'          => 'testimonial-image',
            'name'        => __('Testimonial with Image', 'get-blocks'),
            'description' => __('Testimonial card with author photo', 'get-blocks'),
            'category'    => 'testimonials',
            'keywords'    => ['testimonial', 'quote', 'avatar', 'image'],
            'variables'   => ['quote', 'author', 'role', 'image_url'],
            'structure'   => [
                'blockName'   => 'core/columns',
                'attrs'       => [
                    'style' => [
                        'spacing' => [
                            'padding' => [
                                'top'    => '40px',
                                'bottom' => '40px',
                            ],
                        ],
                    ],
                ],
                'innerBlocks' => [
                    [
                        'blockName'   => 'core/column',
                        'attrs'       => [
                            'width' => '80px',
                        ],
                        'innerBlocks' => [
                            [
                                'blockName'   => 'core/image',
                                'attrs'       => [
                                    'url'       => '{{image_url}}',
                                    'alt'       => '{{author}}',
                                    'className' => 'is-style-rounded',
                                ],
                                'innerBlocks' => [],
                            ],
                        ],
                    ],
                    [
                        'blockName'   => 'core/column',
                        'attrs'       => [],
                        'innerBlocks' => [
                            [
                                'blockName'   => 'core/paragraph',
                                'attrs'       => [
                                    'content' => '"{{quote}}"',
                                    'style'   => [
                                        'typography' => [
                                            'fontStyle' => 'italic',
                                        ],
                                    ],
                                ],
                                'innerBlocks' => [],
                            ],
                            [
                                'blockName'   => 'core/paragraph',
                                'attrs'       => [
                                    'content' => '<strong>{{author}}</strong><br>{{role}}',
                                ],
                                'innerBlocks' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    // =========================================================================
    // CONTENT TEMPLATES
    // =========================================================================

    /**
     * About section
     *
     * @return array
     */
    private function about_section() {
        return [
            'id'          => 'about-section',
            'name'        => __('About Section', 'get-blocks'),
            'description' => __('Company or personal about section', 'get-blocks'),
            'category'    => 'content',
            'keywords'    => ['about', 'company', 'introduction'],
            'variables'   => ['title', 'content', 'button_text', 'button_url'],
            'structure'   => [
                'blockName'   => 'core/group',
                'attrs'       => [
                    'align' => 'wide',
                    'style' => [
                        'spacing' => [
                            'padding' => [
                                'top'    => '60px',
                                'bottom' => '60px',
                            ],
                        ],
                    ],
                ],
                'innerBlocks' => [
                    [
                        'blockName'   => 'core/heading',
                        'attrs'       => [
                            'level'   => 2,
                            'content' => '{{title}}',
                        ],
                        'innerBlocks' => [],
                    ],
                    [
                        'blockName'   => 'core/paragraph',
                        'attrs'       => [
                            'content' => '{{content}}',
                        ],
                        'innerBlocks' => [],
                    ],
                    [
                        'blockName'   => 'core/buttons',
                        'attrs'       => [],
                        'innerBlocks' => [
                            [
                                'blockName'   => 'core/button',
                                'attrs'       => [
                                    'text' => '{{button_text}}',
                                    'url'  => '{{button_url}}',
                                ],
                                'innerBlocks' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Stats section
     *
     * @return array
     */
    private function stats_section() {
        return [
            'id'          => 'stats-section',
            'name'        => __('Stats Section', 'get-blocks'),
            'description' => __('Display key metrics or statistics', 'get-blocks'),
            'category'    => 'content',
            'keywords'    => ['stats', 'numbers', 'metrics', 'counter'],
            'variables'   => ['stat1_number', 'stat1_label', 'stat2_number', 'stat2_label', 'stat3_number', 'stat3_label'],
            'structure'   => [
                'blockName'   => 'core/columns',
                'attrs'       => [
                    'align' => 'wide',
                    'style' => [
                        'spacing' => [
                            'padding' => [
                                'top'    => '40px',
                                'bottom' => '40px',
                            ],
                        ],
                    ],
                ],
                'innerBlocks' => [
                    [
                        'blockName'   => 'core/column',
                        'attrs'       => [],
                        'innerBlocks' => [
                            [
                                'blockName'   => 'core/heading',
                                'attrs'       => [
                                    'level'     => 2,
                                    'textAlign' => 'center',
                                    'content'   => '{{stat1_number}}',
                                ],
                                'innerBlocks' => [],
                            ],
                            [
                                'blockName'   => 'core/paragraph',
                                'attrs'       => [
                                    'align'   => 'center',
                                    'content' => '{{stat1_label}}',
                                ],
                                'innerBlocks' => [],
                            ],
                        ],
                    ],
                    [
                        'blockName'   => 'core/column',
                        'attrs'       => [],
                        'innerBlocks' => [
                            [
                                'blockName'   => 'core/heading',
                                'attrs'       => [
                                    'level'     => 2,
                                    'textAlign' => 'center',
                                    'content'   => '{{stat2_number}}',
                                ],
                                'innerBlocks' => [],
                            ],
                            [
                                'blockName'   => 'core/paragraph',
                                'attrs'       => [
                                    'align'   => 'center',
                                    'content' => '{{stat2_label}}',
                                ],
                                'innerBlocks' => [],
                            ],
                        ],
                    ],
                    [
                        'blockName'   => 'core/column',
                        'attrs'       => [],
                        'innerBlocks' => [
                            [
                                'blockName'   => 'core/heading',
                                'attrs'       => [
                                    'level'     => 2,
                                    'textAlign' => 'center',
                                    'content'   => '{{stat3_number}}',
                                ],
                                'innerBlocks' => [],
                            ],
                            [
                                'blockName'   => 'core/paragraph',
                                'attrs'       => [
                                    'align'   => 'center',
                                    'content' => '{{stat3_label}}',
                                ],
                                'innerBlocks' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Team member card
     *
     * @return array
     */
    private function team_member() {
        return [
            'id'          => 'team-member',
            'name'        => __('Team Member', 'get-blocks'),
            'description' => __('Team member profile card', 'get-blocks'),
            'category'    => 'content',
            'keywords'    => ['team', 'member', 'staff', 'profile'],
            'variables'   => ['name', 'role', 'bio', 'image_url'],
            'structure'   => [
                'blockName'   => 'core/group',
                'attrs'       => [
                    'style' => [
                        'spacing' => [
                            'padding' => [
                                'top'    => '30px',
                                'bottom' => '30px',
                                'left'   => '30px',
                                'right'  => '30px',
                            ],
                        ],
                        'border'  => [
                            'radius' => '8px',
                        ],
                        'color'   => [
                            'background' => '#ffffff',
                        ],
                    ],
                ],
                'innerBlocks' => [
                    [
                        'blockName'   => 'core/image',
                        'attrs'       => [
                            'url'       => '{{image_url}}',
                            'alt'       => '{{name}}',
                            'align'     => 'center',
                            'className' => 'is-style-rounded',
                        ],
                        'innerBlocks' => [],
                    ],
                    [
                        'blockName'   => 'core/heading',
                        'attrs'       => [
                            'level'     => 3,
                            'textAlign' => 'center',
                            'content'   => '{{name}}',
                        ],
                        'innerBlocks' => [],
                    ],
                    [
                        'blockName'   => 'core/paragraph',
                        'attrs'       => [
                            'align'   => 'center',
                            'content' => '<strong>{{role}}</strong>',
                        ],
                        'innerBlocks' => [],
                    ],
                    [
                        'blockName'   => 'core/paragraph',
                        'attrs'       => [
                            'align'   => 'center',
                            'content' => '{{bio}}',
                        ],
                        'innerBlocks' => [],
                    ],
                ],
            ],
        ];
    }
}
