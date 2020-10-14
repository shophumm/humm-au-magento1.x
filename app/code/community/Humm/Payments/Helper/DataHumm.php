<?php

/**
 * Class Humm_Payments_Helper_DataHumm
 *
 * Provides helper methods for retrieving data for the humm plugin
 */
class Humm_Payments_Helper_DataHumm extends Mage_Core_Helper_Abstract
{
    const LAUNCH_TIME_URL = 'https://humm-variables.s3-ap-southeast-2.amazonaws.com/nz-launch-time.txt';
    const LAUNCH_TIME_DEFAULT = "2019-05-11 00:00:00 UTC";
    const LAUNCH_TIME_CHECK_ENDS = "2019-11-18 00:00:00 UTC";
    const Log_file = 'humm.log';
    /**
     *
     */
    public static function init()
    {

    }

    /**
     * @return string
     */

    public static function getTitle()
    {
        $is_after = Mage::getStoreConfig('payment/HummPayments/force_humm');
        $title = ($is_after || Mage::getStoreConfig('payment/humm_payments/country_currency/specific_countries') == 'AU') ? 'Humm' : 'Oxipay';

        return $title;
    }

    /**
     * get the URL of the configured humm gateway checkout
     * @return string
     */
    public static function getCheckoutURL()
    {
        $checkoutUrl = Mage::getStoreConfig('payment/humm_payments/path');
        if (isset($checkoutUrl) && strtolower(substr($checkoutUrl, 0, 4)) == 'https') {
            return $checkoutUrl;
        } else {
            $title = self::getTitle();
            $country = Mage::getStoreConfig('payment/humm_payments/country_currency/specific_countries');
            $country_domain = $country == 'NZ' ? '.co.nz' : '.com.au';
            $domainsTest = array(
                'Humm' => 'integration-cart.shophumm',
                'Oxipay' => 'securesandbox.oxipay'
            );
            $domains = array(
                'Humm' => 'cart.shophumm',
                'Oxipay' => 'secure.oxipay'
            );

            return 'https://' . (Mage::getStoreConfig('payment/humm_payments/environment') == 'sandbox' ? $domainsTest[$title] : $domains[$title]) . $country_domain . '/Checkout?platform=Default';
        }
    }

    /**
     * get the URL of the configured humm gateway refund
     * @return string
     */
    public static function getRefundURL()
    {

        $title = self::getTitle();

        $isSandbox = Mage::getStoreConfig('payment/humm_payments/environment') == 'sandbox' ? 'sandbox_refund_address' : 'live_refund_address';
        $country = Mage::getStoreConfig('payment/humm_payments/country_currency/specific_countries');
        $country_domain = $country == 'NZ' ? '.co.nz' : '.com.au';
        $domainsTest = array(
            'Humm' => 'integration-buyerapi.shophumm',
            'Oxipay' => 'portalssandbox.oxipay'
        );
        $domains = array(
            'Humm' => 'buyerapi.shophumm',
            'Oxipay' => 'portals.oxipay'
        );

        return 'https://' . (Mage::getStoreConfig('payment/humm_payments/environment') == 'sandbox' ? $domainsTest[$title] : $domains[$title]) . $country_domain . '/api/ExternalRefund/v1/processrefund';
    }

    /**
     * @return string
     */
    public static function getCompleteUrl()
    {
        return Mage::getBaseUrl() . 'humm_payments/payment/complete';
    }

    /**
     * @return string
     */
    public static function getCancelledUrl($orderId)
    {
        return Mage::getBaseUrl() . "humm_payments/payment/cancel?orderId=$orderId";
    }


    /**
     *
     */
    static function getLaunchDate()
    {
        Mage::log(self::LAUNCH_TIME_CHECK_ENDS, 7, self::Log_file);
        if (time() - strtotime(self::LAUNCH_TIME_CHECK_ENDS) > 0) {
            return false;
        }
        try {
            $remote_launch_time_string = file_get_contents(self::LAUNCH_TIME_URL);
        } catch (\Exception $exception) {
            Mage::log(sprintf("Get ForceHumm %s Url Wrong %s", self::LAUNCH_TIME_URL, $exception->getMessage()), 4, self::Log_file);
            return false;
        }
        return strtotime($remote_launch_time_string) >= strtotime(self::LAUNCH_TIME_DEFAULT) ? strtotime($remote_launch_time_string) : strtotime(self::LAUNCH_TIME_DEFAULT);
    }
}

Humm_Payments_Helper_DataHumm::init();