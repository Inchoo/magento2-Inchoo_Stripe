/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'Magento_Payment/js/view/payment/cc-form'
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            getCode: function() {
                return 'inchoo_stripe';
            },

            isActive: function() {
                return true;
            }
        });
    }
);
