<?php
namespace VSFEDViewSocialFeeds\Widgets;

use \Elementor\Controls_Manager;
use \Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class VSFEDProducts extends Widget_Base {
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

    protected function register_controls () {
        $this->start_controls_section(
            'eael_section_instafeed_settings_account',
            [
                'label' => esc_html__('Instagram Account Settings', 'view-social-feeds'),
            ]
        );

        $this->add_control(
            'eael_instafeed_access_token',
            [
                'label'       => esc_html__('Access Token', 'view-social-feeds'),
                'type'        => Controls_Manager::TEXT,
                'ai' => [
					'active' => false,
				],
                'label_block' => true,
                'view-social-feeds',
            ]
        );
        $this->end_controls_section();
    }

    protected function render () {
        $settings = $this->get_settings_for_display();
		echo 'Form render';
        // Instantiate VSFEDViewSocialFeedsBWD class
        $vsfed_bwd = new \VSFEDViewSocialFeeds\VSFEDViewSocialFeedsBWD();

        // Call instafeed_render_items() with settings
        $vsfed_bwd->instafeed_render_items($settings);
    }
}
//this is a widget file. in this widget file why not working the function instafeed_render_items and how can i work it in here