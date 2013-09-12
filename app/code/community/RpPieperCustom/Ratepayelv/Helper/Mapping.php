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

class PRpPieperCustom_Ratepayelv_Helper_Mapping extends Mage_Core_Helper_Abstract
{
    /**
     * Article preparations for PAYMENT_REQUEST, PAYMENT_CHANGE, CONFIRMATION_DELIVER
     *
     * @param Mage_Sales_Model_Quote|Mage_Sales_Model_Order|Mage_Sales_Model_Order_Invoice|Mage_Sales_Model_Order_Creditmemo $object
     * @return array
     */
    public function getArticles($object)
    {
        $articles = array();
        $articleDiscountAmount = 0;
        if ($object instanceof Mage_Sales_Model_Order_Invoice || $object instanceof Mage_Sales_Model_Order_Creditmemo) {
            $objectItems = $object->getAllItems();
        } else {
            $objectItems = $object->getAllItems();
        }

        foreach ($objectItems as $item) {
            $rowTotal = $item->getRowTotal();
            //if ($rowTotal > 0 || $rowTotal < 0) {
                $article = array();
                $article['articleNumber'] = $item->getSku();
                $article['articleName']   = $item->getName();
                $article['totalPrice']    = $rowTotal; // gesamt netto
                $article['tax']           = $item->getTaxAmount(); // gesamt steuer
                $article['taxPercent']    = $item->getTaxPercent(); // gesamt steuer

                if ($object instanceof Mage_Sales_Model_Order) {
                    $article['quantity']  = $item->getQtyOrdered();
                } else {
                    $article['quantity']  = $item->getQty();
                }

                if ($object instanceof Mage_Sales_Model_Quote) {
                    $article['unitPrice'] = $item->getCalculationPrice();
                } else {
                    $article['unitPrice'] = $item->getPrice(); // netto
                }
                $article['discountId'] = '';

                if ($item->getDiscountAmount() > 0) {
                    $discount = array();
                    $discount['articleNumber'] = 'DISCOUNT';
                    $discount['articleName'] = 'DISCOUNT - ' . $item->getName();
                    $discount['quantity'] = $article['quantity'];
                    $article['tax'] = $item->getRowTotalInclTax() - $item->getRowTotal();
                    $discount['tax'] = -1 * ($article['tax'] - $item->getTaxAmount());
                    $tax = 0;
                    $taxConfig = Mage::getSingleton('tax/config');
                    if($taxConfig->priceIncludesTax($object->getStoreId())) {
                            $tax = $discount['tax'];
                    }
                    $discount['unitPrice'] = ((-1 * $item->getDiscountAmount()) - $tax) / $article['quantity'];
                    $discount['totalPrice'] = (-1 * $item->getDiscountAmount()) - $tax;

                    if(round($discount['tax'],2) < 0) {
                        $discount['taxPercent']    = $article['taxPercent'];
                    }
                    $discount['discountId']    = $item->getSku();

                    $articleDiscountAmount = $articleDiscountAmount + $item->getDiscountAmount();
                }

                $articles[] = $article;
                if ($item->getDiscountAmount() > 0) { // only for sort reason
                    $articles[] = $discount;
                }
            //}
        }

        if ($object instanceof Mage_Sales_Model_Order || $object instanceof Mage_Sales_Model_Order_Invoice  || $object instanceof Mage_Sales_Model_Order_Creditmemo) {
            $shippingObject = $object;
            if ($object instanceof Mage_Sales_Model_Order_Creditmemo) {
                $articles = $this->addAdjustments($object, $articles);
            }
        } else {
            $shippingObject = $object->getShippingAddress();
        }

        if ($shippingObject->getShippingAmount() > 0) {

            if ($object instanceof Mage_Sales_Model_Order_Invoice || $object instanceof Mage_Sales_Model_Order_Creditmemo) {
                $shippingDiscountAmount = $shippingObject->getDiscountAmount() - $articleDiscountAmount;
                $shippingDescription = $object->getOrder()->getShippingDescription();
            } else {
                $shippingDiscountAmount = $shippingObject->getShippingDiscountAmount();
                $shippingDescription = $shippingObject->getShippingDescription();
            }

            $article = array();
            $article['articleNumber'] = 'SHIPPING';
            $article['articleName'] = $shippingDescription;
            $article['quantity'] = '1';
            $article['unitPrice'] = $shippingObject->getShippingAmount();
            $article['totalPrice'] = $shippingObject->getShippingAmount();
            $article['tax'] = $shippingObject->getShippingTaxAmount();
            $shippingTaxPercent = 0;
            if(($shippingObject->getShippingInclTax() - $shippingObject->getShippingAmount()) > 0){
                $shippingTaxPercent = (($shippingObject->getShippingInclTax() - $shippingObject->getShippingAmount()) * 100) /  $shippingObject->getShippingAmount();
            }
            $article['taxPercent'] = $shippingTaxPercent;
            $article['discountId'] = '';

            if ($shippingDiscountAmount > 0) {
                $discount = array();
                $discount['articleNumber'] = 'SHIPPINGDISCOUNT';
                $discount['articleName'] = 'Shipping - Discount';
                $discount['quantity'] = 1;
                $discount['unitPrice'] = -1 * $order->getShippingDiscountAmount();
                $discount['totalPrice'] = -1 * $order->getShippingDiscountAmount();
                $article['tax'] = $order->getShippingInclTax() - $order->getShippingAmount();
                $discount['tax'] = -1 * ($article['tax'] - $order->getShippingTaxAmount());
                $tax = 0;
                $taxConfig = Mage::getSingleton('tax/config');
                if($taxConfig->shippingPriceIncludesTax($object->getStoreId())) {
                        $tax = $discount['tax'];
                }
                $discount['unitPrice'] = (-1 * $order->getShippingDiscountAmount())- $tax;
                $discount['totalPrice'] = (-1 * $order->getShippingDiscountAmount()) - $tax;
                $discount['taxPercent'] = 0;
                if(round($discount['tax'],2) < 0) {
                    $discount['taxPercent']    = $article['taxPercent'];
                }
                $discount['discountId'] = 'SHIPPING';
            }

            $articles[] = $article;
            if ($shippingDiscountAmount > 0) { // only for sort reason
                $articles[] = $discount;
            }
        }
        return $articles;
    }
    
