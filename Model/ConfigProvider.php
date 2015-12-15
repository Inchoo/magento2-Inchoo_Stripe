<?php
/**
 * Stripe config provider
 *
 * @category    Inchoo
 * @package     Inchoo_Stripe
 * @author      Ivan Weiler & Stjepan Udovičić
 * @copyright   Inchoo (http://inchoo.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Inchoo\Stripe\Model;

class ConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    /**
     * @param \Magento\Payment\Gateway\ConfigInterface $config
     */
    public function __construct(
        \Magento\Payment\Gateway\ConfigInterface $config
    ){
        $this->config = $config;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        //$a = 2;
        //var_dump($this->config->getValue('publishable_key')); die();

        return [
            'payment' => [
                \Inchoo\Stripe\Model\Payment::CODE => [
                    'publishableKey' => $this->config->getValue('publishable_key')
                ]
            ]
        ];
    }

}