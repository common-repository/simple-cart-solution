!function(t){var o={};function e(n){if(o[n])return o[n].exports;var r=o[n]={i:n,l:!1,exports:{}};return t[n].call(r.exports,r,r.exports,e),r.l=!0,r.exports}e.m=t,e.c=o,e.d=function(t,o,n){e.o(t,o)||Object.defineProperty(t,o,{enumerable:!0,get:n})},e.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},e.t=function(t,o){if(1&o&&(t=e(t)),8&o)return t;if(4&o&&"object"==typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(e.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:t}),2&o&&"string"!=typeof t)for(var r in t)e.d(n,r,function(o){return t[o]}.bind(null,r));return n},e.n=function(t){var o=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(o,"a",o),o},e.o=function(t,o){return Object.prototype.hasOwnProperty.call(t,o)},e.p="",e(e.s=3)}([,,,function(t,o,e){t.exports=e(4)},function(t,o){var e;e=jQuery,wp.customize("simple_cart_button_bg",(function(t){t.bind((function(t){e(".simple-cart-popup-button").css("background-color",t),e("body .simple-cart-popup-button .simple-cart-popup-button-actions span").css("border-color",t),e("body .simple-cart-popup-button .simple-cart-popup-button-actions span").css("color",t)}))})),wp.customize("simple_cart_button_color",(function(t){t.bind((function(t){e(".simple-cart-popup-button").css("color",t)}))})),wp.customize("simple_cart_button_position",(function(t){t.bind((function(t){var o="right"===t?"right":"left",n="right"===t?"left":"right";e(".simple-cart-popup-button, .simple-cart-popup").css(n,"auto"),e(".simple-cart-popup-button, .simple-cart-popup").css(o,"20px")}))}))}]);
//# sourceMappingURL=customizer.js.map