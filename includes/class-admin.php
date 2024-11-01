<?php
/**
 * Class to handle Admin parts
 */

namespace SimpleCart;

/**
 * Class Admin
 *
 * @package SimpleCart
 */
class Admin {

	/**
	 * Ajax constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'simple_cart_settings_page', array( $this, 'output_fields' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

		add_action( 'wp_loaded', array( $this, 'save' ) );
	}

	/**
	 * Save the page.
	 */
	public function save() {
	    if ( ! isset( $_POST['simple_cart_save_changes'] ) ) {
	        return;
        }
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general';
		$fields = $this->get_fields( $active_tab );

		if ( ! $fields ) {
			return;
		}

		self::save_fields( $fields );
	}

	/**
	 * Enqueue Admin scripts
	 */
	public function enqueue( $hook ) {
	    if ( 'toplevel_page_simple-cart' !== $hook ) {
	        return;
        }

		wp_enqueue_script( 'simple-cart-admin-js', untrailingslashit( SIMPLE_CART_URL ) . '/assets/dist/js/admin.js', array( 'jquery', 'iris' ), '', true );
		wp_enqueue_style( 'simple-cart-admin-css', untrailingslashit( SIMPLE_CART_URL ) . '/assets/dist/css/admin.css' );
	}

	/**
	 * Register Menu
	 */
	public function register_menu() {
		add_menu_page(
			__( 'Simple Cart', 'simple-cart' ),
			__( 'Simple Cart', 'simple-cart' ),
			'manage_options',
			'simple-cart',
			array( $this, 'menu_page' ),
			'dashicons-cart',
			30
		);
	}

	/**
	 * Admin Menu Page
	 */
	public function menu_page() {
		$tabs = apply_filters( 'simple_cart_settings_tabs', array(
			'general' => __( 'General', 'simple-cart' ),
		) );

		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general';

		if ( ! isset( $tabs[ $active_tab ] ) && $tabs ) {
			$tab_keys = array_keys( $tabs );
			$active_tab = $tab_keys[0];
		}

		?>
		<div class="wrap">
			<h1><?php echo get_admin_page_title(); ?></h1>
			<?php
			if ( $tabs ) {
			?>
			<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
				<?php
				foreach ( $tabs as $tab_slug => $tab_title ) {
					$active_class = '';

					if ( $active_tab === $tab_slug ) {
						$active_class = 'nav-tab-active';
					}
					?>
					<a href="<?php echo esc_url( admin_url('admin.php?page=simple-cart&tab=' . $tab_slug ) ); ?>" class="nav-tab <?php echo esc_attr( $active_class ); ?>"><?php echo esc_html( $tab_title ) ;?></a>
					<?php
				}
				?>
			</nav>
			<?php } ?>

			<form method="post" enctype="multipart/form-data">
                <?php
                    do_action( 'simple_cart_settings_page', $active_tab );
                    do_action( 'simple_cart_settings_' . $active_tab );
                ?>
				<button type="submit" name="simple_cart_save_changes" class="button button-primary"><?php esc_html_e( 'Save Changes', 'simple-cart' ); ?></button>
			</form>
		</div>
		<?php
	}

	/**
     * Output fields
     *
	 * @param string $tab Tab
	 */
	public function output_fields( $tab ) {
	    $fields = $this->get_fields( $tab );

	    if ( ! $fields ) {
	        return;
        }

        foreach ( $fields as $field_slug => $field ) {
	        $field['value'] = get_option( $field['id'] );
	        $this->render_field( $field, $field_slug );
        }
    }

