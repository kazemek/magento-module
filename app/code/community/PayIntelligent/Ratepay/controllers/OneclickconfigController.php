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

class PayIntelligent_Ratepay_OneclickconfigController extends Mage_Adminhtml_Controller_Action
{
    /**
     * payment method label reference array
     *
     * @var array
     */
    private $paymentMethods = array('invoice' => 'rechnung',
                                    'installment' => 'rate',
                                    'elv' => 'directdebit',
                                    'prepayment' => 'prepayment');

    /**
     * Sets config und go back to adminhtml
     *
     */
    public function configAction()
    {
        $this->_setConfiguration($this->_callConfigRequest());

        $this->backToConfiguration();
    }

    /**
     * Render the config layout
     * 
     * @return PayIntelligent_Ratepay_Adminhtml_ConfigController
     */
    protected function backToConfiguration()
    {
        //$this->_redirect('ratepay/adminhtml_configuration/saved');
        $this->_redirect('*/adminhtml_configuration/index');
    }

    /**
     * Call profile_request and retrieve response
     *
     * @return boolean|array
     */
    protected function _callConfigRequest()
    {
        $client = Mage::getModel('ratepay/request');
        $headInfo = array(
            'securityCode' => Mage::getStoreConfig('payment/ratepay_rate/security_code'),
            'profileId' => Mage::getStoreConfig('payment/ratepay_rate/profile_id'),
            'transactionId' => '',
            'transactionShortId' => '',
            'orderId' => ''
        );

        $loggingInfo = array(
            'logging'       => false,
            'requestType'   => 'PROFILE_REQUEST',
            'sandbox'       => Mage::getStoreConfig('payment/ratepay_rate/sandbox')
        );

        return $client->callProfileRequest($headInfo, $loggingInfo);
    }

    /**
     * Sets merchant and installment configurationen
     */
    protected function _setConfiguration($config)
    {
        $this->_setMerchantConfig($config['merchant_config']);
        $this->_setInstallmentConfig($config['installment_config']);
    }

    /**
     * Sets merchant specific configuration. PROFILE_RESPONSE
     */
    protected function _setMerchantConfig($merchantConfig)
    {
        $coreConfig = Mage::getModel('core/config');

        foreach($this->paymentMethods as $prMethod => $dbMethod) {
            $coreConfig->saveConfig('payment/ratepay_' . $dbMethod . '/active', (($merchantConfig['merchant-status'] == 2) && ($merchantConfig['activation-status-' . $prMethod] == 2)  && ($merchantConfig['eligibility-ratepay-' . $prMethod] == 'yes')) ? 1 : 0);

            $coreConfig->saveConfig('payment/ratepay_' . $dbMethod . '/min_order_total', $merchantConfig['tx-limit-' . $prMethod . '-min']);
            $coreConfig->saveConfig('payment/ratepay_' . $dbMethod . '/max_order_total', $merchantConfig['tx-limit-' . $prMethod . '-max']);

            $coreConfig->saveConfig('payment/ratepay_' . $dbMethod . '/b2b', ($merchantConfig['b2b-' . $prMethod] == 'yes') ? 1 : 0);
            $coreConfig->saveConfig('payment/ratepay_' . $dbMethod . '/delivery_address', ($merchantConfig['delivery-address-' . $prMethod] == 'yes') ? 1 : 0);
        }
    }

    /**
     * Sets installment specific configuration. CONFIGURATION_RESPONSE
     */
    protected function _setInstallmentConfig($installmentConfig)
    {
        Mage::getModel('ratepay/rateconfig')->setConfig($installmentConfig);
    }
}