    /**
     * Add adjustment items to the article list
     * 
     * @param Mage_Sales_Model_Creditmemo $creditmemo
     * @param array
     */
    private function addAdjustments($creditmemo, $articles)
    {
        if ($creditmemo->getAdjustmentPositive() != 0) {
            array_push($articles, $this->addAdjustment($articles, (float) $creditmemo->getAdjustmentPositive() * -1, 'Adjustment Positive', 'adj-pos'));  
        }
        if ($creditmemo->getAdjustmentNegative() != 0) {
            array_push($articles, $this->addAdjustment($articles, (float) $creditmemo->getAdjustmentNegative(), 'Adjustment Negative', 'adj-neg'));
        }
        
        return $articles;
    }
    
    /**
     * Add merchant credit to artcile list
     * 
     * @param array $articles
     * @param float $amount
     * @return array
     */
    public function addAdjustment(array $articles, $amount, $name, $articleNumber)
    {
        $tempVoucherItem = array();
        $tempVoucherItem['articleName'] = $name;
        $tempVoucherItem['articleNumber'] = $articleNumber;
        $tempVoucherItem['quantity'] = 1;
        $tempVoucherItem['unitPrice'] = $amount;
        $tempVoucherItem['totalPrice'] = $amount;
        $tempVoucherItem['tax'] = 0;
        $tempVoucherItem['discountId'] = 0;
        
        return $tempVoucherItem;
    }

