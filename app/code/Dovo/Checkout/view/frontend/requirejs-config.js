var config = {
    map: {
        '*': {
            'Magento_Checkout/js/view/progress-bar.js':
                'Dovo_Checkout/js/view/progress-bar.js',
            'Magento_Paypal/template/payment/paypal-express.html':
                'Dovo_Checkout/template/paypal/paypal-express.html',
            'Magento_Paypal/template/payment/paypal-express-in-context.html':
                'Dovo_Checkout/template/paypal/paypal-express-in-context.html',
            'Magento_Paypal/template/payment/paypal-express-bml.html':
                'Dovo_Checkout/template/paypal/paypal-express-bml.html',
            'Magento_Checkout/template/summary/cart-items.html':
                'Dovo_Checkout/template/summary/cart-items.html',
            'Magento_Checkout/template/shipping-information.html':
                'Dovo_Checkout/template/shipping-information.html',
            'Magento_Ui/template/form/field.html':
                'Dovo_Checkout/template/form/field.html',
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
