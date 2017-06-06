<?php
/*
Plugin Name: UP WP Cart
Plugin URI: https://github.com/viewup/up-wp-cart
Description: Simple Cart for WordPress
Author: ViewUp
Author URI: http://viewup.com.br/
Version: 0.1.0
Text Domain: upwpcart
License: MIT
*/

/**
 * Initialize GLOBALS
 */

define( 'UPWPCART_VERSION', '0.1.0' );
define( 'UPWPCART_PLUGIN_DIR', __DIR__ );
define( 'UPWPCART_CLASS_NAME', 'WPCart' );
define( 'UPWPCART_SESSION_NAME', 'UP_WP_CART' );

if ( ! defined( 'UP_API_BASE' ) ) {
	define( 'UP_API_BASE', 'up' );
}
if ( ! defined( 'UP_API_VERSION' ) ) {
	define( 'UP_API_VERSION', 'v1' );
}
if ( ! defined( 'CART_CONTENT_FILTER' ) ) {
	define( 'CART_CONTENT_FILTER', 'cart_content' );
}

define( 'UPWPCART_API_BASE', UP_API_BASE . '/' . UP_API_VERSION );
define( 'UPWPCART_API_ROUTE', 'cart' );


/**
 * Import class
 */
require_once UPWPCART_PLUGIN_DIR . "/WPCart.php";

/**
 * Start session
 */
add_action( 'init', 'UpCartSessionStart', 1 );
function UpCartSessionStart() {
	if ( ! session_id() ) {
		session_start();
	}
}


/**
 * The default cart content filter
 *
 * it's modified version of defaults WP_API get_post
 *
 * @param int|WP_Post $id The post ID or object
 *
 * @return WP_Post|null The post object or null
 */
function default_cart_content_filter( $id ) {
	$post_obj = get_post( $id );
	$post     = apply_filters( 'rest_the_post', $post_obj, $id );

	return $post;
}

// sets the default filter
add_filter( CART_CONTENT_FILTER, 'default_cart_content_filter' );


add_action( 'init', 'UpCartInit' );

function UpCartInit() {
	global $cart;
	if ( class_exists( UPWPCART_CLASS_NAME ) ) {
		$cart = $_SESSION[ UPWPCART_SESSION_NAME ];
		if ( ! $cart ) {
			$cart = new WPCart();
		}

		$_SESSION[ UPWPCART_SESSION_NAME ] = $cart;
	}
}

/**
 * Import API Rest
 */
require_once UPWPCART_PLUGIN_DIR . '/WPCartAPI.php';

add_action( 'rest_api_init', function () {
	global $cartApi;
	global $cart;
	if ( ! $cartApi ) {
		$cartApi = new WPCartAPI( $cart );
	}
	$cartApi->register_routes();
} );
