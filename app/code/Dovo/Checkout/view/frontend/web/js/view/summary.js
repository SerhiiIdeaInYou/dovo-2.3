/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/model/step-navigator'
], function (Component, totals, stepNavigator) {
    'use strict';

    return Component.extend({
        isLoading: totals.isLoading,
        checkVisible: function () {
            let index = stepNavigator.getActiveItemIndex();
            console.log('summary visible ' + index);
            if (index > 1) {
                return ko.observable(true);
            }
            return ko.observable(false);
        }
    });
});