	/**
     * Render the field
     *
	 * @param array  $field
	 * @param string $field_slug
	 */
    public function render_field( $field, $field_slug = '' ) {
        $field = wp_parse_args( $field, array(
            'id'      => '',
            'type'    => 'text',
            'css'     => '',
            'class'   => '',
            'options' => array(),
            'default' => '',
            'value'   => '',
            'suffix'  => '',
            'placeholder' => '',
        ));

        switch ( $field['type'] ) {
            case 'section_start':
                ?>
                <h3><?php echo esc_html( $field['label'] ); ?></h3>
                <table class="form-table">
                <?php
                break;
            case 'section_end':
                ?>
                </table>
                <?php
                break;
            // Radio inputs.
            case 'radio':
               $option_value = $field['value'] ? $field['value'] : $field['default'];


                ?>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                    </th>
                    <td class="forminp forminp-<?php echo esc_attr( sanitize_title( $field['type'] ) ); ?>">
                        <fieldset>

                            <ul>
                            <?php
                            foreach ( $field['options'] as $key => $val ) {
                                ?>
                                <li>
                                    <label><input
                                        name="<?php echo esc_attr( $field['id'] ); ?>"
                                        value="<?php echo esc_attr( $key ); ?>"
                                        type="radio"
                                        style="<?php echo esc_attr( $value['css'] ); ?>"
                                        class="<?php echo esc_attr( $field['class'] ); ?>"
                                        <?php checked( $key, $option_value ); ?>
                                        /> <?php echo esc_html( $val ); ?></label>
                                </li>
                                <?php
                            }
                            ?>
                            </ul>
                        </fieldset>
                    </td>
                </tr>
                <?php
                break;

            // Checkbox input.
            case 'checkbox':
                $option_value     = $field['value'];
                $visibility_class = array();

                if ( ! isset( $field['hide_if_checked'] ) ) {
                    $field['hide_if_checked'] = false;
                }
                if ( ! isset( $field['show_if_checked'] ) ) {
                    $field['show_if_checked'] = false;
                }
                if ( 'yes' === $field['hide_if_checked'] || 'yes' === $field['show_if_checked'] ) {
                    $visibility_class[] = 'hidden_option';
                }
                if ( 'option' === $field['hide_if_checked'] ) {
                    $visibility_class[] = 'hide_options_if_checked';
                }
                if ( 'option' === $field['show_if_checked'] ) {
                    $visibility_class[] = 'show_options_if_checked';
                }

                if ( ! isset( $field['checkboxgroup'] ) || 'start' === $field['checkboxgroup'] ) {
                    ?>
                        <tr valign="top" class="<?php echo esc_attr( implode( ' ', $visibility_class ) ); ?>">
                            <th scope="row" class="titledesc"><?php echo esc_html( $field['label'] ); ?></th>
                            <td class="forminp forminp-checkbox">
                                <fieldset>
                    <?php
                } else {
                    ?>
                        <fieldset class="<?php echo esc_attr( implode( ' ', $visibility_class ) ); ?>">
                    <?php
                }

                if ( ! empty( $field['label'] ) ) {
                    ?>
                        <legend class="screen-reader-text"><span><?php echo esc_html( $field['label'] ); ?></span></legend>
                    <?php
                }

                ?>
                    <label for="<?php echo esc_attr( $field['id'] ); ?>">
                        <input
                            name="<?php echo esc_attr( $field['id'] ); ?>"
                            id="<?php echo esc_attr( $field['id'] ); ?>"
                            type="checkbox"
                            class="<?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : '' ); ?>"
                            value="1"
                            <?php checked( $option_value, 'yes' ); ?>
                        />
                    </label>
                <?php

                if ( ! isset( $field['checkboxgroup'] ) || 'end' === $field['checkboxgroup'] ) {
                    ?>
                                </fieldset>
                            </td>
                        </tr>
                    <?php
                } else {
                    ?>
                        </fieldset>
                    <?php
                }
                break;

            case 'color':
	            $option_value = $field['value'] ? $field['value'] : $field['default'];

	            ?>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                    </th>
                    <td class="forminp forminp-<?php echo esc_attr( sanitize_title( $field['type'] ) ); ?>">&lrm;
                        <span class="sc-colorpickpreview" style="background: <?php echo esc_attr( $option_value ); ?>">&nbsp;</span>
                        <input
                                name="<?php echo esc_attr( $field['id'] ); ?>"
                                id="<?php echo esc_attr( $field['id'] ); ?>"
                                type="text"
                                dir="ltr"
                                style="<?php echo esc_attr( $field['css'] ); ?>"
                                value="<?php echo esc_attr( $option_value ); ?>"
                                class="<?php echo esc_attr( $field['class'] ); ?>sc-colorpick"
                                placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
                        />&lrm;
                        <div id="colorPickerDiv_<?php echo esc_attr( $field['id'] ); ?>" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>
                    </td>
                </tr>
	            <?php
                break;
	        // Standard text inputs and subtypes like 'number'.
	        case 'text':
	        case 'password':
	        case 'datetime':
	        case 'datetime-local':
	        case 'date':
	        case 'month':
	        case 'time':
	        case 'week':
	        case 'number':
	        case 'email':
	        case 'url':
	        case 'tel':
		        $option_value = $field['value'] ? $field['value'] : $field['default'];

		        ?><tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                </th>
                <td class="forminp forminp-<?php echo esc_attr( sanitize_title( $field['type'] ) ); ?>">
                    <input
                            name="<?php echo esc_attr( $field['id'] ); ?>"
                            id="<?php echo esc_attr( $field['id'] ); ?>"
                            type="<?php echo esc_attr( $field['type'] ); ?>"
                            style="<?php echo esc_attr( $field['css'] ); ?>"
                            value="<?php echo esc_attr( $option_value ); ?>"
                            class="<?php echo esc_attr( $field['class'] ); ?>"
                            placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
                    /><?php echo esc_html( $field['suffix'] ); ?>
                </td>
                </tr>
		        <?php
		        break;
	        // Select boxes.
	        case 'select':
	        case 'multiselect':
		        $option_value = $field['value'];

		        ?>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                    </th>
                    <td class="forminp forminp-<?php echo esc_attr( sanitize_title( $field['type'] ) ); ?>">
                        <select
                                name="<?php echo esc_attr( $field['id'] ); ?><?php echo ( 'multiselect' === $field['type'] ) ? '[]' : ''; ?>"
                                id="<?php echo esc_attr( $field['id'] ); ?>"
                                style="<?php echo esc_attr( $field['css'] ); ?>"
                                class="<?php echo esc_attr( $field['class'] ); ?>"
					        <?php echo 'multiselect' === $field['type'] ? 'multiple="multiple"' : ''; ?>
                        >
					        <?php
					        foreach ( $field['options'] as $key => $val ) {
						        ?>
                                <option value="<?php echo esc_attr( $key ); ?>"
							        <?php

							        if ( is_array( $option_value ) ) {
								        selected( in_array( (string) $key, $option_value, true ), true );
							        } else {
								        selected( $option_value, (string) $key );
							        }

							        ?>
                                ><?php echo esc_html( $val ); ?></option>
						        <?php
					        }
					        ?>
                        </select>
                    </td>
                </tr>
		        <?php
		        break;
            default:
                do_action( 'simple_cart_field_html_' . $field['type'], $field, $field_slug );
                break;
        }
    }

