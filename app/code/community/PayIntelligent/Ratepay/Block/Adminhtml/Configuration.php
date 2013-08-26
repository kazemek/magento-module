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

class PayIntelligent_Ratepay_Block_Adminhtml_Configuration extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Construct
     */
    public function __construct()
    {
        $this->_blockGroup = 'ratepay';
        $this->_controller = 'adminhtml_configuration';
        $this->_headerText = Mage::helper('ratepay')->__('Pi Ratepay Configuration');
        parent::__construct();
    }

    /**
     * @see Mage_Adminhtml_Block_Widget_Grid_Container::_prepareLayout()
     */
    protected function _prepareLayout()
    {
        $this->_removeButton('add');

        $this->_addButton('save_config', array(
            'label'     => Mage::helper('ratepay')->__('Pi save config'),
            'onclick'   => "setLocation('".$this->getUrl('*/*/configactioncontroller')."')",

        ));

        return parent::_prepareLayout();
    }
}