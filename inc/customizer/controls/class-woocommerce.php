<?php
namespace Polysaas\Customizer\Controls;

use Polysaas\Core\Config;

class Woocommerce extends Control_Base {

    /**
     * Section ID
     */
    protected $section = 'woocommerce';

    /**
     * Register controls
     */
    public function register() {
        // Only register if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            return;
        }
        
        $this->add_shop_archive_controls();
        $this->add_product_single_controls();
    }

    /**
     * Add shop archive controls
     */
    private function add_shop_archive_controls() {
        // Shop Layout Heading
        $this->add_heading_control([
            'settings'    => $this->get_setting('shop_archive_heading_1'),
            'heading'     => __('Shop Layout', Config::get('text_domain')),
            'tab'         => 'general',
            'divider'     => false,
            'section'     => Config::prefix('shop_archive'),
        ]);

        // Sidebar Position
        $this->add_inline_control('Select', [
            'settings'    => $this->get_setting('shop_sidebar_position'),
            'label'       => __('Sidebar Position', Config::get('text_domain')),
            'default'     => 'right',
            'choices'     => [
                'left'     => __('Left', Config::get('text_domain')),
                'right'    => __('Right', Config::get('text_domain')),
                'disabled' => __('No Sidebar', Config::get('text_domain')),
            ],
            'transport'   => 'refresh',
            'tab'         => 'general',
            'section'     => Config::prefix('shop_archive'),
        ]);

        // Products Per Page
        $this->add_inline_control('Number', [
            'settings'    => $this->get_setting('shop_products_per_page'),
            'label'       => __('Products Per Page', Config::get('text_domain')),
            'default'     => 12,
            'choices'     => [
                'min'  => 4,
                'max'  => 48,
                'step' => 4,
            ],
            'transport'   => 'refresh',
            'tab'         => 'general',
            'section'     => Config::prefix('shop_archive'),
        ]);

        // Products Per Row
        $this->add_inline_control('Select', [
            'settings'    => $this->get_setting('shop_products_per_row'),
            'label'       => __('Products Per Row', Config::get('text_domain')),
            'default'     => 3,
            'choices'     => [
                2 => __('2 Columns', Config::get('text_domain')),
                3 => __('3 Columns', Config::get('text_domain')),
                4 => __('4 Columns', Config::get('text_domain')),
                5 => __('5 Columns', Config::get('text_domain')),
            ],
            'transport'   => 'refresh',
            'tab'         => 'general',
            'section'     => Config::prefix('shop_archive'),
        ]);
        
        // Shop Elements Heading
        $this->add_heading_control([
            'settings'    => $this->get_setting('shop_archive_heading_2'),
            'heading'     => __('Shop Elements', Config::get('text_domain')),
            'tab'         => 'general',
            'divider'     => 'top',
            'section'     => Config::prefix('shop_archive'),
        ]);

        // Show Product Rating
        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('shop_show_rating'),
            'label'       => __('Show Rating', Config::get('text_domain')),
            'default'     => true,
            'transport'   => 'refresh',
            'tab'         => 'general',
            'section'     => Config::prefix('shop_archive'),
        ]);

        // Show Product Price
        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('shop_show_price'),
            'label'       => __('Show Price', Config::get('text_domain')),
            'default'     => true,
            'transport'   => 'refresh',
            'tab'         => 'general',
            'section'     => Config::prefix('shop_archive'),
        ]);

        // Show Add to Cart Button
        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('shop_show_add_to_cart'),
            'label'       => __('Show Add to Cart', Config::get('text_domain')),
            'default'     => true,
            'transport'   => 'refresh',
            'tab'         => 'general',
            'section'     => Config::prefix('shop_archive'),
        ]);

        // Show Sale Badge
        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('shop_show_sale_badge'),
            'label'       => __('Show Sale Badge', Config::get('text_domain')),
            'default'     => true,
            'transport'   => 'refresh',
            'tab'         => 'general',
            'section'     => Config::prefix('shop_archive'),
        ]);

        // Shop Product Card Heading
        $this->add_heading_control([
            'settings'    => $this->get_setting('shop_archive_heading_3'),
            'heading'     => __('Product Card', Config::get('text_domain')),
            'tab'         => 'design',
            'divider'     => false,
            'section'     => Config::prefix('shop_archive'),
        ]);

        // Card Background Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('shop_card_bg_color'),
            'label'       => __('Card Background', Config::get('text_domain')),
            'default'     => '#ffffff',
            'transport'   => 'auto',
            'tab'         => 'design',
            'section'     => Config::prefix('shop_archive'),
            'output'      => [
                [
                    'element'  => '.woocommerce ul.products li.product',
                    'property' => 'background-color',
                ],
            ],
        ]);

        // Card Border Radius
        $this->add_control('Slider', [
            'settings'    => $this->get_setting('shop_card_border_radius'),
            'label'       => __('Card Border Radius', Config::get('text_domain')),
            'default'     => 8,
            'choices'     => [
                'min'  => 0,
                'max'  => 50,
                'step' => 1,
            ],
            'transport'   => 'auto',
            'tab'         => 'design',
            'section'     => Config::prefix('shop_archive'),
            'output'      => [
                [
                    'element'  => '.woocommerce ul.products li.product',
                    'property' => 'border-radius',
                    'units'    => 'px',
                ],
            ],
        ]);

        // Card Box Shadow
        $this->add_control('Box_Shadow', [
            'settings'    => $this->get_setting('shop_card_box_shadow'),
            'label'       => __('Card Box Shadow', Config::get('text_domain')),
            'default'     => [
                'horizontal' => '0px',
                'vertical'   => '4px',
                'blur'       => '12px',
                'spread'     => '0px',
                'color'      => 'rgba(0,0,0,0.06)',
            ],
            'transport'   => 'auto',
            'tab'         => 'design',
            'section'     => Config::prefix('shop_archive'),
            'output'      => [
                [
                    'element'  => '.woocommerce ul.products li.product',
                    'property' => 'box-shadow',
                ],
            ],
        ]);

        // Product Title Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('shop_product_title_color'),
            'label'       => __('Product Title', Config::get('text_domain')),
            'default'     => 'var(--e-global-color-secondary)',
            'transport'   => 'auto',
            'tab'         => 'design',
            'section'     => Config::prefix('shop_archive'),
            'output'      => [
                [
                    'element'  => '.woocommerce ul.products li.product .woocommerce-loop-product__title',
                    'property' => 'color',
                ],
            ],
        ]);

        // Product Price Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('shop_product_price_color'),
            'label'       => __('Product Price', Config::get('text_domain')),
            'default'     => 'var(--e-global-color-primary)',
            'transport'   => 'auto',
            'tab'         => 'design',
            'section'     => Config::prefix('shop_archive'),
            'output'      => [
                [
                    'element'  => '.woocommerce ul.products li.product .price',
                    'property' => 'color',
                ],
            ],
        ]);

        // Add to Cart Button Colors Heading
        $this->add_heading_control([
            'settings'    => $this->get_setting('shop_archive_heading_4'),
            'heading'     => __('Add to Cart Button', Config::get('text_domain')),
            'tab'         => 'design',
            'divider'     => 'top',
            'section'     => Config::prefix('shop_archive'),
        ]);

        // Button Text Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('shop_button_text_color'),
            'label'       => __('Button Text', Config::get('text_domain')),
            'default'     => 'var(--e-global-color-secondary)',
            'transport'   => 'auto',
            'tab'         => 'design',
            'section'     => Config::prefix('shop_archive'),
            'output'      => [
                [
                    'element'  => '.woocommerce ul.products li.product .button',
                    'property' => 'color',
                ],
            ],
        ]);

        // Button Background Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('shop_button_bg_color'),
            'label'       => __('Button Background', Config::get('text_domain')),
            'default'     => '',
            'transport'   => 'auto',
            'tab'         => 'design',
            'section'     => Config::prefix('shop_archive'),
            'output'      => [
                [
                    'element'  => '.woocommerce ul.products li.product .button',
                    'property' => 'background-color',
                ],
            ],
        ]);

        // Button Hover Text Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('shop_button_hover_text_color'),
            'label'       => __('Button Hover Text', Config::get('text_domain')),
            'default'     => 'var(--e-global-color-primary)',
            'transport'   => 'auto',
            'tab'         => 'design',
            'section'     => Config::prefix('shop_archive'),
            'output'      => [
                [
                    'element'  => '.woocommerce ul.products li.product .button:hover',
                    'property' => 'color',
                ],
            ],
        ]);

        // Button Hover Background Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('shop_button_hover_bg_color'),
            'label'       => __('Button Hover Background', Config::get('text_domain')),
            'default'     => '',
            'transport'   => 'auto',
            'tab'         => 'design',
            'section'     => Config::prefix('shop_archive'),
            'output'      => [
                [
                    'element'  => '.woocommerce ul.products li.product .button:hover',
                    'property' => 'background-color',
                ],
            ],
        ]);

        // Button Border Radius
        $this->add_control('Slider', [
            'settings'    => $this->get_setting('shop_button_border_radius'),
            'label'       => __('Button Border Radius', Config::get('text_domain')),
            'default'     => 0,
            'choices'     => [
                'min'  => 0,
                'max'  => 50,
                'step' => 1,
            ],
            'transport'   => 'auto',
            'tab'         => 'design',
            'section'     => Config::prefix('shop_archive'),
            'output'      => [
                [
                    'element'  => '.woocommerce ul.products li.product .button',
                    'property' => 'border-radius',
                    'units'    => 'px',
                ],
            ],
        ]);
    }

    /**
     * Add product single controls
     */
    private function add_product_single_controls() {
        // Product Layout Heading
        $this->add_heading_control([
            'settings'    => $this->get_setting('shop_product_heading_1'),
            'heading'     => __('Product Layout', Config::get('text_domain')),
            'tab'         => 'general',
            'divider'     => false,
            'section'     => Config::prefix('shop_product'),
        ]);

        // Single Product Sidebar
        $this->add_inline_control('Select', [
            'settings'    => $this->get_setting('shop_product_sidebar_position'),
            'label'       => __('Sidebar Position', Config::get('text_domain')),
            'default'     => 'disabled',
            'choices'     => [
                'left'     => __('Left', Config::get('text_domain')),
                'right'    => __('Right', Config::get('text_domain')),
                'disabled' => __('No Sidebar', Config::get('text_domain')),
            ],
            'transport'   => 'refresh',
            'tab'         => 'general',
            'section'     => Config::prefix('shop_product'),
        ]);

        // Product Elements Heading
        $this->add_heading_control([
            'settings'    => $this->get_setting('shop_product_heading_2'),
            'heading'     => __('Product Elements', Config::get('text_domain')),
            'tab'         => 'general',
            'divider'     => 'top',
            'section'     => Config::prefix('shop_product'),
        ]);

        // Show Product Meta
        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('shop_product_show_meta'),
            'label'       => __('Show Product Meta', Config::get('text_domain')),
            'default'     => true,
            'transport'   => 'refresh',
            'tab'         => 'general',
            'section'     => Config::prefix('shop_product'),
        ]);

        // Show Product Tabs
        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('shop_product_show_tabs'),
            'label'       => __('Show Product Tabs', Config::get('text_domain')),
            'default'     => true,
            'transport'   => 'refresh',
            'tab'         => 'general',
            'section'     => Config::prefix('shop_product'),
        ]);

        // Show Related Products
        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('shop_product_show_related'),
            'label'       => __('Show Related Products', Config::get('text_domain')),
            'default'     => true,
            'transport'   => 'refresh',
            'tab'         => 'general',
            'section'     => Config::prefix('shop_product'),
        ]);

        // Related Products Count
        $this->add_inline_control('Number', [
            'settings'    => $this->get_setting('shop_product_related_count'),
            'label'       => __('Related Products Count', Config::get('text_domain')),
            'default'     => 4,
            'choices'     => [
                'min'  => 2,
                'max'  => 12,
                'step' => 1,
            ],
            'transport'   => 'refresh',
            'tab'         => 'general',
            'section'     => Config::prefix('shop_product'),
            'active_callback' => [
                [
                    'setting'  => Config::prefix('shop_product_show_related'),
                    'operator' => '==',
                    'value'    => true,
                ],
            ],
        ]);

        // Product Design Heading
        $this->add_heading_control([
            'settings'    => $this->get_setting('shop_product_heading_3'),
            'heading'     => __('Product Design', Config::get('text_domain')),
            'tab'         => 'design',
            'divider'     => false,
            'section'     => Config::prefix('shop_product'),
        ]);

        // Product Title Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('shop_product_single_title_color'),
            'label'       => __('Product Title', Config::get('text_domain')),
            'default'     => 'var(--e-global-color-secondary)',
            'transport'   => 'auto',
            'tab'         => 'design',
            'section'     => Config::prefix('shop_product'),
            'output'      => [
                [
                    'element'  => '.woocommerce div.product .product_title',
                    'property' => 'color',
                ],
            ],
        ]);

        // Product Price Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('shop_product_single_price_color'),
            'label'       => __('Product Price', Config::get('text_domain')),
            'default'     => '',
            'transport'   => 'auto',
            'tab'         => 'design',
            'section'     => Config::prefix('shop_product'),
            'output'      => [
                [
                    'element'  => '.woocommerce div.product p.price, .woocommerce div.product span.price',
                    'property' => 'color',
                ],
            ],
        ]);

        // Product Description Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('shop_product_single_desc_color'),
            'label'       => __('Product Description', Config::get('text_domain')),
            'default'     => 'var(--e-global-color-text)',
            'transport'   => 'auto',
            'tab'         => 'design',
            'section'     => Config::prefix('shop_product'),
            'output'      => [
                [
                    'element'  => '.woocommerce div.product .woocommerce-product-details__short-description',
                    'property' => 'color',
                ],
            ],
        ]);

        // Add to Cart Button Heading
        $this->add_heading_control([
            'settings'    => $this->get_setting('shop_product_heading_4'),
            'heading'     => __('Add to Cart Button', Config::get('text_domain')),
            'tab'         => 'design',
            'divider'     => 'top',
            'section'     => Config::prefix('shop_product'),
        ]);

        // Button Text Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('shop_product_button_text_color'),
            'label'       => __('Button Text', Config::get('text_domain')),
            'default'     => '#ffffff',
            'transport'   => 'auto',
            'tab'         => 'design',
            'section'     => Config::prefix('shop_product'),
            'output'      => [
                [
                    'element'  => '.woocommerce div.product form.cart .button',
                    'property' => 'color',
                ],
            ],
        ]);

        // Button Background Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('shop_product_button_bg_color'),
            'label'       => __('Button Background', Config::get('text_domain')),
            'default'     => 'var(--e-global-color-primary)',
            'transport'   => 'auto',
            'tab'         => 'design',
            'section'     => Config::prefix('shop_product'),
            'output'      => [
                [
                    'element'  => '.woocommerce div.product form.cart .button',
                    'property' => 'background-color',
                ],
            ],
        ]);

        // Button Hover Text Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('shop_product_button_hover_text_color'),
            'label'       => __('Button Hover Text', Config::get('text_domain')),
            'default'     => '#ffffff',
            'transport'   => 'auto',
            'tab'         => 'design',
            'section'     => Config::prefix('shop_product'),
            'output'      => [
                [
                    'element'  => '.woocommerce div.product form.cart .button:hover',
                    'property' => 'color',
                ],
            ],
        ]);

        // Product Tabs Options
        $this->add_heading_control([
            'settings'    => $this->get_setting('shop_product_heading_tabs'),
            'heading'     => __('Product Tabs', Config::get('text_domain')),
            'tab'         => 'general',
            'divider'     => 'top',
            'section'     => Config::prefix('shop_product'),
        ]);

        // Show Product Reviews Tab
        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('shop_product_show_reviews'),
            'label'       => __('Show Reviews Tab', Config::get('text_domain')),
            'default'     => true,
            'transport'   => 'refresh',
            'tab'         => 'general',
            'section'     => Config::prefix('shop_product'),
            'active_callback' => [
                [
                    'setting'  => Config::prefix('shop_product_show_tabs'),
                    'operator' => '==',
                    'value'    => true,
                ],
            ],
        ]);

        // Show Custom Tab
        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('shop_product_show_custom_tab'),
            'label'       => __('Show Custom Tab', Config::get('text_domain')),
            'default'     => false,
            'transport'   => 'refresh',
            'tab'         => 'general',
            'section'     => Config::prefix('shop_product'),
            'active_callback' => [
                [
                    'setting'  => Config::prefix('shop_product_show_tabs'),
                    'operator' => '==',
                    'value'    => true,
                ],
            ],
        ]);

        // Custom Tab Title
        $this->add_control('Text', [
            'settings'    => $this->get_setting('shop_product_custom_tab_title'),
            'label'       => __('Custom Tab Title', Config::get('text_domain')),
            'default'     => '',
            'transport'   => 'refresh',
            'tab'         => 'general',
            'section'     => Config::prefix('shop_product'),
            'active_callback' => [
                [
                    'setting'  => Config::prefix('shop_product_show_tabs'),
                    'operator' => '==',
                    'value'    => true,
                ],
                [
                    'setting'  => Config::prefix('shop_product_show_custom_tab'),
                    'operator' => '==',
                    'value'    => true,
                ],
            ],
        ]);

        // Custom Tab Content
        $this->add_control('Editor', [
            'settings'    => $this->get_setting('shop_product_custom_tab_content'),
            'label'       => __('Custom Tab Content', Config::get('text_domain')),
            'default'     => '',
            'transport'   => 'refresh',
            'tab'         => 'general',
            'section'     => Config::prefix('shop_product'),
            'active_callback' => [
                [
                    'setting'  => Config::prefix('shop_product_show_tabs'),
                    'operator' => '==',
                    'value'    => true,
                ],
                [
                    'setting'  => Config::prefix('shop_product_show_custom_tab'),
                    'operator' => '==',
                    'value'    => true,
                ],
            ],
        ]);

        // Product Meta Options
        $this->add_heading_control([
            'settings'    => $this->get_setting('shop_product_heading_meta'),
            'heading'     => __('Product Meta', Config::get('text_domain')),
            'tab'         => 'general',
            'divider'     => 'top',
            'section'     => Config::prefix('shop_product'),
        ]);

        // Show Custom Meta
        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('shop_product_show_custom_meta'),
            'label'       => __('Show Custom Meta', Config::get('text_domain')),
            'default'     => false,
            'transport'   => 'refresh',
            'tab'         => 'general',
            'section'     => Config::prefix('shop_product'),
        ]);

        // Product Gallery Options
        $this->add_heading_control([
            'settings'    => $this->get_setting('shop_product_heading_gallery'),
            'heading'     => __('Product Gallery', Config::get('text_domain')),
            'tab'         => 'design',
            'divider'     => 'top',
            'section'     => Config::prefix('shop_product'),
        ]);

        // Gallery Border Radius
        $this->add_control('Slider', [
            'settings'    => $this->get_setting('shop_product_gallery_border_radius'),
            'label'       => __('Gallery Border Radius', Config::get('text_domain')),
            'default'     => 8,
            'choices'     => [
                'min'  => 0,
                'max'  => 50,
                'step' => 1,
            ],
            'transport'   => 'auto',
            'tab'         => 'design',
            'section'     => Config::prefix('shop_product'),
            'output'      => [
                [
                    'element'  => '.woocommerce div.product div.images img',
                    'property' => 'border-radius',
                    'units'    => 'px',
                ],
            ],
        ]);

        // Tabs Design Options
        $this->add_heading_control([
            'settings'    => $this->get_setting('shop_product_heading_tabs_design'),
            'heading'     => __('Product Tabs Design', Config::get('text_domain')),
            'tab'         => 'design',
            'divider'     => 'top',
            'section'     => Config::prefix('shop_product'),
        ]);

        // Tabs Background Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('shop_product_tabs_bg_color'),
            'label'       => __('Tabs Background', Config::get('text_domain')),
            'default'     => '#f8f9fa',
            'transport'   => 'auto',
            'tab'         => 'design',
            'section'     => Config::prefix('shop_product'),
            'output'      => [
                [
                    'element'  => '.woocommerce div.product .woocommerce-tabs ul.tabs li',
                    'property' => 'background-color',
                ],
            ],
        ]);

        // Active Tab Background Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('shop_product_tabs_active_bg_color'),
            'label'       => __('Active Tab Background', Config::get('text_domain')),
            'default'     => '#ffffff',
            'transport'   => 'auto',
            'tab'         => 'design',
            'section'     => Config::prefix('shop_product'),
            'output'      => [
                [
                    'element'  => '.woocommerce div.product .woocommerce-tabs ul.tabs li.active',
                    'property' => 'background-color',
                ],
            ],
        ]);

        // Tab Text Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('shop_product_tabs_text_color'),
            'label'       => __('Tab Text Color', Config::get('text_domain')),
            'default'     => 'var(--e-global-color-text)',
            'transport'   => 'auto',
            'tab'         => 'design',
            'section'     => Config::prefix('shop_product'),
            'output'      => [
                [
                    'element'  => '.woocommerce div.product .woocommerce-tabs ul.tabs li a',
                    'property' => 'color',
                ],
            ],
        ]);

        // Active Tab Text Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('shop_product_tabs_active_text_color'),
            'label'       => __('Active Tab Text Color', Config::get('text_domain')),
            'default'     => 'var(--e-global-color-primary)',
            'transport'   => 'auto',
            'tab'         => 'design',
            'section'     => Config::prefix('shop_product'),
            'output'      => [
                [
                    'element'  => '.woocommerce div.product .woocommerce-tabs ul.tabs li.active a',
                    'property' => 'color',
                ],
            ],
        ]);

    }
}