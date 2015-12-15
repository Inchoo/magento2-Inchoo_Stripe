/**
 * Inchoo_Stripe Magento JS component
 *
 * @category    Inchoo
 * @package     Inchoo_Stripe
 * @author      Ivan Weiler & Stjepan Udovičić
 * @copyright   Inchoo (http://inchoo.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Payment/js/view/payment/cc-form',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Payment/js/model/credit-card-validation/validator',
        'stripejs'
    ],
    function ($, Component, placeOrderAction, fullScreenLoader, additionalValidators) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Inchoo_Stripe/payment/stripe-form'
            },

            getCode: function() {
                return 'inchoo_stripe';
            },

            isActive: function() {
                return true;
            },

            /**
             * @override
             */
            initialize: function() {
                this._super();
                Stripe.setPublishableKey(this.getPublishableKey());
            },

            /**
             * @override
             * @todo: possible to implement with this._super() ?
             */
            placeOrder: function (data, event) {
                var self = this,
                    placeOrder;

                if (event) {
                    event.preventDefault();
                }

                if (this.validate() /*&& additionalValidators.validate()*/) {

                    this.isPlaceOrderActionAllowed(false);
                    fullScreenLoader.startLoader();

                    $.when(this.createToken()).done(function() {

                        placeOrder = placeOrderAction(self.getData(), self.redirectAfterPlaceOrder, self.messageContainer);

                        $.when(placeOrder).fail(function() {
                            fullScreenLoader.stopLoader();
                            self.isPlaceOrderActionAllowed(true);
                        });

                    }).fail(function(result) {

                        fullScreenLoader.stopLoader();
                        self.isPlaceOrderActionAllowed(true);

                        self.messageContainer.addErrorMessage({
                            'message': result
                        });
                    });

                    return true;
                }
                return false;
            },

            /**
             * Creates Stripe token from cc data on client side
             */
            createToken: function() {
                var self = this;

                var cardInfo = {
                    number: this.creditCardNumber(),
                    exp_month: this.creditCardExpMonth(),
                    exp_year: this.creditCardExpYear(),
                    cvc: this.creditCardVerificationNumber()
                };

                var deffer = $.Deferred();

                Stripe.card.createToken(cardInfo, function(status, response) {
                    if (response.error) {
                        deffer.reject(response.error.message);
                    } else {
                        self.token = response.id;
                        deffer.resolve();
                    }
                });

                return deffer.promise();
            },

            /**
             * @override
             */
            getData: function() {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'cc_last4': this.creditCardNumber().slice(-4),
                        'cc_token': this.token,
                        'cc_type': this.creditCardType(),
                        'cc_exp_year': this.creditCardExpYear(),
                        'cc_exp_month': this.creditCardExpMonth()
                    }
                };
            },

            getPublishableKey: function () {
                return window.checkoutConfig.payment[this.getCode()].publishableKey;
            },

            /**
             * Validates cc form
             */
            validate: function() {
                var $form = $('#' + this.getCode() + '-form');
                return $form.validation() && $form.validation('isValid');
            }
        });
    }
);
