( function( $ ) {
    wp.customize( 'simple_cart_button_bg', function( value ) {
        value.bind( function( newval ) {
            $('.simple-cart-popup-button').css('background-color', newval );
            $('body .simple-cart-popup-button .simple-cart-popup-button-actions span').css('border-color', newval );
            $('body .simple-cart-popup-button .simple-cart-popup-button-actions span').css('color', newval );
        } );
    } );

    //Update site link color in real time...
    wp.customize( 'simple_cart_button_color', function( value ) {
        value.bind( function( newval ) {
            $('.simple-cart-popup-button').css('color', newval );
        } );
    } );

    wp.customize( 'simple_cart_button_position', function( value ) {
        value.bind( function( newval ) {
            var position = 'right' === newval ? 'right' : 'left',
                reset    = 'right' === newval ? 'left' : 'right';

            $('.simple-cart-popup-button, .simple-cart-popup').css( reset, 'auto' );
            $('.simple-cart-popup-button, .simple-cart-popup').css( position, '20px' );
        } );
    } );

} )( jQuery );