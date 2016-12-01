<?php

/**
 * Plugin Name: GMT EDD Redirect on Empty Cart
 * Plugin URI: https://github.com/cferdinandi/gmt-edd-redirect-on-empty-cart/
 * GitHub Plugin URI: https://github.com/cferdinandi/gmt-edd-redirect-on-empty-cart/
 * Description: Redirect Easy Digital Downloads from your checkout cart to another page when the cart is empty.
 * Version: 1.0.0
 * Author: Chris Ferdinandi
 * Author URI: http://gomakethings.com
 * License: GPLv3
 */


	/**
	 * Add settings section
	 * @param array $sections The current sections
	 */
	function gmt_edd_redirect_empty_cart_settings_section( $sections ) {
		$sections['gmt_edd_redirect_empty_cart'] = __( 'Redirect Empty Cart', 'gmt_edd_empty_cart' );
		return $sections;
	}
	add_filter( 'edd_settings_sections_extensions', 'gmt_edd_redirect_empty_cart_settings_section' );



	/**
	 * Add settings
	 * @param  array $settings The existing settings
	 */
	function gmt_edd_redirect_empty_cart_settings( $settings ) {

		$empty_cart_settings = array(
			array(
				'id'    => 'gmt_edd_redirect_empty_cart_settings',
				'name'  => '<strong>' . __( 'Redirect Empty Cart Settings', 'gmt_edd_empty_cart' ) . '</strong>',
				'desc'  => __( 'Configure Redirect Empty Cart Settings', 'gmt_edd_empty_cart' ),
				'type'  => 'header',
			),
			array(
				'id'      => 'gmt_edd_redirect_empty_cart_page',
				'name'    => __( 'Empty Cart Redirect Page', 'gmt_edd_empty_cart' ),
				'desc'    => __( 'The page to redirect to when the cart is empty', 'edd-empty-cart' ),
				'type'    => 'select',
				'options' => edd_get_pages(),
				'chosen'  => true,
			),
		);
		if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
			$empty_cart_settings = array( 'gmt_edd_redirect_empty_cart' => $empty_cart_settings );
		}
		return array_merge( $settings, $empty_cart_settings );
	}
	add_filter( 'edd_settings_extensions', 'gmt_edd_redirect_empty_cart_settings', 999, 1 );



	/**
	 * Redirect if cart is empty
	 */
	function gmt_edd_redirect_empty_cart() {

		// Sanity check
		if ( is_admin() || !function_exists( 'edd_get_option' ) ) return;

		// Get the redirect URL
		$redirect = edd_get_option( 'gmt_edd_redirect_empty_cart_page', false );
		if ( empty( $redirect ) ) return;

		// If not the cart or the cart has items, bail
		if ( !is_page( edd_get_option( 'purchase_page', false ) ) || edd_get_cart_quantity() !== 0 ) return;

		// Redirect
		wp_safe_redirect( get_permalink( $redirect ) );
		exit;

	}
	add_action( 'template_redirect', 'gmt_edd_redirect_empty_cart' );