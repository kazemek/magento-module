<?php

class PayIntelligent_Ratepay_Model_Mysql4_Rateconfig_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Construct
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('ratepay/rateconfig');
    }
}