define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/form',
    'ko',
    'Magento_Customer/js/model/customer',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/address-converter',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/create-shipping-address',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/action/create-billing-address',
    'Magento_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/checkout-data'
], function (
    $,
    _,
    Component,
    ko,
    customer,
    addressList,
    addressConverter,
    quote,
    createShippingAddress,
    selectShippingAddress,
    createBillingAddress,
    selectBillingAddress,
    setShippingInformationAction,
    stepNavigator,
    checkoutData
) {
    'use strict';

    return function (target) {

        return target.extend({
            initialize: function () {
                this.visible = ko.observable(customer.isLoggedIn() || false);// set visible to be initially false to have your step show first
                this._super();
                return this;
            },
            defaults: {
                template: 'Dovo_Checkout/address'
            },
            isLoggedIn: customer.isLoggedIn(),

            emailVisible: function () {
                if (!customer.isLoggedIn()) {
                    return true;
                }
                return false;
            },
            formVisible: function () {
                if (customer.isLoggedIn()) {
                    if (addressList().length > 0) {
                        return true;
                    }
                }
            },
            inputFocus: function () {
                console.log('grooting was');
                /*var inputText = $('.field input[type="text"]');
                var inputPasswort = $('.field input[type="password"]');
                var inputEmail = $('.field input[type="email"]');
                console.log(inputText);
                inputText.focus(function () {
                    //console.log($(this).parent().parent().find('label'));

                    $(this).parent().parent().find('label').hide();
                });
                inputText.blur(function () {
                    if ($(this).val().length === 0) {
                        $(this).parent().parent().find('label').show();
                    }
                });
                inputEmail.focus(function () {
                    $(this).parent().parent().find('label').hide();
                });
                inputEmail.blur(function () {
                    if ($(this).val().length === 0) {
                        $(this).parent().parent().find('label').show();
                    }
                });
                inputPasswort.focus(function () {
                    $(this).parent().parent().find('label').hide();
                });
                inputPasswort.blur(function () {
                    if ($(this).val().length === 0) {
                        $(this).parent().parent().find('label').show();
                    }
                });*/
            },
            setShippingInformation: function () {
                if (this.validateShippingInformation() && this.validateBillingInformation()) {
                    setShippingInformationAction().done(
                        function () {
                            stepNavigator.next();
                        }
                    );
                }
            },
            validateBillingInformation: function () {

                if ($('[name="billing-address-same-as-shipping"]').is(":checked")) {
                    if (this.isFormInline) {
                        var shippingAddress = quote.shippingAddress();
                        var addressData = addressConverter.formAddressDataToQuoteAddress(
                            this.source.get('shippingAddress')
                        );
                        //Copy form data to quote shipping address object
                        for (var field in addressData) {

                            if (addressData.hasOwnProperty(field) &&
                                shippingAddress.hasOwnProperty(field) &&
                                typeof addressData[field] != 'function' &&
                                _.isEqual(shippingAddress[field], addressData[field])
                            ) {
                                shippingAddress[field] = addressData[field];
                            } else if (typeof addressData[field] != 'function' &&
                                !_.isEqual(shippingAddress[field], addressData[field])) {
                                shippingAddress = addressData;
                                break;
                            }
                        }

                        if (customer.isLoggedIn()) {
                            shippingAddress.save_in_address_book = 1;
                        }
                        var newBillingAddress = createBillingAddress(shippingAddress);
                        selectBillingAddress(newBillingAddress);
                    } else {
                        selectBillingAddress(quote.shippingAddress());
                    }

                    return true;
                }

                var selectedAddress = $('[name="billing_address_id"]').val();
                if (selectedAddress) {
                    var res = addressList.some(function (addressFromList) {
                        if (selectedAddress == addressFromList.customerAddressId) {
                            selectBillingAddress(addressFromList);
                            return true;
                        }
                        return false;
                    });

                    return res;
                }

                this.source.set('params.invalid', false);
                this.source.trigger('billingAddress.data.validate');

                if (this.source.get('params.invalid')) {
                    return false;
                }

                var addressData = this.source.get('billingAddress'),
                    newBillingAddress;

                if ($('#billing-save-in-address-book').is(":checked")) {
                    addressData.save_in_address_book = 1;
                }

                newBillingAddress = createBillingAddress(addressData);
                selectBillingAddress(newBillingAddress);

                return true;
            }
        });
    }
});