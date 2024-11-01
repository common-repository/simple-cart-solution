<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 29/11/2019
 * Time: 11:38
 */

namespace SimpleCart\Carts;


use SimpleCart\Abstracts\Cart;

class WooCommerce extends Cart {


	/**
	 * Add an item
	 *
	 * @param $product_id
	 * @param $quantity
	 * @param $options
	 */
	public function add_item( $product_id, $quantity = 1, $options = array() ) {
		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $product_id ) );
		$product           = wc_get_product( $product_id );
		$quantity          = wc_stock_amount( wp_unslash( $quantity ) );
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
		$product_status    = get_post_status( $product_id );
		$variation_id      = 0;
		$variation         = array();

		if ( $product && 'variation' === $product->get_type() ) {
			$variation_id = $product_id;
			$product_id   = $product->get_parent_id();
			$variation    = $product->get_variation_attributes();
		}

		if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) && 'publish' === $product_status ) {

			do_action( 'woocommerce_ajax_added_to_cart', $product_id );

			return true;

		}

		return false;
	}

	/**
	 * Remove the Item
	 * @param string $cart_key
	 */
	public function remove_item( $cart_key ) {
		$cart = $this->get_cart();

		$cart->remove_cart_item( $cart_key );
	}

	/**
	 * Set the quantity of an item.
	 *
	 * @param $cart_key
	 * @param $quantity
	 */
	public function set_quantity( $cart_key, $quantity ) {
		$cart = $this->get_cart();

		$cart->set_quantity( $cart_key, $quantity );
	}

	/**
	 * @return null|\WC_Cart
	 */
	public function get_cart() {
		if ( null === $this->cart ) {
			$this->cart = WC()->cart;
		}

		return $this->cart;
	}

	/**
	 *
	 */
	public function get_products() {
		$cart  = $this->get_cart();
		$items = array();
		if ( $cart->get_cart_contents() ) {
			foreach ( $cart->get_cart_contents() as $cart_item ) {

				$items[] = array(
					'title'    => $cart_item['data']->get_title(),
					'cart_key' => $cart_item['key'],
					'quantity' => $cart_item['quantity'],
					'total'    => wc_price( $cart_item['line_subtotal'] ),
					'image'    => $cart_item['data']->get_image()
				);
			}
		}

		return $items;
	}

	/**
	 * @return int
	 */
	public function get_count() {
		$cart = $this->get_cart();
		return $cart->get_cart_contents_count();
	}

	/**
	 *
	 */
	public function get_totals() {
		$cart   = $this->get_cart();
		$totals = $cart->get_totals();

		return array(
			'subtotal'       => wc_price( $totals['subtotal'] ),
			'shipping_total' => wc_price( $totals['shipping_total'] ),
			'total'          => wc_price( $totals['total'] ),
		);
	}

	/**
	 * Checkout URL
	 */
	public function get_checkout_url() {
		return wc_get_checkout_url();
	}
}