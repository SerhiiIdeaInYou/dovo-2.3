define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'underscore',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Customer/js/model/customer',
        'mage/translate'
    ],
    function (
        $,
        ko,
        Component,
        _,
        stepNavigator,
        customer,
        $t
    ) {
        'use strict';
        /**
         *
         * mystep - is the name of the component's .html template,
         * <Vendor>_<Module>  - is the name of the your module directory.
         *
         */
        return Component.extend({
            defaults: {
                template: 'Marketingseals_BestCheckout/signup'
            },

            //add here your logic to display step,
            isVisible: ko.observable(!customer.isLoggedIn()),

            //add here your logic to display step,
            //isVisible: ko.observable(true),
            isLoggedIn: customer.isLoggedIn(),
            //step code will be used as step content id in the component template
            stepCode: 'sign-up',
            //step title value
            stepTitle: $t('Sign Up Step'),
            //registerFormVisible: ko.observable(true),
            registerVisible: ko.observable(false),
            registerFormVisible: function() {
                if(this.checkRegister() === 'guest') {
                    return false;
                }
                if(this.checkRegister() === 'register') {
                    return true;
                }
            },
            guestFormVisible: function() {
                if(this.checkRegister() === 'guest') {

                    return true;
                }
                if(this.checkRegister() === 'register') {
                    return false;
                }
            },
            welcomeFormVisible: ko.observable(true),
            checkRegister: ko.observable("register"),
            /*function() {
                if(this.registerVisible()) {
                    this.registerFormVisible(true);
                    this.guestFormVisible(false)
                }
            },*/
            checkGuest: function() {
                if(this.registerVisible()) {
                    this.registerFormVisible(false);
                    this.guestFormVisible(true);
                }
            },
            showRegister: function () {
                let registerForm = this.registerVisible();
                if (!registerForm) {
                    this.registerVisible(true);
                    this.welcomeFormVisible(false);
                }
            },
            /**
             *
             * @returns {*}
             */
            initialize: function () {
                this._super();
                // register your step
                stepNavigator.registerStep(
                    //step code will be used as step content id in the component template
                    'sign-up',
                    //step alias
                    null,
                    //step title value
                    'Sign Up Step',
                    //observable property with logic when display step or hide step
                    this.isVisible,

                    _.bind(this.navigate, this),

                    /**
                     * sort order value
                     * 'sort order value' < 10: step displays before shipping step;
                     * 10 < 'sort order value' < 20 : step displays between shipping and payment step
                     * 'sort order value' > 20 : step displays after payment step
                     */
                    5
                );
                /*console.log(this.isLoggedIn);
                if (this.isLoggedIn) {
                    this.navigateToNextStep();
                    //stepNavigator.navigateTo('shipping');
                }*/
                return this;
            },
            checkboxChangeRegister: function () {
                this.registerFormVisible(true);
                this.checkGuest(false);
            },
            checkboxChangeGuest: function() {
                this.registerFormVisible(false);
                this.checkRegister(false);
            },
            /**
             * The navigate() method is responsible for navigation between checkout step
             * during checkout. You can add custom logic, for example some conditions
             * for switching to your custom step
             */
            navigate: function (step) {
                console.log('navigate');
                if (this.isLoggedIn) {
                    stepNavigator.setHash('shipping');
                   //this.navigateToNextStep();

                }
                //step && step.isVisible(true);
            },

            /**
             * @returns void
             */
            navigateToNextStep: function () {
                stepNavigator.next();
                /*console.log(stepNavigator.getActiveItemIndex());
                $('#checkout').removeClass('customer-step');*/

            }
        });
    }
);
