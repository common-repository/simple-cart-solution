<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 05/12/2019
 * Time: 18:29
 */

namespace SimpleCart\Integrations;

use SimpleCart\Cart_Templating;

/**
 * Class WooCommerce
 *
 * @package SimpleCart\Integrations
 */
class WooCommerce {

	/**
	 * WooCommerce constructor.
	 */
	public function __construct() {
		add_filter( 'simple_cart_load_cart_object', array( $this, 'load_cart_object' ) );
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'add_cart_fragment' ) );
		add_filter( 'simple_cart_get_cart_pre_item_fragments', array( $this, 'load_woocommerce_cart_fragments' ) );
	}

	public function load_woocommerce_cart_fragments( $fragments ) {
		if ( null !== $fragments ) {
			return $fragments;
		}

		ob_start();

		woocommerce_mini_cart();

		$mini_cart = ob_get_clean();

		return apply_filters(
			'woocommerce_add_to_cart_fragments',
			array(
				'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
			)
		);
	}

	/**
	 * @param \SimpleCart\Abstracts\Cart|false $cart
	 */
	public function load_cart_object( $cart ) {
		if ( false === $cart ) {
			return new \SimpleCart\Carts\WooCommerce();
		}

		return $cart;
	}

	/**
	 * @param $fragments
	 *
	 * @return mixed
	 */
	public function add_cart_fragment( $fragments ) {

		ob_start();
		Cart_Templating::show_cart_items();

		$fragments['#SimpleCartPopupTableContainer'] = ob_get_clean();

		ob_start();
		Cart_Templating::show_checkout_button();

		$fragments['#SimpleCartCheckoutButton'] = ob_get_clean();

		ob_start();
		Cart_Templating::show_cart_count();
		$fragments['span#scCartPopupCount'] = ob_get_clean();

		return $fragments;
	}
}