    /**
     * Gets all needed Informations for Logging
     * 
     * @param Mage_Sales_Model_Quote|Mage_Sales_Model_Order $quoteOrOrder
     * @param string $methodCode
     * @return array
     */
    public function getLoggingInfo($quoteOrOrder, $methodCode = '')
    {
        $loggingInfo = array();
        if($methodCode == '') {
            $methodCode = $quoteOrOrder->getPayment()->getMethod();
        }
        $loggingInfo['logging'] = Mage::getStoreConfig('paymentelv/' . $methodCode . '/logging', $quoteOrOrder->getStoreId());
        $loggingInfo['sandbox'] = Mage::getStoreConfig('paymentelv/' . $methodCode . '/sandbox', $quoteOrOrder->getStoreId());
        if($quoteOrOrder instanceof Mage_Sales_Model_Order) {
            $loggingInfo['orderId'] = $quoteOrOrder->getRealOrderId();
        } else {
            $loggingInfo['orderId'] = $quoteOrOrder->getReservedOrderId();
        }
        $loggingInfo['transactionId'] = $quoteOrOrder->getPayment()->getAdditionalInformation('transactionId');
        switch($methodCode) {
            case 'ratepay_rechnung':
                $loggingInfo['paymentMethod'] = 'INVOICE';
                break;
            case 'ratepay_rate':
                $loggingInfo['paymentMethod'] = 'INSTALLMENT';
                break;
            case 'ratepay_directdebit':
                $loggingInfo['paymentMethod'] = 'ELV';
                break;
        }
        $loggingInfo['firstName'] = $quoteOrOrder->getBillingAddress()->getFirstname();
        $loggingInfo['lastName'] = $quoteOrOrder->getBillingAddress()->getLastname();
        return $loggingInfo;
    }

    /**
     * Gets all needed Informations for the head Block of Requests for RatePAY
     *
     * @param Mage_Sales_Model_Quote|Mage_Sales_Model_Order $quoteOrOrder
     * @param string $subtype
     * @param string $methodCode
     * @return array
     */
    public function getRequestHead($quoteOrOrder,$subtype = '',$methodCode = '')
    {
        $head = array();
        if($methodCode == '') {
            $head['profileId'] = Mage::getStoreConfig('paymentelv/' . $quoteOrOrder->getPayment()->getMethod() . '/profile_id', $quoteOrOrder->getStoreId());
            $head['securityCode'] = Mage::getStoreConfig('paymentelv/' . $quoteOrOrder->getPayment()->getMethod() . '/security_code', $quoteOrOrder->getStoreId());
            $head['transactionId'] = $quoteOrOrder->getPayment()->getAdditionalInformation('transactionId');
            $head['transactionShortId'] = $quoteOrOrder->getPayment()->getAdditionalInformation('transactionShortId');
        } else {
            $head['profileId'] = Mage::getStoreConfig('paymentelv/' . $methodCode . '/profile_id', $quoteOrOrder->getStoreId());
            $head['securityCode'] = Mage::getStoreConfig('paymentelv/' . $methodCode . '/security_code', $quoteOrOrder->getStoreId());
            $head['transactionId'] = '';
            $head['transactionShortId'] = '';
        }
        if($quoteOrOrder instanceof Mage_Sales_Model_Order) {
            $head['orderId'] = $quoteOrOrder->getRealOrderId();
        } else {
            $head['orderId'] = $quoteOrOrder->getReservedOrderId();
        }
        $head['subtype'] = $subtype;
        return $head;
    }

