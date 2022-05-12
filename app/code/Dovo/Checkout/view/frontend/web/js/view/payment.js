/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        "underscore",
        'uiComponent',
        'ko',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/action/get-payment-information',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'mage/translate',
        'Magento_Ui/js/model/messageList'
    ],
    function (
        $,
        _,
        Component,
        ko,
        quote,
        stepNavigator,
        paymentService,
        methodConverter,
        getPaymentInformation,
        checkoutDataResolver,
        $t,
        messageList
    ) {
        'use strict';

        /** Set payment methods to collection */
        paymentService.setPaymentMethods(methodConverter(window.checkoutConfig.paymentMethods));

        return Component.extend({
            defaults: {
                template: 'Dovo_Checkout/payment',
                activeMethod: ''
            },
            isVisible: ko.observable(quote.isVirtual()),
            quoteIsVirtual: quote.isVirtual(),
            isPaymentMethodsAvailable: ko.computed(function () {
                return paymentService.getAvailablePaymentMethods().length > 0;
            }),

            initialize: function () {
                this._super();
                checkoutDataResolver.resolvePaymentMethod();
                stepNavigator.registerStep(
                    'payment',
                    null,
                    $t('Review & Payments'),
                    this.isVisible,
                    _.bind(this.navigate, this),
                    20
                );
                return this;
            },

            navigate: function () {
                var self = this;
                getPaymentInformation().done(function () {
                    self.isVisible(true);
                });
            },

            navigateTo: function () {
                stepNavigator.navigateTo('shipping');

            },

            getFormKey: function () {
                return window.checkoutConfig.formKey;
            },
            triggerOrder: function () {
                let paymentMethod = $('.payment-method._active');
                console.log(paymentMethod);
                if (paymentMethod.length != 0) {
                    if (!$('[name="agreeTerms"]').is(":checked")) {
                        //$('[name="agreeTerms"]').parent().find('.boxbox').css("border", "2px solid #e02b27");
                        messageList.addErrorMessage({message: $.mage.__('AGB-Message')});
                        return false;
                    } else {
                        paymentMethod.find('.action.primary.checkout').trigger('click');
                    }
                } else {
                    //Send MEssage
                }

            }
        });
    }
);
