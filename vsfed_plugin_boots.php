<?php
namespace VSFEDViewSocialFeeds;

define( "VSFED_ASFSK_ASSETS_PUBLIC_DIR_FILE", plugin_dir_url( __FILE__ ) . "assets/public" );
define( "VSFED_ASFSK_ASSETS_ADMIN_DIR_FILE", plugin_dir_url( __FILE__ ) . "assets/admin" );
class VSFEDViewSocialFeedsBWD {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function vsfed_admin_editor_scripts() {
		add_filter( 'script_loader_tag', [ $this, 'vsfed_admin_editor_scripts_as_a_module' ], 10, 2 );
	}

	public function vsfed_admin_editor_scripts_as_a_module( $tag, $handle ) {
		if ( 'vsfed_the_service_editor' === $handle ) {
			$tag = str_replace( '<script', '<script type="module"', $tag );
		}
		return $tag;
	}

    public function instafeed_render_items($settings = null)
    {
        // check if ajax request
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'instafeed_load_more') {
            $ajax = wp_doing_ajax();
            // check ajax referer
            check_ajax_referer('view-social-feeds', 'security');

            // init vars
            $page = isset($_POST['page']) ? intval($_REQUEST['page'], 10) : 0;
            if (!empty($_POST['post_id'])) {
                $post_id = intval($_POST['post_id'], 10);
            } else {
                $err_msg = __('Post ID is missing', 'view-social-feeds');
                if ($ajax) {
                    wp_send_json_error($err_msg);
                }
                return false;
            }

            if (!empty($_POST['widget_id'])) {
                $widget_id = sanitize_text_field($_POST['widget_id']);
            } else {
                $err_msg = __('Widget ID is missing', 'view-social-feeds');
                if ($ajax) {
                    wp_send_json_error($err_msg);
                }
                return false;
            }
            $settings = HelperClass::vsfedi_get_widget_settings($post_id, $widget_id);

	        if ( ! empty ( $_POST['settings'] ) ) {
		        parse_str( $_POST['settings'], $new_settings );
		        $settings = wp_parse_args( $new_settings, $settings );
	        }

        } else {
            // init vars
            $page = 0;
            $settings = !empty($settings) ? $settings : $this->get_settings_for_display();
        }