	/**
	 * Get Fields
	 */
    public function get_fields( $tab ) {
        $fields = apply_filters( 'simple_cart_fields', array(
            'general' => array(
                'design_title' => array(
                    'label' => __( 'Design', 'simple-cart' ),
                    'type'  => 'section_start',
                ),
                'simple_cart_button_position' => array(
	                'id'    => 'simple_cart_button_position',
	                'label' => __( 'Button Position', 'simple-cart' ),
	                'type'  => 'select',
                    'options' => array(
                        'left' => __( 'Left', 'simple-cart' ),
                        'right' => __( 'Right', 'simple-cart' ),
                    ),
                    'default' => 'right'
                ),
                'simple_cart_button_bg' => array(
                    'id'    => 'simple_cart_button_bg',
	                'label' => __( 'Button Background', 'simple-cart' ),
	                'type'  => 'color',
                    'default' => '#96588a',
                ),
                'simple_cart_button_color' => array(
	                'id'    => 'simple_cart_button_color',
	                'label' => __( 'Button Color', 'simple-cart' ),
	                'type'  => 'color',
                    'default' => 'ffffff'
                ),
                'design_title_end' => array(
                    'type' => 'section_end'
                )
            ),
        ));

        return isset( $fields[ $tab ] ) ? $fields[ $tab ] : array();
    }


	public static function clean( $data ) {
        if ( is_array( $data ) ) {
            return array_map( '\SimpleCart\Admin::clean', $data );
        } else {
            return is_scalar( $data ) ? sanitize_text_field( $data ) : $data;
        }
	}


