<?php

namespace SimpleCart;

class Cart_Templating {

	/**
	 * Cart_Templating constructor.
	 */
	public function __construct() {
		add_action( 'simple_cart_popup_bottom', __CLASS__ . '::show_powered_by', 99 );
		add_action( 'simple_cart_popup_body', __CLASS__ . '::show_cart_items', 20 );
		add_action( 'simple_cart_popup_body', __CLASS__ . '::show_checkout_button', 21 );
		add_action( 'simple_cart_popup_button_actions', __CLASS__ . '::show_cart_count' );
	}

	/**
	 * Show Cart Count
	 */
    public static function show_cart_count() {
	    $cart = \SimpleCart\Plugin::get_cart();
	    if ( ! $cart ) {
	        return;
        }
	    ?>
            <span id="scCartPopupCount" class="sc-cart-popup-count"><?php echo esc_html( $cart->get_count() ); ?></span>
        <?php
    }

	/**
	 * Show the Cart Items
	 */
	public static function show_cart_items() {
        $cart     = \SimpleCart\Plugin::get_cart();

        if ( ! $cart ) {
            return;
        }
        $items    = $cart->get_products();
        $totals   = $cart->get_totals();
        $totals   = $cart->get_formatted_totals( $totals );

        ?>
        <div id="SimpleCartPopupTableContainer" class="simple-cart-popup-table-container">
            <?php if ( $items ) { ?>
            <table id="SimpleCartPopupTable" class="simple-cart-popup-table">
                <tbody>
                <?php
                foreach ( $items as $item ) {
                    ?>
                    <tr class="sc-cart-item" data-key="<?php echo esc_attr( $item['cart_key'] ); ?>">
                        <td class="sc-cart-popup-table-column sc-cart-popup-table-image"><?php echo wp_kses_post( $item['image'] ); ?></td>
                        <td class="sc-cart-popup-table-column sc-cart-popup-table-title"><?php echo wp_kses_post( $item['title'] ); ?></td>
                        <td class="sc-cart-popup-table-column sc-cart-popup-table-quantity">
                            <?php
                                if ( $cart->quantities_enabled() ) {
                            ?>
                            <input type="number" min="0" step="1" value="<?php echo esc_attr( $item['quantity'] ); ?>" />
                            <?php
                                } else {
                                   echo esc_html( $item['quantity'] );
                                }
                            ?>
                        </td>
                        <td class="sc-cart-popup-table-column sc-cart-popup-table-total"><?php echo wp_kses_post( $item['total'] ); ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
                <?php
                if ( $totals ) {
                    ?>
                    <tfoot>
                    <?php
                    foreach ( $totals as $total ) {

                        ?>
                        <tr>
                            <td class="sc-cart-popup-table-footer-total" colspan="3">
                                <?php echo wp_kses_post( $total['label'] ); ?>
                            </td>
                            <td class="sc-cart-popup-table-footer-total sc-cart-popup-table-footer-total-value">
                                <?php echo wp_kses_post( $total['value'] ); ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tfoot>
                    <?php
                }
                ?>
            </table>
            <?php } else {
                ?>
                <div class="simple-cart-popup-empty">
                    <svg id="simpleCartButtonIcon" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="shopping-cart" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="simple-cart-popup-button-icon simple-cart-popup-button-icon-cart"><path fill="currentColor" d="M528.12 301.319l47.273-208C578.806 78.301 567.391 64 551.99 64H159.208l-9.166-44.81C147.758 8.021 137.93 0 126.529 0H24C10.745 0 0 10.745 0 24v16c0 13.255 10.745 24 24 24h69.883l70.248 343.435C147.325 417.1 136 435.222 136 456c0 30.928 25.072 56 56 56s56-25.072 56-56c0-15.674-6.447-29.835-16.824-40h209.647C430.447 426.165 424 440.326 424 456c0 30.928 25.072 56 56 56s56-25.072 56-56c0-22.172-12.888-41.332-31.579-50.405l5.517-24.276c3.413-15.018-8.002-29.319-23.403-29.319H218.117l-6.545-32h293.145c11.206 0 20.92-7.754 23.403-18.681z" class=""></path></svg>
                    <p><?php esc_html_e( 'Your Cart is Empty', 'simple-cart' ); ?></p>
                </div>
                <?php
            } ?>
        </div>
        <?php

	}

	/**
	 * Show Checkout Button
	 */
	public static function show_checkout_button() {
		$cart     = \SimpleCart\Plugin::get_cart();
		if ( ! $cart ) {
		    return;
        }
		$checkout = $cart->get_checkout_url();
		$items    = $cart->get_products();
		?>
        <div id="SimpleCartCheckoutButton">
        <?php
		if ( $checkout && $items ) {
			?>
            <a class="simple-cart-button" href="<?php echo esc_url( $checkout ); ?>"><?php esc_html_e( 'Checkout', 'simple-cart' ); ?></a>
			<?php
		}
		?>
		</div>
        <?php
    }

	public static function show_powered_by() {
		?>
		<div class="simple-cart-powered-by">
			<?php echo sprintf( esc_html__( 'Powered by %s', 'simple-cart' ), '<a href="https://wordpress.org/plugins/simple-cart-solution/">' . __( 'Simple Cart', 'simple-cart' ) . '</a>' ); ?>
		</div>
		<?php
	}

	/**
	 * @return array
	 */
	public static function get_cart_item_fragments() {

		$pre_fragments = apply_filters( 'simple_cart_get_cart_pre_item_fragments', null );

		if ( null !== $pre_fragments ) {
            return $pre_fragments;
        }

		$fragments = array();

		ob_start();
		Cart_Templating::show_cart_items();

		$fragments['#SimpleCartPopupTableContainer'] = ob_get_clean();

		ob_start();
		Cart_Templating::show_checkout_button();

		$fragments['#SimpleCartCheckoutButton'] = ob_get_clean();

		ob_start();
		Cart_Templating::show_cart_count();
		$fragments['span#scCartPopupCount'] = ob_get_clean();


		return apply_filters( 'simple_cart_get_cart_item_fragments', $fragments );
    }
}
