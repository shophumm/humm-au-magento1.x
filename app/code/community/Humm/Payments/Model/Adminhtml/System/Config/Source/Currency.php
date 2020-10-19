<?php

/**
 * Class Humm_Payments_Model_Adminhtml_System_Config_Source_Currency
 */

class Humm_Payments_Model_Adminhtml_System_Config_Source_Currency extends
Mage_Adminhtml_Model_System_Config_Source_Currency
{

    public function toOptionArray($isMultiselect)
    {
        $options = array();
        $supportedCurrencies = Mage::getSingleton('humm_payments/config')
        ->getValue(Humm_Payments_Model_Config::CONFIG_SUPPORTED_CURRENCIES_PATH);

        if (!empty($supportedCurrencies)) {
            $supportedCurrencies = explode(',', (string) $supportedCurrencies);
            $options = parent::toOptionArray($isMultiselect);

            $options = array_filter(
                $options, function ($option) use ($supportedCurrencies) {
                    return in_array($option['value'], $supportedCurrencies);
                }
            );
        }

        return $options;

    }

}
