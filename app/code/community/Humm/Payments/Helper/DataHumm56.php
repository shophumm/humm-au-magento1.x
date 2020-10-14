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
    const URLS = [
        'AU' => [
            'sandboxURL' => 'https://integration-cart.shophumm.com.au/Checkout?platform=Default',
            'liveURL' => 'https://cart.shophumm.com.au/Checkout?platform=Default',
            'sandbox_refund_address' => 'https://integration-buyerapi.shophumm.com.au/api/ExternalRefund/v1/processrefund',
            'live_refund_address' => 'https://buyerapi.shophumm.com.au/api/ExternalRefund/v1/processrefund',
        ],
        'NZ_Oxipay' => [
            'sandboxURL' => 'https://securesandbox.oxipay.co.nz/Checkout?platform=Default',
            'liveURL' => 'https://secure.oxipay.co.nz/Checkout?platform=Default',
            'sandbox_refund_address' => 'https://portalssandbox.oxipay.co.nz/api/ExternalRefund/processrefund',
            'live_refund_address' => 'https://portals.oxipay.co.nz/api/ExternalRefund/processrefund',
        ],
        'NZ_Humm' => [
            'sandboxURL' => 'https://integration-cart.shophumm.co.nz/Checkout?platform=Default',
            'liveURL' => 'https://cart.shophumm.co.nz/Checkout?platform=Default',
            'sandbox_refund_address' => 'https://integration-buyerapi.shophumm.co.nz/api/ExternalRefund/v1/processrefund',
            'live_refund_address' => 'https://buyerapi.shophumm.co.nz/api/ExternalRefund/v1/processrefund',
        ]
    ];

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
//        $launch_time_string = self::getLaunchDate();
//        if ($launch_time_string) {
//            $is_after = (time() - strtotime($launch_time_string) >= 0) || Mage::getStoreConfig('payment/humm_payments/force_humm');
//        }
//        else {
//            $is_after = true;
//        }

        $checkCountry = Mage::getStoreConfig('payment/humm_payments/country_currency/specific_countries');

        if ($checkCountry == 'NZ') {
//            if ($is_after){
//                return 'NZ_Humm';
//            }
//            else {
//                return 'NZ_Oxipay';
//            }
            return 'NZ_Humm';
        }
        else {
            return 'AU';
        }
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

            $isSandbox = Mage::getStoreConfig('payment/humm_payments/environment') == 'sandbox' ? 'sandboxURL' : 'liveURL';
            $checkoutUrl = 'https://' . self::URLS[$title][$isSandbox] . $country_domain . '/Checkout?platform=Default';
            Mage::log(self::URLS[$title][$isSandbox],7,self::Log_file);
            return self::URLS[$title][$isSandbox];
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

        $refundUrl = self::URLS[$title][$isSandbox];

        Mage::log(sprintf("%s refundURL",$refundUrl),7,self::Log_file);
        return $refundUrl;
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
        return strtotime($remote_launch_time_string)>= strtotime(self::LAUNCH_TIME_DEFAULT) ? strtotime($remote_launch_time_string):strtotime(self::LAUNCH_TIME_DEFAULT);
    }
}

Humm_Payments_Helper_DataHumm::init();