	/**
	 * Save admin fields.
	 *
	 * Loops though the woocommerce options array and outputs each field.
	 *
	 * @param array $options Options array to output.
	 * @param array $data    Optional. Data to use for saving. Defaults to $_POST.
	 * @return bool
	 */
	public static function save_fields( $options, $data = null ) {
		if ( is_null( $data ) ) {
			$data = $_POST; // WPCS: input var okay, CSRF ok.
		}
		if ( empty( $data ) ) {
			return false;
		}

		// Options to update will be stored here and saved later.
		$update_options   = array();
		$autoload_options = array();

		// Loop options and get values to save.
		foreach ( $options as $option ) {
			if ( ! isset( $option['id'] ) || ! isset( $option['type'] ) || ( isset( $option['is_option'] ) && false === $option['is_option'] ) ) {
				continue;
			}

			// Get posted value.
			if ( strstr( $option['id'], '[' ) ) {
				parse_str( $option['id'], $option_name_array );
				$option_name  = current( array_keys( $option_name_array ) );
				$setting_name = key( $option_name_array[ $option_name ] );
				$raw_value    = isset( $data[ $option_name ][ $setting_name ] ) ? wp_unslash( $data[ $option_name ][ $setting_name ] ) : null;
			} else {
				$option_name  = $option['id'];
				$setting_name = '';
				$raw_value    = isset( $data[ $option['id'] ] ) ? wp_unslash( $data[ $option['id'] ] ) : null;
			}

			// Format the value based on option type.
			switch ( $option['type'] ) {
				case 'checkbox':
					$value = '1' === $raw_value || 'yes' === $raw_value ? 'yes' : 'no';
					break;
				case 'textarea':
					$value = wp_kses_post( trim( $raw_value ) );
					break;
				case 'multiselect':
				case 'multi_select_countries':
					$value = array_filter( array_map( '\SimpleCart\Admin::clean', (array) $raw_value ) );
					break;
				case 'image_width':
					$value = array();
					if ( isset( $raw_value['width'] ) ) {
						$value['width']  = self::clean( $raw_value['width'] );
						$value['height'] = self::clean( $raw_value['height'] );
						$value['crop']   = isset( $raw_value['crop'] ) ? 1 : 0;
					} else {
						$value['width']  = $option['default']['width'];
						$value['height'] = $option['default']['height'];
						$value['crop']   = $option['default']['crop'];
					}
					break;
				case 'select':
					$allowed_values = empty( $option['options'] ) ? array() : array_map( 'strval', array_keys( $option['options'] ) );
					if ( empty( $option['default'] ) && empty( $allowed_values ) ) {
						$value = null;
						break;
					}
					$default = ( empty( $option['default'] ) ? $allowed_values[0] : $option['default'] );
					$value   = in_array( $raw_value, $allowed_values, true ) ? $raw_value : $default;
					break;
				default:
					$value = self::clean( $raw_value );
					break;
			}

			/**
			 * Sanitize the value of an option.
			 */
			$value = apply_filters( 'simple_cart_admin_settings_sanitize_option', $value, $option, $raw_value );

			/**
			 * Sanitize the value of an option by option name.
			 */
			$value = apply_filters( "simple_cart_admin_settings_sanitize_option_$option_name", $value, $option, $raw_value );

			if ( is_null( $value ) ) {
				continue;
			}

			// Check if option is an array and handle that differently to single values.
			if ( $option_name && $setting_name ) {
				if ( ! isset( $update_options[ $option_name ] ) ) {
					$update_options[ $option_name ] = get_option( $option_name, array() );
				}
				if ( ! is_array( $update_options[ $option_name ] ) ) {
					$update_options[ $option_name ] = array();
				}
				$update_options[ $option_name ][ $setting_name ] = $value;
			} else {
				$update_options[ $option_name ] = $value;
			}

			$autoload_options[ $option_name ] = isset( $option['autoload'] ) ? (bool) $option['autoload'] : true;

			/**
			 * Fire an action before saved.
			 *
			 * @deprecated 2.4.0 - doesn't allow manipulation of values!
			 */
			do_action( 'woocommerce_update_option', $option );
		}

		// Save all options in our array.
		foreach ( $update_options as $name => $value ) {
			update_option( $name, $value, $autoload_options[ $name ] ? 'yes' : 'no' );
		}

		return true;
	}
}