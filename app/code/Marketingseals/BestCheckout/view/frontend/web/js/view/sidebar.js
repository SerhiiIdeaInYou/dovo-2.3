/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'ko',
    'jquery',
    'Magento_Checkout/js/model/sidebar',
    'Magento_Checkout/js/model/step-navigator'
], function (Component, ko, $, sidebarModel, stepNavigator) {
    'use strict';

    return Component.extend({
        /**
         * @param {HTMLElement} element
         */
        setModalElement: function (element) {
            sidebarModel.setPopup($(element));
        },
        checkVisible: function() {
            let checkoutNode = $('#checkout');
            if(stepNavigator.getActiveItemIndex() <= 1) {
                checkoutNode.addClass('customer-step');
                return false;
            }
            checkoutNode.removeClass('customer-step');
            return true;
        }
    });
});
