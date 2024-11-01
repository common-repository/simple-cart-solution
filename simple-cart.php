<?php
/**
 * Plugin Name:     Simple Cart
 * Plugin URI:      https://www.ibenic.com
 * Description:     A Cart solution to provide a better user purchasing experience
 * Author:          Igor Benić
 * Author URI:      https://www.ibenic.com
 * Text Domain:     simple-cart
 * Domain Path:     /languages
 * Version:         1.0.2
 *
 * @package         Simple_Cart
 */

namespace SimpleCart;

use SimpleCart\Integrations\EDD;
use SimpleCart\Integrations\WooCommerce;

if ( ! function_exists( '\SimpleCart\scs_fs' ) ) {
	// Create a helper function for easy SDK access.
	function scs_fs() {
		global $scs_fs;

		if ( ! isset( $scs_fs ) ) {
			// Include Freemius SDK.
			require_once dirname(__FILE__) . '/freemius/start.php';

			$scs_fs = fs_dynamic_init( array(
				'id'                  => '5511',
				'slug'                => 'simple-cart-solution',
				'type'                => 'plugin',
				'public_key'          => 'pk_5b890eadafbc4746ee0a4e8548c10',
				'is_premium'          => false,
				'has_addons'          => true,
				'has_paid_plans'      => false,
				'menu'                => array(
					'slug'           => 'simple-cart',
					'support'        => false,
				),
			) );
		}

		return $scs_fs;
	}

	// Init Freemius.
	scs_fs();
	// Signal that SDK was initiated.
	do_action( 'scs_fs_loaded' );
}

if ( ! class_exists( 'SimpleCart\Plugin' ) ) {
	class Plugin {

		/**
		 * @var null
		 */
		public static $cart = null;

		/**
		 * @var string
		 */
		public $version = '1.0.2';

		/**
		 * Load the integration
		 * @var null
		 */
		public $integration = null;

		/**
		 * Plugin constructor.
		 */
		public function __construct() {
			$this->constants();
			$this->includes();
			add_action( 'plugins_loaded', array( $this, 'run' ), 90 );
		}

		/**
		 * Constants
		 */
		public function constants() {
			define( 'SIMPLE_CART_FILE', __FILE__ );
			define( 'SIMPLE_CART_URL', plugin_dir_url( __FILE__ ) );
			define( 'SIMPLE_CART_PATH', plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Include a file
		 * @param string $file
		 */
		public function include_file( $file ) {
			include_once untrailingslashit( SIMPLE_CART_PATH ) . '/includes/' . $file;
		}

		/**
		 * Includes
		 */
		public function includes(){
			$this->include_file( 'interfaces/class-cart.php' );

			$this->include_file( 'abstracts/class-cart.php' );

			$this->include_file( 'integrations/class-woocommerce.php' );

			$this->include_file( 'integrations/class-edd.php' );

			$this->include_file( 'carts/class-woocommerce.php' );
			$this->include_file( 'carts/class-edd.php');

			$this->include_file( 'class-cart-templating.php' );

			$this->include_file( 'class-ajax.php' );
			$this->include_file( 'class-customizer.php' );

			if ( is_admin() ) {
				$this->include_file( 'class-admin.php' );
			}
		}

		/**
		 * Run the plugin
		 */
		public function run() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_footer', array( $this, 'load_cart' ) );

			$this->load_integration();
			new Cart_Templating();
			new Ajax();
			new Customizer();

			if ( is_admin() ) {
				new Admin();
			}

			do_action( 'simple_cart_run' );
		}

		/**
		 * Load Integration
		 */
		public function load_integration() {
			if ( class_exists( 'WooCommerce' ) ) {
				$this->integration = new WooCommerce();
			}

			if ( class_exists( 'Easy_Digital_Downloads' ) ) {
				$this->integration = new EDD();
			}
		}

		/**
		 * Enqueue Scripts
		 */
		public function enqueue_scripts() {
			wp_enqueue_style( 'simple-cart-css', untrailingslashit( SIMPLE_CART_URL ) . '/assets/dist/css/public.css' );
			wp_enqueue_script( 'simple-cart-js', untrailingslashit( SIMPLE_CART_URL ) . '/assets/dist/js/public.js', array( 'jquery' ), $this->version, true );
			wp_localize_script( 'simple-cart-js', 'simple_cart', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'simple-cart' ),
			));
		}

		/**
		 * @param $file
		 */
		public static function load_template( $file ) {
			$template = apply_filters( 'simple_cart_template', untrailingslashit( SIMPLE_CART_PATH ) . '/templates/' . $file, $file );
			include $template;
		}

		/**
		 * Get the Cart
		 *
		 * @return Cart|false
		 */
		public static function get_cart() {
			if ( null === self::$cart ) {
				self::$cart = self::load_cart_object();
			}

			return self::$cart;
		}

		/**
		 * Load the cart
		 */
		public static function load_cart_object() {
			return apply_filters( 'simple_cart_load_cart_object', false );
		}

		/**
		 * Load Cart
		 */
		public function load_cart() {
			$cart = self::get_cart();
			// We don't have a cart integration.
			if ( ! $cart ) {
				return;
			}
			self::load_template( 'cart-button.php' );
			self::load_template( 'cart-popup.php' );
		}
	}

	new Plugin();
}
