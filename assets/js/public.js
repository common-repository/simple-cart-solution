(function ( $ ) {
    var SimpleCart = function(){
        var self = this;

        this.init = function() {
            this.attachEvents();
        }

        this.addLoader = function addLoader( target ){
            $(target).addClass('simple-cart-loading');
        };

        this.removeLoader = function removeLoader( target ){
            $(target).removeClass('simple-cart-loading');
        };

        this.attachEvents = function() {
            $( document ).on( 'click', '.simple-cart-popup-button', function(){
                var $this = $(this);
                if ( ! self.popupOpened() ) {
                    self.openPopup();
                    $this.addClass('opening');
                    setTimeout(function(){
                        $this.removeClass('opening');
                        $this.addClass('active');
                    }, 200);
                } else {
                    self.closePopup();
                    $this.addClass('closing');
                    setTimeout(function(){
                        $this.removeClass('closing');
                        $this.removeClass('active');
                    }, 200);
                }
            });

            $( document ).on( 'change', '.sc-cart-popup-table-quantity input', function(){
                var $this = $(this),
                    $row  = $this.parents('.sc-cart-item'),
                    $key  = $row.attr('data-key'),
                    $qty  = $this.val();

                self.addLoader('.simple-cart-popup-table-container');

                $.ajax({
                    url: simple_cart.ajaxurl,
                    method: 'POST',
                    data: { action: 'simple_cart_change_cart_item_quantity', cart_key: $key, quantity: $qty, nonce: simple_cart.nonce },
                    success: function( resp ) {
                        if ( resp.success ) {
                            if ( resp.data.cart_fragments ) {
                                $.each( resp.data.cart_fragments, function ( key, value ) {
                                    $( key ).replaceWith( value );
                                } );
                            }
                        }
                    },
                    complete: function() {
                        self.removeLoader('.simple-cart-popup-table-container');
                    }
                });
            });

            // EDD
            $( document.body ).on( 'edd_cart_item_added', function( e, response ) {
                if ( Object.keys( response ).indexOf( 'simple_cart_fragments' ) >= 0 ) {
                    self.reloadFragments( response.simple_cart_fragments );
                }
            });
            $( document.body ).on( 'edd_cart_item_removed', function( e, response ) {
                if ( Object.keys( response ).indexOf( 'simple_cart_fragments' ) >= 0 ) {
                    self.reloadFragments( response.simple_cart_fragments );
                }
            });
            $( document.body ).on( 'edd_quantity_updated', function( e, response ) {
                if ( Object.keys( response ).indexOf( 'simple_cart_fragments' ) >= 0 ) {
                    self.reloadFragments( response.simple_cart_fragments );
                }
            });
        }

        this.reloadFragments = function reloadFragments( cart_fragments ) {
            if ( cart_fragments ) {
                $.each( cart_fragments, function ( key, value ) {
                    $( key ).replaceWith( value );
                } );
            }
        }

        this.togglePopup = function togglePopup() {
            $('.simple-cart-popup').toggleClass('open');
        }

        this.openPopup = function openPopup() {
            $('.simple-cart-popup').addClass('opening');
            setTimeout(function(){
                $('.simple-cart-popup').addClass('open');
                $('.simple-cart-popup').removeClass('opening');
            }, 900);

        }

        this.popupOpened = function popupOpened() {
            return $('.simple-cart-popup').hasClass('opening') || $('.simple-cart-popup').hasClass('open');
        }

        this.closePopup = function closePopup() {

            $('.simple-cart-popup').addClass('closing');
            setTimeout(function(){

                $('.simple-cart-popup').removeClass('open');
                $('.simple-cart-popup').removeClass('closing');
            }, 900);
        }

        return this;
    };
    $(function(){
        var cart = SimpleCart();
        cart.init();
    });
})(jQuery)