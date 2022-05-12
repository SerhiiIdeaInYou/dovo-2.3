/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'ko',
    'uiComponent',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/step-navigator'
], function ($, _, ko, Component, customer, stepNavigator) {
    'use strict';

    var steps = stepNavigator.steps;

    return Component.extend({
        defaults: {
            template: 'Dovo_Checkout/progress-bar',
            visible: true
        },
        steps: steps,

        /** @inheritdoc */
        initialize: function () {
            this._super();
            window.addEventListener('hashchange', _.bind(stepNavigator.handleHash, stepNavigator));

            if (!window.location.hash) {
                stepNavigator.setHash(stepNavigator.steps().sort(stepNavigator.sortItems)[0].code);
            }

            stepNavigator.handleHash();
        },

        /**
         * @param {*} itemOne
         * @param {*} itemTwo
         * @return {*|Number}
         */
        sortItems: function (itemOne, itemTwo) {
            return stepNavigator.sortItems(itemOne, itemTwo);
        },

        /**
         * @param {Object} step
         */
        navigateTo: function (step) {
            console.log(step);
            stepNavigator.navigateTo(step.code);
        },

        /**
         * @param {Object} item
         * @return {*|Boolean}
         */
        isProcessed: function (item) {
            console.log(item);
            return stepNavigator.isProcessed(item.code);
        }
    });
});
