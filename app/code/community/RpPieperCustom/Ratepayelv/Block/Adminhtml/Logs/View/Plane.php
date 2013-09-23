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

class RpPieperCustom_Ratepayelv_Block_Adminhtml_Logs_View_Plane extends Mage_Adminhtml_Block_Widget_Form
{
        /**
         * Prepare form before rendering HTML
         *
         * @return PayIntelligent_Ratepay_Block_Adminhtml_Logs_View_Plane
         */
	protected function _prepareForm()
	{
            $this->setTemplate('ratepayelv/logs/view.phtml');
            return parent::_prepareForm();
	}

        /**
         * Returns Logging Model
         *
         * @return PayIntelligent_Ratepay_Model_Logging
         */
	public function getEntry()
	{
	    return Mage::registry('ratepayelv_logging_data');
	}

        /**
         * Gets the formatted Request Xml
         *
         * @return string
         */
	public function getRequest()
	{
	    return $this->_formatXml($this->getEntry()->getRequest());
	}

        /**
         * Gets the formatted Response Xml
         *
         * @return string
         */
	public function getResponse()
	{
	    return $this->_formatXml($this->getEntry()->getResponse());
	}

        /**
         * Formats Xml
         *
         * @return string
         */
	protected function _formatXml($xmlString)
	{
            $str = str_replace("\n", "", $xmlString);
            $xml = new DOMDocument('1.0');
            $xml->preserveWhiteSpace = false;
            $xml->formatOutput = true;
            $xml->loadXML($str);
            $var = $xml->saveXML();
	    return htmlentities(utf8_decode($var));
	}

}