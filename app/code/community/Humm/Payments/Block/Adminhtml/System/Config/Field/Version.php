<?php

/**
 * Class Humm_Payments_Block_Adminhtml_System_Config_Field_Version
 */

class Humm_Payments_Block_Adminhtml_System_Config_Field_Version
extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @var string
     */
    protected $_template = 'humm/payments/system/config/field/version.phtml';

    /**
     * Set template to itself
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate() && $this->_template) {
            $this->setTemplate($this->_template);
        }

        return $this;
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->addData(
            array(
                'version' => Mage::helper('humm_payments')->getCurrentVersion()
            )
        );

        return $this->_toHtml();
    }
}
