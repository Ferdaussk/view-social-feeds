<?php
namespace VSFEDViewSocialFeeds\Widgets;

use \Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use Elementor\Plugin;
use \Elementor\Utils;
use \Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class VSFEDProducts extends Widget_Base {
    // use \Essential_Addons_Elementor\Pro\Traits\Instagram_Feed;
	// use \VSFEDViewSocialFeeds\VSFEDProducts;

	public function get_name() {
		return esc_html__('InstagramNewsFeeds');
	}

	public function get_title() {
		return esc_html__( 'Instagram News Feeds', 'view-social-feeds' );
	}

	public function get_icon() {
		return 'vsfed-products-icon eicon-products';
	}

	public function get_categories() {
		return [ 'bwdvsfed_creative_products_category' ];
	}

    public function get_keywords () {
        return [
            'instagram',
            'instagram feed',
            'bwd instagram feed',
            'instagram gallery',
            'bwd instagram gallery',
            'social media',
            'social feed',
            'bwd social feed',
            'instagram embed',
            'bwd',
            'social news feed'
        ];
    }

    public function get_style_depends () {
        return [
            'font-awesome-5-all',
            'font-awesome-4-shim',
        ];
    }

    public function get_script_depends () {
        return [
            'font-awesome-4-shim'
        ];
    }

    protected function register_controls () {
        $this->start_controls_section(
            'vsfedi_section_instafeed_settings_account',
            [
                'label' => esc_html__('Instagram Account Settings', 'view-social-feeds'),
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_access_token',
            [
                'label'       => esc_html__('Access Token', 'view-social-feeds'),
                'type'        => Controls_Manager::TEXT,
                'ai' => [
					'active' => false,
				],
                'label_block' => true,
                'description' => '<a href="http://bestwpdeveloper.com/.com/instagram-feed/" class="vsfedi-btn" target="_blank">Add Access Token</a>',
                'view-social-feeds',
            ]
        );

	    $this->add_control(
		    'vsfedi_instafeed_data_cache_limit',
		    [
			    'label' => __('Data Cache Time', 'view-social-feeds'),
			    'type' => Controls_Manager::NUMBER,
			    'min' => 1,
			    'default' => 60,
			    'description' => __('Cache expiration time (Minutes)', 'view-social-feeds')
		    ]
	    );

        $this->end_controls_section();

        $this->start_controls_section(
            'vsfedi_section_instafeed_settings_content',
            [
                'label' => esc_html__('Feed Settings', 'view-social-feeds'),
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_sort_by',
            [
                'label'   => esc_html__('Sort By', 'view-social-feeds'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none'         => esc_html__('None', 'view-social-feeds'),
                    'most-recent'  => esc_html__('Most Recent', 'view-social-feeds'),
                    'least-recent' => esc_html__('Least Recent', 'view-social-feeds'),
//                    'most-liked' => esc_html__('Most Likes', 'view-social-feeds'),
//                    'least-liked' => esc_html__('Least Likes', 'view-social-feeds'),
//                    'most-commented' => esc_html__('Most Commented', 'view-social-feeds'),
//                    'least-commented' => esc_html__('Least Commented', 'view-social-feeds'),
                ],
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_image_count',
            [
                'label'   => esc_html__('Max Visible Images', 'view-social-feeds'),
                'type'    => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 12,
                ],
                'range'   => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_caption_length',
            [
                'label'   => esc_html__('Max Caption Length', 'view-social-feeds'),
                'type'    => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 2000,
			    'default' => 60,
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_force_square',
            [
                'label'        => esc_html__('Force Square Image?', 'view-social-feeds'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => '',
            ]
        );

	    $this->add_control(
		    'vsfedi_instafeed_force_square_type',
		    [
			    'label'     => esc_html__( 'Image Render Type', 'view-social-feeds' ),
			    'type'      => Controls_Manager::SELECT,
			    'default'   => 'fill',
			    'options'   => [
				    'fill'  => esc_html__( 'Stretched', 'view-social-feeds' ),
				    'cover' => esc_html__( 'Cropped', 'view-social-feeds' ),
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .vsfedi-instafeed-square-img .vsfedi-instafeed-item img' => 'object-fit: {{VALUE}};',
			    ],
			    'condition' => [
				    'vsfedi_instafeed_force_square' => 'yes',
			    ],
		    ]
	    );

        $this->add_responsive_control(
            'vsfedi_instafeed_sq_image_size',
            [
                'label'     => esc_html__('Image Dimension (px)', 'view-social-feeds'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 280,
                ],
                'range'     => [
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .vsfedi-instafeed-square-img .vsfedi-instafeed-item img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'vsfedi_instafeed_force_square' => 'yes',
                ],
            ]
        );
        
        $this->end_controls_section();

        $this->start_controls_section(
            'vsfedi_section_instafeed_settings_general',
            [
                'label' => esc_html__('General Settings', 'view-social-feeds'),
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_layout',
            [
                'label'   => esc_html__('Layout', 'view-social-feeds'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'overlay',
                'options' => [
                    'card'    => esc_html__('Card', 'view-social-feeds'),
                    'overlay' => esc_html__('Overlay', 'view-social-feeds'),
                ],
            ]
        );


        $this->add_control(
            'vsfedi_instafeed_card_style',
            [
                'label'     => esc_html__('Card Style', 'view-social-feeds'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'outer',
                'options'   => [
                    'inner' => esc_html__('Content Inner', 'view-social-feeds'),
                    'outer' => esc_html__('Content Outer', 'view-social-feeds'),
                ],
                'condition' => [
                    'vsfedi_instafeed_layout' => 'card',
                ],
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_overlay_style',
            [
                'label'     => esc_html__('Overlay Style', 'view-social-feeds'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'simple',
                'options'   => [
                    'simple'   => esc_html__('Simple', 'view-social-feeds'),
                    'basic'    => esc_html__('Basic', 'view-social-feeds'),
                    'standard' => esc_html__('Standard', 'view-social-feeds'),
                ],
                'condition' => [
                    'vsfedi_instafeed_layout' => 'overlay',
                ],
            ]
        );

        $this->add_responsive_control(
            'vsfedi_instafeed_columns',
            [
                'label'        => esc_html__('Number of Columns', 'view-social-feeds'),
                'type'         => Controls_Manager::SELECT,
                'default'      => 'vsfedi-col-4',
                'options'      => [
                    'vsfedi-col-1' => esc_html__('1 Column', 'view-social-feeds'),
                    'vsfedi-col-2' => esc_html__('2 Columns', 'view-social-feeds'),
                    'vsfedi-col-3' => esc_html__('3 Columns', 'view-social-feeds'),
                    'vsfedi-col-4' => esc_html__('4 Columns', 'view-social-feeds'),
                    'vsfedi-col-5' => esc_html__('5 Columns', 'view-social-feeds'),
                    'vsfedi-col-6' => esc_html__('6 Columns', 'view-social-feeds'),
                ],
                'prefix_class' => 'instafeed-gallery%s-',
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_user_info',
            [
                'label'     => esc_html__('User Info', 'view-social-feeds'),
                'type'      => Controls_Manager::HEADING,
                'condition' => [
                    'vsfedi_instafeed_layout' => 'card',
                ],
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_show_profile_image',
            [
                'label'        => esc_html__('Show Profile Image', 'view-social-feeds'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
                'condition'    => [
                    'vsfedi_instafeed_layout' => 'card',
                ],
            ]
        );
        $this->add_control(
            'vsfedi_instafeed_profile_image',
            [
                'label'     => esc_html__('Profile Image', 'view-social-feeds'),
                'type'      => Controls_Manager::MEDIA,
                'default'   => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'vsfedi_instafeed_show_profile_image' => 'yes',
                    'vsfedi_instafeed_layout'             => 'card',
                ],
                'ai' => [
                    'active' => false,
                ],
            ]
        );


        $this->add_control(
            'vsfedi_instafeed_show_username',
            [
                'label'        => esc_html__('Show Username', 'view-social-feeds'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
                'condition'    => [
                    'vsfedi_instafeed_layout' => 'card',
                ],
            ]
        );
        $this->add_control(
            'vsfedi_instafeed_username',
            [
                'label'     => esc_html__('Username', 'view-social-feeds'),
                'type'      => Controls_Manager::TEXT,
                'dynamic' => [ 'active' => true ],
                'default'   => __('Essential Addons', 'view-social-feeds'),
                'condition' => [
                    'vsfedi_instafeed_show_username' => 'yes',
                    'vsfedi_instafeed_layout'        => 'card',
                ],
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_pagination_heading',
            [
                'label' => __('Pagination', 'view-social-feeds'),
                'type'  => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_pagination',
            [
                'label'        => esc_html__('Enable Load More?', 'view-social-feeds'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => '',
            ]
        );

        $this->add_control(
            'loadmore_text',
            [
                'label'     => __('Label', 'view-social-feeds'),
                'type'      => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default'   => __('Load More', 'view-social-feeds'),
                'condition' => [
                    'vsfedi_instafeed_pagination' => 'yes'
                ],
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_caption_heading',
            [
                'label' => __('Link & Content', 'view-social-feeds'),
                'type'  => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_caption',
            [
                'label'        => esc_html__('Display Caption', 'view-social-feeds'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_date',
            [
                'label'        => esc_html__('Display Date', 'view-social-feeds'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_link',
            [
                'label'        => esc_html__('Enable Link', 'view-social-feeds'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_link_target',
            [
                'label'        => esc_html__('Open in new window?', 'view-social-feeds'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
                'condition'    => [
                    'vsfedi_instafeed_link' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'vsfedi_section_instafeed_styles_general',
            [
                'label' => esc_html__('Instagram Feed Styles', 'view-social-feeds'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'vsfedi_instafeed_spacing',
            [
                'label'      => esc_html__('Padding Between Images', 'view-social-feeds'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .vsfedi-instafeed-item-inner' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'vsfedi_instafeed_box_border',
                'label'    => esc_html__('Border', 'view-social-feeds'),
                'selector' => '{{WRAPPER}} .vsfedi-instafeed-item-inner',
            ]
        );

	    $this->add_control(
		    'vsfedi_instafeed_box_border_radius',
		    [
			    'label'     => esc_html__( 'Border Radius', 'view-social-feeds' ),
			    'type'      => Controls_Manager::DIMENSIONS,
			    'selectors' => [
				    '{{WRAPPER}} .vsfedi-instafeed-item-inner' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px; overflow: hidden;',
			    ],
		    ]
	    );

        $this->end_controls_section();

        $this->start_controls_section(
            'vsfedi_section_instafeed_styles_content',
            [
                'label' => esc_html__('Color &amp; Typography', 'view-social-feeds'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_overlay_color',
            [
                'label'     => esc_html__('Overlay Color', 'view-social-feeds'),
                'type'      => Controls_Manager::COLOR,
                'default'   => 'rgba(137,12,255,0.75)',
                'selectors' => [
                    '{{WRAPPER}} .vsfedi-instafeed-caption' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_like_comments_heading',
            [
                'label' => __('Icon Styles', 'view-social-feeds'),
                'type'  => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_icon_color',
            [
                'label'     => esc_html__('Icon Color', 'view-social-feeds'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .vsfedi-instafeed-caption i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_caption_style_heading',
            [
                'label' => __('Caption Styles', 'view-social-feeds'),
                'type'  => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_caption_color',
            [
                'label'     => esc_html__('Caption Color', 'view-social-feeds'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .vsfedi-instafeed-caption,
                    {{WRAPPER}} .vsfedi-instafeed-caption-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'vsfedi_instafeed_caption_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
                ],
                'selector' => '{{WRAPPER}} .vsfedi-instafeed-caption, {{WRAPPER}} .vsfedi-instafeed-caption-text',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'vsfedi_section_load_more_btn',
            [
                'label' => __('Load More Button Style', 'view-social-feeds'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'vsfedi_instafeed_load_more_btn_padding',
            [
                'label'      => esc_html__('Padding', 'view-social-feeds'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .vsfedi-load-more-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'vsfedi_instafeed_load_more_btn_margin',
            [
                'label'      => esc_html__('Margin', 'view-social-feeds'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .vsfedi-load-more-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'vsfedi_instafeed_load_more_btn_typography',
                'selector' => '{{WRAPPER}} .vsfedi-load-more-button',
            ]
        );

        $this->start_controls_tabs('vsfedi_instafeed_load_more_btn_tabs');

        // Normal State Tab
        $this->start_controls_tab('vsfedi_instafeed_load_more_btn_normal',
            ['label' => esc_html__('Normal', 'view-social-feeds')]);

        $this->add_control(
            'vsfedi_instafeed_load_more_btn_normal_text_color',
            [
                'label'     => esc_html__('Text Color', 'view-social-feeds'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .vsfedi-load-more-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'vsfedi_cta_btn_normal_bg_color',
            [
                'label'     => esc_html__('Background Color', 'view-social-feeds'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#29d8d8',
                'selectors' => [
                    '{{WRAPPER}} .vsfedi-load-more-button' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'vsfedi_instafeed_load_more_btn_normal_border',
                'label'    => esc_html__('Border', 'view-social-feeds'),
                'selector' => '{{WRAPPER}} .vsfedi-load-more-button',
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_load_more_btn_border_radius',
            [
                'label'     => esc_html__('Border Radius', 'view-social-feeds'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .vsfedi-load-more-button' => 'border-radius: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'      => 'vsfedi_instafeed_load_more_btn_shadow',
                'selector'  => '{{WRAPPER}} .vsfedi-load-more-button',
                'separator' => 'before',
            ]
        );

        $this->end_controls_tab();

        // Hover State Tab
        $this->start_controls_tab('vsfedi_instafeed_load_more_btn_hover',
            ['label' => esc_html__('Hover', 'view-social-feeds')]);

        $this->add_control(
            'vsfedi_instafeed_load_more_btn_hover_text_color',
            [
                'label'     => esc_html__('Text Color', 'view-social-feeds'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .vsfedi-load-more-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_load_more_btn_hover_bg_color',
            [
                'label'     => esc_html__('Background Color', 'view-social-feeds'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#27bdbd',
                'selectors' => [
                    '{{WRAPPER}} .vsfedi-load-more-button:hover' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'vsfedi_instafeed_load_more_btn_hover_border_color',
            [
                'label'     => esc_html__('Border Color', 'view-social-feeds'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .vsfedi-load-more-button:hover' => 'border-color: {{VALUE}};',
                ],
            ]

        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'      => 'vsfedi_instafeed_load_more_btn_hover_shadow',
                'selector'  => '{{WRAPPER}} .vsfedi-load-more-button:hover',
                'separator' => 'before',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function render () {
        $settings = $this->get_settings_for_display();
        $layout = isset($settings['vsfedi_instafeed_layout']) ? $settings['vsfedi_instafeed_layout'] : '';
        $post_id = 0;
        if (Plugin::$instance->documents->get_current()) {
            $post_id = Plugin::$instance->documents->get_current()->get_main_id();
        }
        $this->add_render_attribute('insta-wrap', [
            'class' => [
                "vsfedi-instafeed",
                'vsfedi-instafeed-'.$layout,
                'vsfedi-instafeed-'.$layout.'-'.$settings["vsfedi_instafeed_{$layout}_style"]
            ],
            'id' => "vsfedi-instafeed-".$this->get_id(),
        ]);

        if ($settings['vsfedi_instafeed_force_square']=='yes'){
            $this->add_render_attribute('insta-wrap', 'class',"vsfedi-instafeed-square-img");
        }

        $this->add_render_attribute('load-more', [
            'class' => "vsfedi-load-more-button",
            'id' => "vsfedi-load-more-btn-" . $this->get_id(),
            'data-widget-id' => $this->get_id(),
            'data-post-id' => $post_id,
            'data-page' => 1,
        ]);

	    if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
		    $this->add_render_attribute( 'load-more', [
			    'data-settings' => http_build_query( [
				    'vsfedi_instafeed_access_token'       => $settings['vsfedi_instafeed_access_token'],
				    'vsfedi_instafeed_data_cache_limit'   => $settings['vsfedi_instafeed_data_cache_limit'],
				    'vsfedi_instafeed_image_count'        => $settings['vsfedi_instafeed_image_count'],
				    'vsfedi_instafeed_caption_length'        => ! empty( $settings['vsfedi_instafeed_caption_length'] ) ? $settings['vsfedi_instafeed_caption_length'] : 60,
				    'vsfedi_instafeed_sort_by'            => $settings['vsfedi_instafeed_sort_by'],
				    'vsfedi_instafeed_link'               => $settings['vsfedi_instafeed_link'],
				    'vsfedi_instafeed_link_target'        => $settings['vsfedi_instafeed_link_target'],
				    'vsfedi_instafeed_layout'             => $settings['vsfedi_instafeed_layout'],
				    'vsfedi_instafeed_overlay_style'      => $settings['vsfedi_instafeed_overlay_style'],
				    'vsfedi_instafeed_caption'            => $settings['vsfedi_instafeed_caption'],
				    'vsfedi_instafeed_date'               => $settings['vsfedi_instafeed_date'],
				    'vsfedi_instafeed_show_profile_image' => $settings['vsfedi_instafeed_show_profile_image'],
				    'vsfedi_instafeed_profile_image'      => $settings['vsfedi_instafeed_profile_image'],
				    'vsfedi_instafeed_show_username'      => $settings['vsfedi_instafeed_show_username'],
				    'vsfedi_instafeed_username'           => $settings['vsfedi_instafeed_username'],
				    'vsfedi_instafeed_card_style'         => $settings['vsfedi_instafeed_card_style'],
			    ] )
		    ] );
	    }
        ?>
        <div <?php $this->print_render_attribute_string('insta-wrap'); ?>>
            <?php 
            $vsfed_bwd = new \VSFEDViewSocialFeeds\VSFEDViewSocialFeedsBWD();
            echo $vsfed_bwd->instafeed_render_items($settings); 
            ?>
        </div>
        <div class="clearfix"></div>

        <?php
        if (($settings['vsfedi_instafeed_pagination'] == 'yes')) { ?>
            <div class="vsfedi-load-more-button-wrap">
                <button <?php $this->print_render_attribute_string('load-more'); ?>>
                    <div class="vsfedi-btn-loader button__loader"></div>
                    <span><?php echo esc_html($settings['loadmore_text']); ?></span>
                </button>
            </div>
            <?php
        }

        if (Plugin::instance()->editor->is_edit_mode()) {
            echo '<script type="text/javascript">
                jQuery(document).ready(function($) {
                    $(".vsfedi-instafeed").each(function() {
                        var $node_id = "'.$this->get_id().'",
                        $gallery = $(this),
                        $scope = $(".elementor-element-"+$node_id+""),
                        $settings = {
                            itemSelector: ".vsfedi-instafeed-item",
                            percentPosition: true,
                            masonry: {
                                columnWidth: ".vsfedi-instafeed-item",
                            }
                        };
                        
                        // init isotope
                        $instagram_gallery = $(".vsfedi-instafeed", $scope).isotope($settings);
                    
                        // layout gal, while images are loading
                        $instagram_gallery.imagesLoaded().progress(function() {
                            $instagram_gallery.isotope("layout");
                        });

                        $(".vsfedi-instafeed-item", $gallery).resize(function() {
                            $instagram_gallery.isotope("layout");
                        });
                    });
                });
            </script>';
        }
    }
}
