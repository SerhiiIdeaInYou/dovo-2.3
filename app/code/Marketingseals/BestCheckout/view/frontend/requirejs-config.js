var config = {
    map: {
        '*': {
            'Magento_Paypal/template/payment/paypal-express.html':
                'Marketingseals_BestCheckout/template/paypal/paypal-express.html',
            'Magento_Paypal/template/payment/paypal-express-in-context.html':
                'Marketingseals_BestCheckout/template/paypal/paypal-express-in-context.html'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'Marketingseals_BestCheckout/js/mixin/shipping-mixin': true
            },
            'Amazon_Payment/js/view/shipping': {
                'Marketingseals_BestCheckout/js/mixin/shipping-amazon-mixin': true
            }
        }
    }
};
