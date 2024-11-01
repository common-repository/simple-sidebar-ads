<?php
// This file may not be executed directly
if (!defined('ABSPATH')) {
  exit();
}

add_action('init', array('WidgetAds', 'create_post_type'));
add_action('admin_init', array('WidgetAds', 'save_widget_ad_data'));
add_action('admin_init', array('WidgetAds', 'handle_bulk_actions'));
add_action('admin_menu', array('WidgetAds', 'add_widget_ads_menu'));
add_action('admin_enqueue_scripts', array('WidgetAds', 'load_scripts'));
add_action('wp_enqueue_scripts', array('WidgetAds', 'load_fe_scripts'));
add_action('admin_notices', array('WidgetAdsGUI', 'show_admin_notices'));
add_action('widgets_init', array('WidgetAds', 'register_widget_ad_widget'));
