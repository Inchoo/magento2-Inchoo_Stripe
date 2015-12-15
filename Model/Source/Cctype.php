<?php
/**
 * Payment CC Types Source Model
 *
 * @category    Inchoo
 * @package     Inchoo_Stripe
 * @author      Ivan Weiler & Stjepan Udovičić
 * @copyright   Inchoo (http://inchoo.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Inchoo\Stripe\Model\Source;

class Cctype extends \Magento\Payment\Model\Source\Cctype
{
    /**
     * @return array
     */
    public function getAllowedTypes()
    {
        return array('VI', 'MC', 'AE', 'DI', 'JCB', 'OT');
    }
}