    /**
     * Gets all needed Informations for customer Block of the Request
     *
     * @param Mage_Sales_Model_Quote|Mage_Sales_Model_Order $quoteOrOrder
     * @return array
     */
    public function getRequestCustomer($quoteOrOrder)
    {
        $customer = array();
        $contacts = array();
        $billing = array();
        $shipping = array();

        $dob = new Zend_Date($quoteOrOrder->getCustomerDob());//, Zend_Date::ISO_8601);
        $customer['dob'] = $dob->toString("yyyy-MM-dd");
        $customer['gender'] = Mage::helper("ratepay")->getGenderCode($quoteOrOrder);
        $customer['firstName'] = $quoteOrOrder->getBillingAddress()->getFirstname();
        $customer['lastName'] = $quoteOrOrder->getBillingAddress()->getLastname();
        $customer['ip'] = Mage::helper('core/http')->getRemoteAddr(false);
        $customer['nationality'] = $quoteOrOrder->getBillingAddress()->getCountryId();
        $customer['company'] = $quoteOrOrder->getBillingAddress()->getCompany();
        $customer['vatId'] = $quoteOrOrder->getCustomerTaxvat();

        $contacts['email'] = $quoteOrOrder->getCustomerEmail();
        $contacts['phone'] = $quoteOrOrder->getBillingAddress()->getTelephone();

        if ($quoteOrOrder->getBillingAddress()->getFax() != '') {
            $contacts['fax'] = $quoteOrOrder->getBillingAddress()->getFax();
        }

        $billing['street'] = $quoteOrOrder->getBillingAddress()->getStreetFull();
        $billing['zipCode'] = $quoteOrOrder->getBillingAddress()->getPostcode();
        $billing['city'] = $quoteOrOrder->getBillingAddress()->getCity();
        $billing['countryId'] = $quoteOrOrder->getBillingAddress()->getCountryId();

        $shipping['street'] = $quoteOrOrder->getShippingAddress()->getStreetFull();
        $shipping['zipCode'] = $quoteOrOrder->getShippingAddress()->getPostcode();
        $shipping['city'] = $quoteOrOrder->getShippingAddress()->getCity();
        $shipping['countryId'] = $quoteOrOrder->getShippingAddress()->getCountryId();

        $customer['contacts'] = $contacts;
        $customer['billing'] = $billing;
        $customer['shipping'] = $shipping;
        return $customer;
    }

    /**
     * Gets all needed Informations for shopping-basket Block of the Request
     *
     * @param Mage_Sales_Model_Quote|Mage_Sales_Model_Order|Mage_Sales_Model_Order_Invoice|Mage_Sales_Model_Order_Creditmemo $object
     * @param float $amount
     * @param array $articleList
     * @return array
     */
    public function getRequestBasket($object, $amount = '', $articleList = '')
    {
        $basket = array();

        if(is_numeric($amount)) {
          $basket['amount'] = $amount;
        } else {
          $basket['amount'] = $object->getGrandTotal();
        }

        if($object instanceof Mage_Sales_Model_Order) {
            $basket['currency'] = $object->getOrderCurrencyCode();
        } else if($object instanceof Mage_Sales_Model_Quote){
            $basket['currency'] = $object->getQuoteCurrencyCode();
        } else {
            $basket['currency'] = $object->getOrder()->getOrderCurrencyCode();
        }

        if($articleList != '') {
          $basket['items'] = $articleList;
        } else {
          $basket['items'] = $this->getArticles($object);
        }

        return $basket;
    }

    /**
     * Gets all needed Informations for payment Block of the Request
     *
     * @param Mage_Sales_Model_Quote|Mage_Sales_Model_Order $object
     * @param float amount
     * @param string $request
     * @return array
     */
    public function getRequestPayment($object, $amount = '', $request = '')
    {
        $payment = array();
        $payment['method'] = 'ELV';

        if($object instanceof Mage_Sales_Model_Order) {
            $payment['currency'] = $object->getOrderCurrencyCode();
        } else {
            $payment['currency'] = $object->getQuoteCurrencyCode();
        }
        if(is_numeric($amount)) {
           $payment['amount'] = $amount;
        } else {
           $payment['amount'] = $object->getGrandTotal();
        }

        return $payment;
    }
    
    /**
     * Is due dynamic
     *
     * @return boolean
     */
    public function isDynamicDue()
    {
        return false;
    }

}