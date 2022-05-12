/*jshint browser:true*/
/*global define*/
define(
    [
        'ko',
        'underscore',
        'jquery',
        'Magento_Ui/js/form/form',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/create-billing-address',
        'Magento_Checkout/js/action/select-billing-address',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Magento_Customer/js/customer-data',
        'Magento_Checkout/js/action/set-billing-address',
        'Magento_Ui/js/model/messageList',
        'mage/translate'
    ],
    function (
        ko,
        _,
        $,
        Component,
        customer,
        addressList,
        quote,
        createBillingAddress,
        selectBillingAddress,
        checkoutData,
        checkoutDataResolver,
        customerData,
        setBillingAddressAction,
        globalMessageList,
        $t
    ) {
        'use strict';

        var newAddressOption = {
                /**
                 * Get new address label
                 * @returns {String}
                 */
                getAddressInline: function () {
                    return $t('New Address');
                },
                customerAddressId: null
            },
            countryData = customerData.get('directory-data'),
            addressOptions = addressList().filter(function (address) {
                return address.getType() == 'customer-address';
            });

        addressOptions.push(newAddressOption);

        return Component.extend({
            defaults: {
                template: 'Dovo_Checkout/billing-address'
            },
            currentBillingAddress: quote.billingAddress,
            addressOptions: addressOptions,
            customerHasAddresses: addressOptions.length > 1,

            /**
             * Init component
             */
            initialize: function () {
                this._super();
            },

            /**
             * @return {exports.initObservable}
             */
            initObservable: function () {
                this._super()
                    .observe({
                        selectedAddress: null,
                        isAddressFormVisible: false,
                        isAddressSameAsShipping: true,
                        saveInAddressBook: 1,
                        isAddressFormListVisible: false
                    });

                return this;
            },

            canUseShippingAddress: ko.computed(function () {
                return !quote.isVirtual() && quote.shippingAddress() && quote.shippingAddress().canUseForBilling();
            }),

            /**
             * @param {Object} address
             * @return {*}
             */
            addressOptionsText: function (address) {
                return address.getAddressInline();
            },

            /**
             * @return {Boolean}
             */
            useShippingAddress: function () {
                if (this.isAddressSameAsShipping()) {
                    this.isAddressFormVisible(false);
                    this.isAddressFormListVisible(false);
                } else {
                    if (addressOptions.length == 1) {
                        this.isAddressFormVisible(true);
                    } else {
                        this.isAddressFormListVisible(true);
                    }
                }
                return true;
            },
            /**
             * @param {Object} address
             */
            onAddressChange: function (address) {
                if (address) {
                    this.isAddressFormVisible(false);
                } else {
                    this.isAddressFormVisible(true);
                }
            },

            /**
             * @param {int} countryId
             * @return {*}
             */
            getCountryName: function (countryId) {
                return countryData()[countryId] != undefined ? countryData()[countryId].name : '';
            },

            /**
             * Get code
             * @param {Object} parent
             * @returns {String}
             */
            getCode: function (parent) {
                return _.isFunction(parent.getCode) ? parent.getCode() : 'shared';
            },
            inputFocus: function () {
                console.log('grooting wassss');
                /*var inputText = $('.field input[type="text"]');
                var inputPasswort = $('.field input[type="password"]');
                var inputEmail = $('.field input[type="email"]');
                console.log(inputText);
                inputText.on('animationstart', function (e) {
                    console.log(e);
                });
                if (inputText.val().length !== 0) {
                    inputText.parent().parent().find('label').hide();
                }
                if (inputEmail.val().length !== 0) {
                    inputEmail.parent().parent().find('label').hide();
                }
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
            }
        });
    }
);