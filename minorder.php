<?php

// Attempt to prevent data leaks
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

/**
* Plugin Name: Simple WooCommerce Minimum Order
* Plugin URI: https://github.com/Baelx/simple-woocommerce-min-order
* Description: A dead-simple minimum order tab for your Woocommerce store. Go to your Woocommerce settings section to find the Simple Minimum Order tab.
* Author: Alexander Wintschel
* Author URI: http://alexwintschel.com
* Version: 1.0
*/

class WC_Min_Order {
  /**
  * Bootstraps the class and hooks required actions & filters.
  */
  public static function init() {
    add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
    add_action( 'woocommerce_settings_tabs_settings_tab_demo', __CLASS__ . '::settings_tab' );
    add_action( 'woocommerce_update_options_settings_tab_demo', __CLASS__ . '::update_settings' );
    add_action( 'woocommerce_checkout_process', 'wc_minimum_order_amount' );
    add_action( 'woocommerce_before_cart' , 'wc_minimum_order_amount' );
  }

  /**
  * Set a minimum order amount for checkout
  */
  public static function wc_minimum_order_amount() {

    /**
    * WP sanitize_text_field() function which:
    * Checks for invalid UTF-8 (uses wp_check_invalid_utf8())
    * Converts single < characters to entity
    * Strips all tags
    * Remove line breaks, tabs and extra white space
    * Strip octets
    */
    $min_order_field_input = sanitize_text_field(get_option('wc_min_order_field', 1));


    /**
    * Sets value to min order field and ensures type is integer
    */
    $minimum = $min_order_field_input;

    if ( WC()->cart->total < $minimum ) {

      if( is_cart() ) {

        wc_print_notice(
          sprintf( 'Your current order total is %s — you must have an order with a minimum of %s to place your order ' ,
          wc_price( WC()->cart->total ),
          wc_price( $minimum )
        ), 'error'
      );

    } else {

      wc_add_notice(
        sprintf( 'Your current order total is %s — you must have an order with a minimum of %s to place your order' ,
        wc_price( WC()->cart->total ),
        wc_price( $minimum )
      ), 'error'
    );

  }
}
}


/**
* Add a new settings tab to the WooCommerce settings tabs array.
*
* @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
* @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
*/
public static function add_settings_tab( $settings_tabs ) {
  $settings_tabs['settings_tab_demo'] = __( 'Simple Woocommerce Minimum Order', 'woocommerce-settings-tab-demo' );
  return $settings_tabs;
}
/**
* Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
*
* @uses woocommerce_admin_fields()
* @uses self::get_option()
*/
public static function settings_tab() {
  woocommerce_admin_fields( self::get_option() );
}
/**
* Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
*
* @uses woocommerce_update_options()
* @uses self::get_option()
*/
public static function update_settings() {
  woocommerce_update_options( self::get_option() );
}

/**
* Get all the settings for this plugin for @see woocommerce_admin_fields() function.
*
* @return array Array of settings for @see woocommerce_admin_fields() function.
*/
public static function get_option() {
  $settings = array(
    'section_title' => array(
      'name'     => __( 'Simple Woocommerce Minimum Order', 'woocommerce-settings-tab-demo' ),
      'type'     => 'title',
      'desc'     => '',
      'id'       => 'wc_min_order_section_title'
    ),
    'title' => array(
      'name' => __( 'Minimum Order Amount', 'woocommerce-settings-tab-demo' ),
      'type' => 'text',
      'css'  => 'max-width: 100px;',
      'desc' => __( 'All orders must be greater than or equal to the amount in this field.', 'woocommerce-settings-tab-demo' ),
      'id'   => 'wc_min_order_field'
    ),
    'section_end' => array(
      'type' => 'sectionend',
    )
  );
  return apply_filters( 'wc_min_order_settings', $settings );
}
}
WC_Min_Order::init();
