<?php
namespace VSFEDViewSocialFeeds;
class VSFEDViewSocialFeedsBWD {
	private static $_instance = null;
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    public function instafeed_render_items($settings = null) {
        $settings = !empty($settings) ? $settings : $this->get_settings_for_display();

        // Perform rendering logic using $settings
        // Example:
        if (isset($settings['eael_instafeed_access_token'])) {
            $access_token = $settings['eael_instafeed_access_token'];
            // Use $access_token for further processing
            echo 'Access Token: ' . esc_html($access_token);
        } else {
            echo 'Access Token not provided.';
        }
    }
	public function vsfed_register_widgets() {
		require_once( __DIR__ . '/widgets/vsfed-products.php' );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\VSFEDProducts() );
	}
	function vsfed_add_elementor_widget_categories( $elements_manager ) {

		$elements_manager->add_category(
			'bwdvsfed_creative_products_category',
			[
				'title' => esc_html__( 'News Feeds', 'view-social-feeds' ),
				'icon' => 'eicon-person',
			]
		);
	}
	public function __construct() {
		add_action( 'elementor/elements/categories_registered', [ $this, 'vsfed_add_elementor_widget_categories' ] );
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'vsfed_register_widgets' ] );
	}
}
VSFEDViewSocialFeedsBWD::instance();
// without access modifire 'public' why the function is working here