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
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'inchoo_stripe',
                component: 'Inchoo_Stripe/js/view/payment/method-renderer/stripe-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);