<?php
namespace Inchoo\Stripe\Model;

class Payment extends \Magento\Payment\Model\Method\Cc
{
	const CODE = 'inchoo_stripe';
	
	protected $_code = self::CODE;

	protected $_isGateway                   = true;
	protected $_canCapture                  = true;
	protected $_canCapturePartial           = true;
	protected $_canRefund                   = true;
		
	public function capture(\Magento\Framework\Object $payment, $amount)
	{
		
		throw new \Magento\Framework\Model\Exception(__('Stop'));
	
		return $this;
	}	
	
}