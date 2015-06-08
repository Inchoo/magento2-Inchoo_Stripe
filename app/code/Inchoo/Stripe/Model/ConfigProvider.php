<?php

namespace Inchoo\Stripe\Model;

class ConfigProvider extends \Magento\Payment\Model\CcGenericConfigProvider
{
    protected $methodCodes = ['inchoo_stripe'];
}