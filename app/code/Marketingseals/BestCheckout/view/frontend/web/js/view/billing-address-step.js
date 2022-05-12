define(
    [
        'ko',
        'uiComponent',
        'underscore',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/step-navigator',
        'Marketingseals_BestCheckout/js/model/address-validator',
        'mage/translate'
    ],
    function (
        ko,
        Component,
        _,
        quote,
        stepNavigator,
        addressValidator,
        $t
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Marketingseals_BestCheckout/billing-step'
            },

            //add here your logic to display step,
            isVisible: ko.observable(quote.isVirtual()),
            isVirtual: quote.isVirtual(),

            /**
             *
             * @returns {*}
             */
            initialize: function () {
                this._super();
                if (quote.isVirtual()) { //update condition if you need to enable for virtual products
                    // register your step
                    stepNavigator.registerStep(
                        'virtual-billing-step',
                        null,
                        $t('Billing Address'),
                        this.isVisible, _.bind(this.navigate, this),
                        10
                    );
                }

            },

            /**
             * The navigate() method is responsible for navigation between checkout step
             * during checkout. You can add custom logic, for example some conditions
             * for switching to your custom step
             */
            navigate: function () {

            },

            /**
             * @returns void
             */
            navigateToNextStep: function () {
                if(addressValidator.validateBillingInformation(this.isFormInline, this.source)) {
                    stepNavigator.next();
                }
            }
        });
    }
);
