/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'Magento_Ui/js/form/form',
    'Magento_Customer/js/action/login',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Customer/js/model/customer',
    'mage/validation',
    'Magento_Checkout/js/model/authentication-messages',
    'Magento_Checkout/js/model/full-screen-loader'
], function ($, ko, Component, loginAction, stepNavigator, customer, validation, messageContainer, fullScreenLoader) {
    'use strict';

    var checkoutConfig = window.checkoutConfig;

    return Component.extend({
        isGuestCheckoutAllowed: checkoutConfig.isGuestCheckoutAllowed,
        isCustomerLoginRequired: checkoutConfig.isCustomerLoginRequired,
        registerUrl: checkoutConfig.registerUrl,
        forgotPasswordUrl: checkoutConfig.forgotPasswordUrl,
        autocomplete: checkoutConfig.autocomplete,
        defaults: {
            template: 'Dovo_Checkout/authentication'
        },

        //add here your logic to display step,
        isVisible: ko.observable(!customer.isLoggedIn()),
        ifVisible: function () {
            /*if ($("input.ms-login-email").val().length === 0) {
                this.focusVisibilityEmail(true);
            } else {
                this.focusVisibilityEmail(false);
            }
            if ($("input.ms-login-password").val().length === 0) {
                this.focusVisibilityPW(true);
            } else {
                this.focusVisibilityPW(false);
            }*/

        },
        focusEmail: function () {
            /*let email = $("input.ms-login-email");
            if (email.is(':focus')) {
                this.focusVisibilityEmail(false);
            } else {
                if (email.val().length === 0) {
                    this.focusVisibilityEmail(true);
                }
            }*/
        },
        focusPW: function () {
            /*let password = $("input.ms-login-password");
            if (password.is(':focus')) {
                this.focusVisibilityPW(false);
            } else {
                if (password.val().length === 0) {
                    this.focusVisibilityPW(true);
                }
            }*/
        },
        /**
         * Is login form enabled for current customer.
         *
         * @return {Boolean}
         */
        isActive: function () {
            return !customer.isLoggedIn();
        },
        focusVisibilityEmail: ko.observable(true),
        focusVisibilityPW: ko.observable(true),
        /**
         * Provide login action.
         *
         * @param {HTMLElement} loginForm
         */
        login: function (loginForm) {
            var loginData = {},
                formDataArray = $(loginForm).serializeArray();

            formDataArray.forEach(function (entry) {
                loginData[entry.name] = entry.value;
            });

            if ($(loginForm).validation() &&
                $(loginForm).validation('isValid')
            ) {
                fullScreenLoader.startLoader();
                loginAction(loginData, checkoutConfig.checkoutUrl, undefined, messageContainer).always(function () {
                    fullScreenLoader.stopLoader();
                });
            }
        }
    });
});
