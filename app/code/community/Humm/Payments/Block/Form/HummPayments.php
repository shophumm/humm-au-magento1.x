<?php

/**
 * Class Humm_Payments_Block_Form_HummPayments
 */
class Humm_Payments_Block_Form_HummPayments extends Mage_Payment_Block_Form {
    const LAUNCH_TIME_URL = "https://humm-variables.s3-ap-southeast-2.amazonaws.com/nz-launch-time.txt";
    const LAUNCH_TIME_DEFAULT = "2020-04-30 14:30:00 UTC";
    const LAUNCH_TIME_CHECK_ENDS = "2020-05-01 14:30:00 UTC";

    protected function _construct() {
        $country = Mage::getStoreConfig( 'payment/humm_payments/country_currency/specific_countries' );
        if ( $country == 'NZ' ) {
            $this->updateLaunchDate();
        }
        $mark = Mage::getConfig()->getBlockClassName( 'core/template' );
        $mark = new $mark;
        $mark->setTemplate('humm/payments/mark.phtml');
        $this->setMethodLabelAfterHtml( $mark->toHtml() );
        parent::_construct();
        $this->setTemplate( 'humm/payments/form.phtml' );
    }

    private function updateLaunchDate() {
        if ( time() - strtotime( self::LAUNCH_TIME_CHECK_ENDS ) > 0 ) {
            if ( ! Mage::getStoreConfig( 'payment/humm_payments/launch_time_string' ) ) {
                Mage::getConfig()->saveConfig( 'payment/humm_payments/launch_time_string', self::LAUNCH_TIME_DEFAULT );
            }
            return;
        }
        $launch_time_string      = Mage::getStoreConfig( 'payment/humm_payments/launch_time_string' );
        $launch_time_update_time = Mage::getStoreConfig( 'payment/humm_payments/launch_time_updated' );
        if ( empty( $launch_time_string ) || ( time() - $launch_time_update_time >= 1440 ) ) {
            $remote_launch_time_string = '';
            try {
                $remote_launch_time_string = file_get_contents( self::LAUNCH_TIME_URL );
            } catch ( Exception $exception ) {
            }
            if ( ! empty( $remote_launch_time_string ) ) {
                $launch_time_string = $remote_launch_time_string;
                Mage::getConfig()->saveConfig( 'payment/humm_payments/launch_time_string', $launch_time_string );
                Mage::getConfig()->saveConfig( 'payment/humm_payments/launch_time_updated', time() );
            } elseif ( empty( $launch_time_string ) || ( empty( $launch_time_update_time ) && $launch_time_string != self::LAUNCH_TIME_DEFAULT ) ) {
                $launch_time_string = self::LAUNCH_TIME_DEFAULT;
                Mage::getConfig()->saveConfig( 'payment/humm_payments/launch_time_string', $launch_time_string );
            }
        }
    }
}