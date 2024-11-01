<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 29/11/2019
 * Time: 11:38
 */

namespace SimpleCart\Carts;


use SimpleCart\Abstracts\Cart;

class EDD extends Cart {

	/**
	 * @return bool
	 */
	public function quantities_enabled() {
		return edd_item_quantities_enabled();
	}

	/**
	 * Remove the Item
	 * @param string $cart_key
	 */
	public function remove_item( $cart_key ) {
		$cart = $this->get_cart();

		$cart->remove( $cart_key );
	}

	/**
	 * Set the quantity of an item.
	 *
	 * @param $cart_key
	 * @param $quantity
	 */
	public function set_quantity( $cart_key, $quantity ) {
		$cart = $this->get_cart();
		$contents = $cart->get_contents();

		if ( ! is_array( $contents ) ) {
			return false;
		} else {
			foreach ( $contents as $position => $item ) {
				if ( absint( $position ) === absint( $cart_key ) ) {
					$cart->set_item_quantity( $item['id'], $quantity, $item['item_number']['options'] );
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @return null|\EDD_Cart
	 */
	public function get_cart() {
		if ( null === $this->cart ) {
			$this->cart = EDD()->cart;
		}

		return $this->cart;
	}

	/**
	 *
	 */
	public function get_products() {
		$cart  = $this->get_cart();

		$items = array();

		if ( $cart->get_contents_details() ) {
			foreach ( $cart->get_contents_details() as $cart_key => $cart_item ) {

				$price_id = false;

				if ( isset( $cart_item['item_number']['options'] ) && isset( $cart_item['item_number']['options']['price_id'] ) ) {
					$price_id = $cart_item['item_number']['options']['price_id'];
				}
				$items[] = array(
					'title'    => $cart_item['name'],
					'cart_key' => $cart_key,
					'quantity' => $cart_item['quantity'],
					'total'    => edd_price( $cart_item['id'], false, $price_id ),
					'image'    => get_the_post_thumbnail(  $cart_item['id'] )
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
		return $cart->get_quantity();
	}

	/**
	 *
	 */
	public function get_totals() {
		$cart = $this->get_cart();
		$subtotal = $cart->subtotal();
		$total    = $cart->total( false );

		return array(
			'subtotal'       => $subtotal,
			'total'          => $total,
		);
	}

	/**
	 * Checkout URL
	 */
	public function get_checkout_url() {
		return edd_get_checkout_uri();
	}
}