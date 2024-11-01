<?php
/**
 * Class to handle AJAX calls
 */

namespace SimpleCart;

/**
 * Class Ajax
 *
 * @package SimpleCart
 */
class Ajax {

	/**
	 * Ajax constructor.
	 */
	public function __construct() {
		$events = array(
			'change_cart_item_quantity' => true,
		);

		foreach ( $events as $event => $nopriv ) {
			add_action( 'wp_ajax_simple_cart_' . $event, array( $this, $event ) );

			if ( $nopriv ) {

				add_action( 'wp_ajax_nopriv_simple_cart_' . $event, array( $this, $event ) );
			}
		}
	}

	/**
	 * Change the Quantity
	 */
	public function change_cart_item_quantity() {
		check_ajax_referer( 'simple-cart', 'nonce' );

		$cart = Plugin::get_cart();

		$cart_key = isset( $_POST['cart_key'] ) ? sanitize_text_field( $_POST['cart_key'] ) : '';
		$quantity = isset( $_POST['quantity'] ) ? sanitize_text_field( floatval( $_POST['quantity'] ) ) : 0;

		if ( $quantity < 1 ) {
			$cart->remove_item( $cart_key );
		} else {
			$cart->set_quantity( $cart_key, $quantity );
		}

		$fragments = Cart_Templating::get_cart_item_fragments();

		wp_send_json_success(array( 'cart_fragments' => $fragments ) );
		wp_die();
	}
}