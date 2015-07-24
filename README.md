magento2-Inchoo_Stripe
======================

Stripe payment gateway Magento2 extension

Module was made for educational purposes only. You can read the full article [here](http://inchoo.net/magento-2/implementing-payment-gateway-magento-2/).

Other notes on extension: https://github.com/Inchoo/magento2-Inchoo_Stripe/wiki/Notes/

Install
=======

1. Go to Magento2 root folder

2. Enter following commands to install module:

    ```bash
    composer config repositories.inchoostripe git https://github.com/Inchoo/magento2-Inchoo_Stripe.git
    composer require inchoo/stripe:dev-master
    ```
   Wait while dependencies are updated.

3. Enter following commands to enable module:

    ```bash
    php bin/magento module:enable Inchoo_Stripe --clear-static-content
    php bin/magento setup:upgrade
    ```
4. Enable and configure Stripe in Magento Admin under Stores/Configuration/Payment Methods/Stripe


