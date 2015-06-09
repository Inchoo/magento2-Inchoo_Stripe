<?php

/**
 * Stripe payment method model
 *
 * @category	Inchoo
 * @package		Inchoo_Stripe
 * @author		Ivan Weiler <ivan.weiler@inchoo.net> & Stjepan Udovičić <stjepan.udovicic@inchoo.net>
 * @copyright	Inchoo (http://inchoo.net)
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Inchoo\Stripe\Model;

class Payment extends \Magento\Payment\Model\Method\Cc
{
    const CODE = 'inchoo_stripe';

    protected $_code = self::CODE;

    protected $_isGateway                   = true;
    protected $_canCapture                  = true;
    protected $_canCapturePartial           = true;
    protected $_canRefund                   = true;
    protected $_canRefundInvoicePartial     = true;

    protected $_stripeApi = false;

    protected $_minAmount = null;
    protected $_maxAmount = null;
    protected $_supportedCurrencyCodes = array('USD');

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger, //added in 0.74.0-beta12
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Stripe\Stripe $stripe,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,  //added in 0.74.0-beta12
            $moduleList,
            $localeDate,
            null,
            null,
            $data
        );

        $this->_stripeApi = $stripe;
        $this->_stripeApi->setApiKey(
            $this->getConfigData('api_key')
        );

        $this->_minAmount = $this->getConfigData('min_order_total');
        $this->_maxAmount = $this->getConfigData('max_order_total');
    }

    /**
     * Payment capturing
     *
     * @param \Magento\Framework\Object $payment
     * @param float $amount
     * @return $this
     * @throws \Magento\Framework\Validator\Exception
     */
    public function capture(\Magento\Framework\Object $payment, $amount)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();

        /** @var \Magento\Sales\Model\Order\Address $billing */
        $billing = $order->getBillingAddress();

        try {
            $charge = \Stripe\Charge::create(array(
                'amount'        => $amount * 100,
                'currency'      => strtolower($order->getBaseCurrencyCode()),
                'description'   => sprintf('#%s, %s', $order->getIncrementId(), $order->getCustomerEmail()),
                'card'          => array(
                    'number'            => $payment->getCcNumber(),
                    'exp_month'         => sprintf('%02d',$payment->getCcExpMonth()),
                    'exp_year'          => $payment->getCcExpYear(),
                    'cvc'               => $payment->getCcCid(),
                    'name'              => $billing->getName(),
                    'address_line1'     => $billing->getStreet(1),
                    'address_line2'     => $billing->getStreet(2),
                    'address_zip'       => $billing->getPostcode(),
                    'address_state'     => $billing->getRegion(),
                    'address_country'   => $billing->getCountry(),
                ),
            ));

            $payment
                ->setTransactionId($charge->id)
                ->setIsTransactionClosed(0);
        } catch (\Exception $e) {
            $this->debugData($e->getMessage());
            $this->_logger->error(__('Payment capturing error.'));
            throw new \Magento\Framework\Validator\Exception(__('Payment capturing error.'));
        }

        return $this;
    }

    /**
     * Payment refund
     *
     * @param \Magento\Framework\Object $payment
     * @param float $amount
     * @return $this
     * @throws \Magento\Framework\Validator\Exception
     */
    public function refund(\Magento\Framework\Object $payment, $amount)
    {
        $transactionId = $payment->getParentTransactionId();

        try {
            \Stripe\Charge::retrieve($transactionId)->refund();
        } catch (\Exception $e) {
            $this->debugData($e->getMessage());
            $this->_logger->error(__('Payment refunding error.'));
            throw new \Magento\Framework\Validator\Exception(__('Payment refunding error.'));
        }

        $payment
            ->setTransactionId($transactionId . '-' . \Magento\Sales\Model\Order\Payment\Transaction::TYPE_REFUND)
            ->setParentTransactionId($transactionId)
            ->setIsTransactionClosed(1)
            ->setShouldCloseParentTransaction(1);

        return $this;
    }

    /**
     * Determine method availability based on quote amount and config data
     *
     * @param null $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        if ($quote && (
            $quote->getBaseGrandTotal() < $this->_minAmount
            || ($this->_maxAmount && $quote->getBaseGrandTotal() > $this->_maxAmount))
        ) {
            return false;
        }

        if (!$this->getConfigData('api_key')) {
            return false;
        }

        return parent::isAvailable($quote);
    }

    /**
     * Availability for currency
     *
     * @param string $currencyCode
     * @return bool
     */
    public function canUseForCurrency($currencyCode)
    {
        if (!in_array($currencyCode, $this->_supportedCurrencyCodes)) {
            return false;
        }
        return true;
    }
}