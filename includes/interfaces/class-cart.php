<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 29/11/2019
 * Time: 11:25
 */

namespace SimpleCart\Interfaces;


interface Cart {

	/**
	 * Get the cart object
	 * @return mixed
	 */
	public function get_cart();

	/**
	 * Get items of the cart
	 * @return mixed
	 */
	public function get_products();

	/**
	 * Get all totals
	 *
	 * @return mixed
	 */
	public function get_totals();

	/**
	 * Return the Checkout URL
	 *
	 * @return mixed
	 */
	public function get_checkout_url();
}