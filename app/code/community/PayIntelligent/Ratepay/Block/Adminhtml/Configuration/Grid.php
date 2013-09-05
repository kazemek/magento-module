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

class PayIntelligent_Ratepay_Block_Adminhtml_Configuration_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    
    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('configurationGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
    }

    /**
     * Prepare Collection
     *
     * @return PayIntelligent_Ratepay_Block_Adminhtml_Logs_Grid
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->_getConfigCollection());
        return parent::_prepareCollection();
    }
    
    /**
     * Retrieve config data
     * 
     * @return stdClass
     */
    protected function _getConfigCollection()
    {
        $data = $this->_callConfigRequest();
        if ($data) {
            $collection = new Varien_Data_Collection();
            //print_r($data);

            foreach ($data as $key => $subdata) {
                foreach ($subdata as $subkey => $value) {
                    $obj = new Varien_Object();
                    if(!strpos($subkey, 'prepayment')) { // hiding prepayment
                        $obj->addData(array('category' =>__(Mage::helper('ratepay')->__($key)) ,'key' => $this->__($subkey), 'value' => $value));
                        $collection->addItem($obj);
                    }
                }
            }
                    
            return $collection;
        }
        return null;
    }
    
    /**
     * Call configuration_request and retrieve response
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
     * Prepare Columns
     *
     * @return PayIntelligent_Ratepay_Block_Adminhtml_Logs_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('category', array(
            'header'    => Mage::helper('ratepay')->__('Pi category'),
            'index'     => 'category',
            'filter'    => false,
            'sortable'  => false
        ));

        $this->addColumn('key', array(
            'header'    => Mage::helper('ratepay')->__('Pi key'),
            'index'     => 'key',
            'filter'    => false,
            'sortable'  => false
        ));

        $this->addColumn('value', array(
            'header'    => Mage::helper('ratepay')->__('Pi value'),
            'index'     => 'value',
            'filter'    => false,
            'sortable'  => false
        ));
        
        return parent::_prepareColumns();
    }

}