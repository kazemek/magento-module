<?php

class PayIntelligent_Ratepay_Model_Mysql4_Rateconfig extends Mage_Core_Model_Mysql4_Abstract
{
    
    /**
     * Construct
     */
    function _construct()
    {
        $this->_init('ratepay/rateconfig', 'id');
    }
}