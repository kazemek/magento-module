<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category PayIntelligent
 * @package PayIntelligent_RatePAY
 * @copyright Copyright (c) 2011 PayIntelligent GmbH (http://www.payintelligent.de)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class RpPieperCustom_Ratepayelv_Model_Source_Directdebit_Currency
{
    /**
     * Define which Currencies are allowed for payment
     *
     * @return array
     */
    public function toOptionArray()
    {
        $currency = array(
            array(
                'label' => Mage::helper('core')->__('Euro'),
                'value' => 'EUR'
            )
        );
        return $currency;
    }
}

