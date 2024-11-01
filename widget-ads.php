<?php
/**
 * Plugin Name: Simple Sidebar Ads
 * Description: Place image and text ads easily in widget positions. Just install and set your image, text and link. Then use the widget to put the ad wherever you want. 
 * Version: 1.1.0
 * Author: Simple Sidebar Ads
 * Author URI: http://simplesidebarads.com
 */

// This file may not be executed directly
if (!defined('ABSPATH')) {
  exit();
}

if (!class_exists('WidgetAds')):

  final class WidgetAds
  {
    private static $instance;

    public static function instantiate()
    {
  		if (!isset(self::$instance) && !self::$instance instanceof WidgetAds) {
  			self::$instance = new WidgetAds;
  			self::$instance->includes();
  		}
  		return self::$instance;
    }

    public function includes()
    {

      if (!defined('WIDGETADS_PATH')) {
			  define('WIDGETADS_PATH', plugin_dir_path( __FILE__ ));
		  }

      if (!defined('WIDGETADS_URL')) {
        define('WIDGETADS_URL', plugins_url('', __FILE__));
      }

      require_once WIDGETADS_PATH . 'WidgetAd.php';
      require_once WIDGETADS_PATH . 'WidgetAdsGUI.php';
      require_once WIDGETADS_PATH . 'WidgetAdsWidget.php';
      require_once WIDGETADS_PATH . 'hooks.php';
    }


    // Static functions

    public static function load_scripts()
    {
      $screen = get_current_screen();

      if (isset($_GET['page']) && ($_GET['page'] == 'wads_menu' || $_GET['page'] == 'wads_menu_add_new')) {
        if ($_GET['page'] == 'wads_menu_add_new') {
          wp_enqueue_media();
        }

        wp_enqueue_style('wads-style', WIDGETADS_URL . '/css/admin-stylesheet.css');
        wp_enqueue_script('wads-script', WIDGETADS_URL . '/js/admin-script.js', array('jquery'));
      } elseif (isset($screen->id) && $screen->id === 'widgets') {
        wp_enqueue_style('wads-style', WIDGETADS_URL . '/css/admin-stylesheet.css');
      }

    }

    public static function load_fe_scripts()
    {
      wp_enqueue_style('wads-style', WIDGETADS_URL . '/css/frontend-stylesheet.css');
    }

    public static function create_post_type()
    {
      register_post_type('widget_ad',
        array(
          'labels' => array(
            'name' => __('Widget Ads'),
            'singular_name' => __('Widget Ad'),
            'add_new_item' => __('Add New Widget Ad')
          ),
          'public' => false,
          'has_archive' => false,
          'exclude_from_search' => true,
          'publicly_queryable' => false,
          'show_ui' => false,
          'show_in_nav_menus' => false,
          'show_in_menu' => false,
          'show_in_admin_bar' => false,
          'supports' => false
        )
      );
    }

    public static function add_widget_ads_menu()
    {
      add_menu_page('Widget Ads', 'Widget Ads', 'manage_options', 'wads_menu', array('WidgetAdsGUI', 'display_menu_page'));
      add_submenu_page('wads_menu', 'Add New Widget Ad', 'Add New', 'manage_options', 'wads_menu_add_new', array('WidgetAdsGUI', 'display_submenu_page'));
    }

    public static function handle_bulk_actions()
    {
      if (isset($_POST['wads_action']) && $_POST['wads_action'] == 'delete') {

        if (!wp_verify_nonce($_POST['widget_ads_list_nonce'], 'widget_ads_list_nonce_field')) {
          return;
        }

        if (isset($_POST['widgetads']) && is_array($_POST['widgetads']) && count($_POST['widgetads']) > 0) {
          foreach($_POST['widgetads'] as $widgetad) {
            wp_delete_post(intval($widgetad), true);
          }
          WidgetAdsGUI::add_notice(
            array("updated", "Widget Ads Deleted")
          );
        }
      }
    }

    public static function save_widget_ad_data()
    {
      if (!isset($_POST['wads_save'])) {
        return;
      }

       if (!isset($_POST['widget_ads_nonce'])) {
		       return;
	      }

	     if (!wp_verify_nonce($_POST['widget_ads_nonce'], 'widget_ads_meta_nonce_field')) {
		     return;
	     }

        if (!isset($_POST['wads_url'])) {
          WidgetAdsGUI::add_notice(
            array("error", "Failed to Save Widget Ad: a valid link URL is required")
          );
          return;
        }

        $linkURL = esc_url_raw($_POST['wads_url']);

        if (empty($linkURL) || !filter_var($linkURL, FILTER_VALIDATE_URL)) {
          WidgetAdsGUI::add_notice(
            array("error", "Failed to Save Widget Ad: a valid link URL is required")
          );
          return;
        }

        if (isset($_POST['wads_attachment_id']) && !empty($_POST['wads_attachment_id'])) {
          // Image is selected

          $attachmentID = intval($_POST['wads_attachment_id']);

          if ($attachmentID < 0) {
            WidgetAdsGUI::add_notice(
              array("error", "Failed to Save Widget Ad: invalid attachment ID provided")
            );
            return;
          }

          $attachmentMimeType = get_post_mime_type($attachmentID);

          if ($attachmentMimeType === false || substr($attachmentMimeType, 0, 5) !== 'image') {
            WidgetAdsGUI::add_notice(
              array("error", "Failed to Save Widget Ad: chosen attachment is not an image")
            );
            return;
          }

          if (!isset($_POST['wads_image_width']) || empty($_POST['wads_image_width'])
              || !isset($_POST['wads_image_height']) || empty($_POST['wads_image_height'])) {
            WidgetAdsGUI::add_notice(
              array("error", "Failed to Save Widget Ad: image dimensions are required and must be greater than zero")
            );
            return;
          }

          $dimensions = array();
          $dimensions[0] = floatval($_POST['wads_image_width']);
          $dimensions[1] = floatval($_POST['wads_image_height']);

          if (empty($dimensions[0]) || empty($dimensions[1])
              || $dimensions[0] < 0 || $dimensions[1] < 0) {
            WidgetAdsGUI::add_notice(
              array("error", "Failed to Save Widget Ad: image dimensions are required and must be greater than zero")
            );
            return;
          }

        }

        $topText = (isset($_POST['wads_top_text'])) ? sanitize_text_field($_POST['wads_top_text']): '';
        $bottomText = (isset($_POST['wads_bottom_text'])) ? sanitize_text_field($_POST['wads_bottom_text']): '';
        $overlayText = (isset($_POST['wads_overlay_text'])) ? sanitize_text_field($_POST['wads_overlay_text']): '';

        if (isset($_POST['wads_id'])) {
          $widgetAd = new WidgetAd(intval($_POST['wads_id']));
        } else {
          $widgetAd = new WidgetAd();
        }

        $widgetAd->setLink($linkURL);
        if (isset($attachmentID)) {
          $widgetAd->setImage($attachmentID);
          $widgetAd->setImageDimensions($dimensions);
          if (!empty($overlayText)) {
            $widgetAd->setOverlayText($overlayText);
          }
        } elseif (empty($topText) && empty($bottomText) && empty($overlayText)) {
          WidgetAdsGUI::add_notice(
            array("error", "Failed to Save Widget Ad: a widget ad must have top/bottom text or an image, or both")
          );
          return;
        }

        if ($widgetAd->getID() > 0 && !isset($attachmentID)) {
          $widgetAd->setImage(0);
          $widgetAd->setImageDimensions(array());
        }

        if (!empty($topText)) {
          $widgetAd->setTopText($topText);
        }
        if (!empty($bottomText)) {
          $widgetAd->setBottomText($bottomText);
        }

        if ($widgetAd->save()) {
          $location = admin_url('admin.php?page=wads_menu_add_new');
          $location = add_query_arg('adid', strval($widgetAd->getID()), $location);
          $location = add_query_arg('just_added', 'yes', $location);
          wp_safe_redirect($location);
        } else {
          WidgetAdsGUI::add_notice(
            array("error", "Failed to Save Widget Ad: unknown error occurred")
          );
        }
    }

    public static function register_widget_ad_widget()
    {
      register_widget('WidgetAdsWidget');
    }

  }

  function WidgetAds_start()
  {
    return WidgetAds::instantiate();
  }

  WidgetAds_start();

endif;

?>