        $key = 'vsfedi_instafeed_'.md5(str_replace('.', '_', $settings['vsfedi_instafeed_access_token']).$settings['vsfedi_instafeed_data_cache_limit']);
        $html = '';

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'instafeed_load_more') {
            if($instagram_data = get_transient($key)){
                $instagram_data = json_decode($instagram_data, true);
                if ( ($page * $settings['vsfedi_instafeed_image_count']['size'] >= count($instagram_data['data'])) && !empty($instagram_data['paging']['next']) ) {
                    $request_args = array(
                        'timeout' => 60,
                    );
                    $instagram_data_new = wp_remote_retrieve_body(wp_remote_get($instagram_data['paging']['next'],
                        $request_args));
                    $instagram_data_new = json_decode($instagram_data_new, true);
                    if (!empty($instagram_data_new['data'])) {
                        $instagram_data['data'] = array_merge($instagram_data['data'], $instagram_data_new['data']);
                        $new_paging['paging'] = !empty($instagram_data_new['paging']['next']) ? $instagram_data_new['paging']: '';
                        $instagram_data = array_merge($instagram_data, $new_paging);
                        $instagram_data = json_encode($instagram_data);
                        set_transient($key, $instagram_data, 1800);
                    }
                }
            }
        }

        if (get_transient($key) === false) {
            $request_args = array(
                'timeout' => 60,
            );
            $instagram_data = wp_remote_retrieve_body(wp_remote_get('https://graph.instagram.com/me/media/?fields=username,id,caption,media_type,media_url,permalink,thumbnail_url,timestamp&limit=500&access_token=' . $settings['vsfedi_instafeed_access_token'],
                $request_args));
            $data_check = json_decode($instagram_data, true);
            if (!empty($data_check['data'])) {
                set_transient($key, $instagram_data, ($settings['vsfedi_instafeed_data_cache_limit'] * MINUTE_IN_SECONDS));
            }
        } else {
            $instagram_data = get_transient($key);
        }

        $instagram_data = json_decode($instagram_data, true);
        
        if (empty($instagram_data['data'])) {
            return;
        }

        if (empty($settings['vsfedi_instafeed_image_count']['size'])) {
            return;
        }

        switch ($settings['vsfedi_instafeed_sort_by']) {
            case 'most-recent':
                usort($instagram_data['data'], function ($a, $b) {
                    return (int)(strtotime($a['timestamp']) < strtotime($b['timestamp']));
                });
                break;

            case 'least-recent':
                usort($instagram_data['data'], function ($a, $b) {
                    return (int)(strtotime($a['timestamp']) > strtotime($b['timestamp']));
                });
                break;
        }

        if ($items = $instagram_data['data']) {
            $items = array_splice($items, ($page * $settings['vsfedi_instafeed_image_count']['size']),
                $settings['vsfedi_instafeed_image_count']['size']);

            foreach ($items as $item) {
                $img_alt_posted_by = !empty($item['username']) ? $item['username'] : '-';
                $img_alt_content = __('Photo by ', 'view-social-feeds') . $img_alt_posted_by;

                if ('yes' === $settings['vsfedi_instafeed_link']) {
                    $target = ($settings['vsfedi_instafeed_link_target']) ? 'target=_blank' : 'target=_self';
                } else {
                    $item['permalink'] = '#';
                    $target = '';
                }

                $image_src = ($item['media_type'] == 'VIDEO') ? $item['thumbnail_url'] : $item['media_url'];
                $caption_length = ( ! empty( $settings['vsfedi_instafeed_caption_length'] ) & $settings['vsfedi_instafeed_caption_length'] > 0 )  ? $settings['vsfedi_instafeed_caption_length'] : 60;
                // echo $item['media_url'].'<br>';
                if ($settings['vsfedi_instafeed_layout'] == 'overlay') {
                    $html .= '<a href="' . $item['permalink'] . '" ' . esc_attr($target) . ' class="vsfedi-instafeed-item">
                        <div class="vsfedi-instafeed-item-inner">
                            <img alt="' . $img_alt_content . '" class="vsfedi-instafeed-img" src="' . $image_src . '">

                            <div class="vsfedi-instafeed-caption">
                                <div class="vsfedi-instafeed-caption-inner">';
                    if ($settings['vsfedi_instafeed_overlay_style'] == 'simple' || $settings['vsfedi_instafeed_overlay_style'] == 'standard') {
                        $html .= '<div class="vsfedi-instafeed-icon">
                                            <i class="fab fa-instagram" aria-hidden="true"></i>
                                        </div>';
                    } else {
                        if ($settings['vsfedi_instafeed_overlay_style'] == 'basic') {
                            if ($settings['vsfedi_instafeed_caption'] && !empty($item['caption'])) {
                                $html .= '<p class="vsfedi-instafeed-caption-text">' . substr( $item['caption'], 0, intval( $caption_length ) ) . '...</p>';
                            }
                        }
                    }

                    $html .= '<div class="vsfedi-instafeed-meta">';
                    if ($settings['vsfedi_instafeed_overlay_style'] == 'basic' && $settings['vsfedi_instafeed_date']) {
                        $html .= '<span class="vsfedi-instafeed-post-time"><i class="far fa-clock" aria-hidden="true"></i> ' . date("d M Y",
                            strtotime($item['timestamp'])) . '</span>';
                    }
                    if ($settings['vsfedi_instafeed_overlay_style'] == 'standard') {
                        if ($settings['vsfedi_instafeed_caption'] && !empty($item['caption'])) {
                            $html .= '<p class="vsfedi-instafeed-caption-text">' . substr( $item['caption'], 0, intval( $caption_length ) ) . '...</p>';
                        }
                    }
                    $html .= '</div>';
                    $html .= '</div>
                            </div>
                        </div>
                    </a>';
                } else {

                    $html .= '<div class="vsfedi-instafeed-item">
                        <div class="vsfedi-instafeed-item-inner">
                            <header class="vsfedi-instafeed-item-header clearfix">
                               <div class="vsfedi-instafeed-item-user clearfix">';
                    if ($settings['vsfedi_instafeed_show_profile_image'] == 'yes' && !empty($settings['vsfedi_instafeed_profile_image']['url'])) {
                        $html .= '<a href="//www.instagram.com/' . $item['username'] . '"><img alt="' . $img_alt_content . '" src="' . $settings['vsfedi_instafeed_profile_image']['url'] . '" alt="' . $item['username'] . '" class="vsfedi-instafeed-avatar"></a>';
                    }
                    if ($settings['vsfedi_instafeed_show_username'] == 'yes' && !empty($settings['vsfedi_instafeed_username'])) {
                        $html .= '<a href="//www.instagram.com/' . $item['username'] . '"><p class="vsfedi-instafeed-username">' . $settings['vsfedi_instafeed_username'] . '</p></a>';
                    }

                    $html .= '</div>';
                    $html .= '<span class="vsfedi-instafeed-icon"><i class="fab fa-instagram" aria-hidden="true"></i></span>';

                    if ($settings['vsfedi_instafeed_date'] && $settings['vsfedi_instafeed_card_style'] == 'outer') {
                        $html .= '<span class="vsfedi-instafeed-post-time"><i class="far fa-clock" aria-hidden="true"></i> ' . date("d M Y",
                            strtotime($item['timestamp'])) . '</span>';
                    }
                    $html .= '</header>
                            <a href="' . $item['permalink'] . '" ' . esc_attr($target) . ' class="vsfedi-instafeed-item-content">
                                <img alt="' . $img_alt_content . '" class="vsfedi-instafeed-img" src="' . $image_src . '">';

                    if ($settings['vsfedi_instafeed_card_style'] == 'inner' && $settings['vsfedi_instafeed_caption'] && !empty($item['caption'])) {
                        $html .= '<div class="vsfedi-instafeed-caption">
                                        <div class="vsfedi-instafeed-caption-inner">
                                            <div class="vsfedi-instafeed-meta">
                                                <p class="vsfedi-instafeed-caption-text">' . substr( $item['caption'], 0, intval( $caption_length ) ) . '...</p>
                                            </div>
                                        </div>
                                    </div>';
                    }
                    $html .= '</a>
                            <footer class="vsfedi-instafeed-item-footer">
                                <div class="clearfix">';
                    if ($settings['vsfedi_instafeed_card_style'] == 'inner' && $settings['vsfedi_instafeed_date']) {
                        $html .= '<span class="vsfedi-instafeed-post-time"><i class="far fa-clock" aria-hidden="true"></i> ' . date("d M Y",
                            strtotime($item['timestamp'])) . '</span>';
                    }
                    $html .= '</div>';

                    if ($settings['vsfedi_instafeed_card_style'] == 'outer' && $settings['vsfedi_instafeed_caption'] && !empty($item['caption'])) {
                        $html .= '<p class="vsfedi-instafeed-caption-text">' . substr( $item['caption'], 0, intval( $caption_length ) ) . '...</p>';
                    }
                    $html .= '</footer>
                        </div>
                    </div>';
                }
            }
        }

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'instafeed_load_more') {
            $data = [
                'num_pages' => ceil(count($instagram_data['data']) / $settings['vsfedi_instafeed_image_count']['size']),
                'html' => $html,
            ];
            while (ob_get_status()) {
                ob_end_clean();
            }
            if (function_exists('gzencode')) {
                $response = gzencode(wp_json_encode($data));
                header('Content-Type: application/json; charset=utf-8');
                header('Content-Encoding: gzip');
                header('Content-Length: ' . strlen($response));

                echo $response;
            } else {
                wp_send_json($data);
            }
            wp_die();
        }
        return $html;
    }

	private function include_widgets_files() {
		require_once( __DIR__ . '/widgets/vsfed-products.php' );
	}

	public function vsfed_register_widgets() {
		$this->include_widgets_files();
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\VSFEDProducts() );
	}

	public function vsfed_add_elementor_widget_categories( $elements_manager ) {
		$elements_manager->add_category(
			'bwdvsfed_creative_products_category',
			[
				'title' => esc_html__( 'News Feeds', 'view-social-feeds' ),
				'icon' => 'eicon-person',
			]
		);
	}
    
	public function vsfed_all_assets_for_the_public(){
		$all_css_js = array(
            'vsfed_main_style' => array('vsfed_path_admin_define'=>VSFED_ASFSK_ASSETS_PUBLIC_DIR_FILE . '/css/instagram-gallery.css'),
        );
        foreach($all_css_js as $handle => $fileinfo){
            wp_enqueue_style( $handle, $fileinfo['vsfed_path_admin_define'], null, '1.0', 'all');
        }
	}
	public function vsfed_all_assets_for_elementor_editor_admin(){
		$all_css_js_file = array(
            'vsfed_products_admin_icon_css' => array('vsfed_path_admin_define'=>VSFED_ASFSK_ASSETS_ADMIN_DIR_FILE . '/icon.css'),
        );
        foreach($all_css_js_file as $handle => $fileinfo){
            wp_enqueue_style( $handle, $fileinfo['vsfed_path_admin_define'], null, '1.0', 'all');
        }
	}

	public function __construct() {
		
        add_action('wp_ajax_instafeed_load_more', [$this, 'instafeed_render_items']);
        add_action('wp_ajax_nopriv_instafeed_load_more', [$this, 'instafeed_render_items']);

		add_action('wp_enqueue_scripts', [$this, 'vsfed_all_assets_for_the_public']);
		add_action('elementor/editor/before_enqueue_scripts', [$this, 'vsfed_all_assets_for_elementor_editor_admin']);
		add_action( 'elementor/elements/categories_registered', [ $this, 'vsfed_add_elementor_widget_categories' ] );
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'vsfed_register_widgets' ] );
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'vsfed_admin_editor_scripts' ] );
	}
}

// Instantiate Plugin Class
VSFEDViewSocialFeedsBWD::instance();