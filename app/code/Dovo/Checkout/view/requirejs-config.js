var config = {
    map: {
        '*': {
            'Magento_Checkout/js/view/progress-bar.js':
                'Dovo_Checkout/js/view/progress-bar.js',
            'Magento_Checkout/js/model/step-navigator.js':
                'Dovo_Checkout/js/model/step-navigator.js',

            'Magento_Checkout/template/summary/cart-items.html':
                'Dovo_Checkout/template/summary/cart-items.html',
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'Dovo_Checkout/js/mixin/shipping-mixin': true
            }
        }
    }
};
