<?php

/**
 * Class Humm_Payments_Model_Adminhtml_System_Config_Source_DisplayMode
 */


class Humm_Payments_Model_Adminhtml_System_Config_Source_DisplayMode
{

    const DISPLAY_MODE_REDIRECT = 'redirect';
    const DISPLAY_MODE_LIGHTBOX = 'lightbox';

    /**
     * Returns the display mode option array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::DISPLAY_MODE_REDIRECT,
                'label' => Mage::helper('humm_payments')->__('Redirect')
            ),
            array(
                'value' => self::DISPLAY_MODE_LIGHTBOX,
                'label' => Mage::helper('humm_payments')->__('Lightbox')
            )
        );
    }

}
