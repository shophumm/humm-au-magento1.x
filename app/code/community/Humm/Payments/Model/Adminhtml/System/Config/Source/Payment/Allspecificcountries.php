<?php

/**
 * Class Humm_Payments_Model_Adminhtml_System_Config_Source_Payment_Allspecificcountries
 */

class Humm_Payments_Model_Adminhtml_System_Config_Source_Payment_Allspecificcountries
extends Mage_Adminhtml_Model_System_Config_Source_Payment_Allspecificcountries
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 1,
                'label' => Mage::helper('adminhtml')->__('Specific Countries')
            )
        );
    }
}
