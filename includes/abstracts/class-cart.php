<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 29/11/2019
 * Time: 11:30
 */

namespace SimpleCart\Abstracts;

/**
 * Class Cart
 *
 * @package SimpleCart\Abstracts
 */
abstract class Cart implements \SimpleCart\Interfaces\Cart {

	public $cart = null;

	/**
	 * Does the cart has quantities enabled?
	 * @return bool
	 */
	public function quantities_enabled() {
		return true;
	}

	public function remove_item( $cart_key ) {}

	public function set_quantity( $car_key, $quantity ) {}

	/**
	 *
	 */
	public function get_cart() {
		return $this->cart;
	}

	/**
	 *
	 */
	public function get_products() {
		return array();
	}

	/**
	 *
	 */
	public function get_totals() {
		return array();
	}

	/**
	 * Get Count
	 * @return int
	 */
	public function get_count() {
		return 0;
	}

	/**
	 * Checkout URL
	 */
	public function get_checkout_url() {
		return '';
	}

	/**
	 * @param $totals
	 *
	 * @return array
	 */
	public function get_formatted_totals( $totals ) {
		$totals_strings = array(
			'subtotal' => __( 'Subtotal', 'simple-cart' ),
		);

		$formatted_totals = array();

		foreach ( $totals_strings as $totals_key => $label ) {
			if ( ! isset( $totals[ $totals_key ] ) ) {
				continue;
			}

			$formatted_totals[ $totals_key ] = array(
				'label' => $label,
				'value' => $totals[ $totals_key ],
			);
		}

		return $formatted_totals;
	}
}