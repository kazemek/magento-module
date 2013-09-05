<?php

class PayIntelligent_Ratepay_Model_Rateconfig extends Mage_Core_Model_Abstract
{
    
    /**
     * Construct
     */
    function _construct()
    {
        parent::_construct();
        $this->_init('ratepay/rateconfig');
    }

    /**
     * This method saves configuration response in the database
     * 
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->setId(0)
            ->setInterestrateMin($config['interestrate-min'])
            ->setInterestrateDefault($config['interestrate-default'])
            ->setInterestrateMax($config['interestrate-max'])
            ->setMonthNumberMin($config['month-number-min'])
            ->setMonthNumberMax($config['month-number-max'])
            ->setMonthLongrun($config['month-longrun'])
            ->setMonthAllowed($config['month-allowed'])
            ->setValidPaymentFirstdays($config['valid-payment-firstdays'])
            ->setPaymentFirstday($config['payment-firstday'])
            ->setPaymentAmount($config['payment-amount'])
            ->setPaymentLastrate($config['payment-lastrate'])
            ->setRateMinNormal($config['rate-min-normal'])
            ->setRateMinLongrun($config['rate-min-longrun'])
            ->setServiceCharge($config['service-charge'])
            ->save();
    }

    public function getConfig()
    {
        return $this->getCollection()->getData();
    }
}