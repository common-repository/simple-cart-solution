<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 12/02/2020
 * Time: 23:28
 */

namespace SimpleCart;


class Customizer {

	/**
	 * Customizer constructor.
	 */
	public function __construct() {
		add_action( 'customize_register' , array( $this , 'register' ) );
		add_action( 'wp_head' , array( $this , 'header_output' ) );
		add_action( 'customize_preview_init' , array( $this , 'live_preview' ) );
	}

	/**
	 * @param \WP_Customize_Manager $wp_customize
	 */
	public function register ( $wp_customize ) {

		$wp_customize->add_section( 'simple_cart_design',
			array(
				'title'       => __( 'Simple Cart', 'simple-cart' ),
				'priority'    => 35,
				'capability'  => 'edit_theme_options',
				'description' => __('Allows you to customize Simple Cart.', 'simple-cart'),
			)
		);

		$wp_customize->add_setting( 'simple_cart_button_bg',
			array(
				'default'    => '#96588a',
				'type'       => 'option',
				'capability' => 'edit_theme_options',
				'transport'  => 'postMessage',
			)
		);

		$wp_customize->add_setting( 'simple_cart_button_color',
			array(
				'default'    => '#ffffff',
				'type'       => 'option',
				'capability' => 'edit_theme_options',
				'transport'  => 'postMessage',
			)
		);

		$wp_customize->add_setting( 'simple_cart_button_position',
			array(
				'default'    => 'right',
				'type'       => 'option',
				'capability' => 'edit_theme_options',
				'transport'  => 'postMessage',
			)
		);

		$wp_customize->add_control( new \WP_Customize_Color_Control(
			$wp_customize,
			'simple_cart_button_bg',
			array(
				'label'      => __( 'Button Background Color', 'simple-cart' ),
				'priority'   => 10,
				'section'    => 'simple_cart_design',
			)
		) );

		$wp_customize->add_control( new \WP_Customize_Color_Control(
			$wp_customize,
			'simple_cart_button_color',
			array(
				'label'      => __( 'Button Color', 'simple-cart' ),
				'priority'   => 10,
				'section'    => 'simple_cart_design',
			)
		) );

		$wp_customize->add_control( new \WP_Customize_Control(
			$wp_customize,
			'simple_cart_button_position',
			array(
				'label'      => __( 'Button Position', 'simple-cart' ),
				'priority'   => 5,
				'section'    => 'simple_cart_design',
                'type'       => 'select',
				'choices'    => array(
					'right' => __( 'Right', 'simple-cart' ),
					'left'  => __( 'Left', 'simple-cart' ),
				)
			)
		) );
	}

	/**
	 * This will output the custom WordPress settings to the live theme's WP head.
	 *
	 * Used by hook: 'wp_head'
	 *
	 * @see add_action('wp_head',$func)
	 * @since MyTheme 1.0
	 */
	public function header_output() {
	    $position = get_option( 'simple_cart_button_position', 'right' );
		?>
		<!--Customizer CSS-->
		<style type="text/css">
			<?php self::generate_css('body .simple-cart-popup-button', 'background-color', 'simple_cart_button_bg' ); ?>
            <?php self::generate_css('body .simple-cart-popup-button:focus, body .simple-cart-popup-button:active, body .simple-cart-popup-button:hover', 'background-color', 'simple_cart_button_bg' ); ?>
            <?php self::generate_css('body .simple-cart-popup-button .simple-cart-popup-button-actions span', 'border-color', 'simple_cart_button_bg' ); ?>
			<?php self::generate_css('body .simple-cart-popup-button .simple-cart-popup-button-actions span', 'color', 'simple_cart_button_bg' ); ?>
			<?php self::generate_css('body .simple-cart-popup-button, body .simple-cart-popup-button:focus, body .simple-cart-popup-button:active, body .simple-cart-popup-button:focus, body .simple-cart-popup-button:hover', 'color', 'simple_cart_button_color' ); ?>

            body .simple-cart-popup-button,
            body .simple-cart-popup {
                <?php echo $position; ?>: 20px;
            }
        </style>
		<!--/Customizer CSS-->
		<?php
	}

	/**
	 * This outputs the javascript needed to automate the live settings preview.
	 * Also keep in mind that this function isn't necessary unless your settings
	 * are using 'transport'=>'postMessage' instead of the default 'transport'
	 * => 'refresh'
	 *
	 * Used by hook: 'customize_preview_init'
	 *
	 * @see add_action('customize_preview_init',$func)
	 * @since MyTheme 1.0
	 */
	public function live_preview() {
		wp_enqueue_script(
			'simple-cart-customizer',
			untrailingslashit( SIMPLE_CART_URL ) . '/assets/dist/js/customizer.js',
			array(  'jquery', 'customize-preview' ),
			'1.0.0',
			true
		);
	}

	/**
	 * This will generate a line of CSS for use. If the setting
	 * ($mod_name) has no defined value, the CSS will not be output.
	 *
	 * @uses get_theme_mod()
	 * @param string $selector CSS selector
	 * @param string $style The name of the CSS *property* to modify
	 * @param string $mod_name The name of the 'theme_mod' option to fetch
	 * @param string $prefix Optional. Anything that needs to be output before the CSS property
	 * @param string $postfix Optional. Anything that needs to be output after the CSS property
	 * @param bool $echo Optional. Whether to print directly to the page (default: true).
	 * @return string Returns a single line of CSS with selectors and a property.
	 * @since MyTheme 1.0
	 */
	public static function generate_css( $selector, $style, $mod_name, $prefix='', $postfix='', $echo=true ) {
		$return = '';
		$mod = get_option( $mod_name );
		if ( ! empty( $mod ) ) {
			$return = sprintf('%s { %s:%s; }',
				$selector,
				$style,
				$prefix.$mod.$postfix
			);
			if ( $echo ) {
				echo $return;
			}
		}
		return $return;
	}
}