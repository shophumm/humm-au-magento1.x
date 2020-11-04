<?php

/**
 * Class Humm_Payments_Model_Paymentmethod
 */
class Humm_Payments_Model_Paymentmethod extends Mage_Payment_Model_Method_Abstract
{

    /**
     * @var null
     */
    protected $_config = null;
    protected $_code = 'humm_payments';
    protected $_formBlockType = 'humm_payments/form_HummPayments';
    protected $_infoBlockType = 'humm_payments/info_HummPayments';
    protected $_isInitializeNeeded = true;
    protected $_canUseInternal = true;
    protected $_canUseForMultishipping = false;
    protected $_canUseCheckout = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canCapturePartial = true;

    /**
     * Override redirect location of magento's payment method subsystem
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('humm_payments/Payment/start', array('_secure' => false));
    }

    /**
     * @param Varien_Object $payment
     * @param float $amount
     * @return Mage_Payment_Model_Abstract|void
     */
    public function refund(Varien_Object $payment, $amount)
    {
        $url = Humm_Payments_Helper_DataHumm::getRefundUrl();

        $merchant_number = Mage::getStoreConfig('payment/humm_payments/public_key');
        $apiKey = Mage::helper('core')->decrypt(Mage::getStoreConfig('payment/humm_payments/private_key'));

        if (!$payment->getData('creditmemo')) {
            return;
        }
        $refund_amount = $amount;
        $transaction_id = $payment->getData()['creditmemo']->getData('invoice')->getData('transaction_id');
        $refund_details = array(
            "x_merchant_number" => $merchant_number,
            "x_purchase_number" => $transaction_id,
            "x_amount" => $refund_amount,
            "x_reason" => "Refund"
        );

        $refund_signature = Humm_Payments_Helper_Crypto::generateSignature($refund_details, $apiKey);
        $refund_details['signature'] = $refund_signature;

        $json = json_encode($refund_details);

        // Do refunding POST request using curl
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        $response = curl_exec($curl);

        // split and parse header and body
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header_string = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        $header_rows = explode(PHP_EOL, $header_string);
        $header_rows = array_map('trim', $header_rows);
        $parsed_header = ($this->parseHeaders($header_rows));

        curl_close($curl);

        if ($parsed_header['response_code'] == '204') {
            return $this;
        } elseif ($parsed_header['response_code'] == '401') {
            $error_message = 'Humm refunding error: Failed Signature Check when communicating with the Humm gateway.';
            Mage::logException(new Exception($error_message));
            Mage::throwException($error_message);
        } elseif ($parsed_header['response_code'] == '400') {
            $return_message = json_decode($body, true)['Message'];
            $return_message_explain = '';
            if ($return_message == "MERR0001") {
                $return_message_explain = ' (API Key Not found)';
            } elseif ($return_message == "MERR0003") {
                $return_message_explain = ' (Refund Failed)';
            } elseif ($return_message == "MERR0004") {
                $return_message_explain = ' (Invalid Request)';
            }
            $error_message = 'Humm refunding error with returned message from gateway: ' . $return_message . $return_message_explain;
            Mage::logException(new Exception($error_message));
            Mage::throwException($error_message);
        } else {
            $error_message = "Humm refunding failed with unknown error.";
            Mage::logException(new Exception($error_message));
            Mage::throwException($error_message);
        }
    }

    function parseHeaders($headers)
    {
        $head = array();
        foreach ($headers as $k => $v) {
            $t = explode(':', $v, 2);
            if (isset($t[1])) {
                $head[trim($t[0])] = trim($t[1]);
            } else {
                $head[] = $v;
                if (preg_match("#HTTP/[0-9\.]+\s+([0-9]+)#", $v, $out)) {
                    $head['response_code'] = intval($out[1]);
                }
            }
        }
        return $head;
    }


    /**
     * @return Mage_Core_Helper_Abstract|Mage_Payment_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('humm_payments');
    }


    /**
     * @return mixed
     */
    public function getConfig()
    {
        if ($this->_config == null) {
            $this->_config = $this->_getHelper()->getConfig();
        }
        return $this->_config;
    }

    /**
     * @param null $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
           if ($this->_getHelper()->getCheckoutValid())
               return parent::isAvailable($quote) && $this->getConfig()->isMethodAvailable();
           else
               return false;
    }
}