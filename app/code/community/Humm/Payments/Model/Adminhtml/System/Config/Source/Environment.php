<?php

/**
 * Class Humm_Payments_Model_Adminhtml_System_Config_Source_Environment
 */

class Humm_Payments_Model_Adminhtml_System_Config_Source_Environment
{
    /**
     * Returns the environment option array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'sandbox',
                'label' => Mage::helper('humm_payments')->__('Sandbox')
            ),
            array(
                'value' => 'production',
                'label' => Mage::helper('humm_payments')->__('Production')
            )
        );
    }

}
