<?php

/**
 * Class Humm_Payments_Helper_Data
 */
class Humm_Payments_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * get config model
     */
    public function getConfig()
    {
        return Mage::getSingleton('humm_payments/config');
    }

    /**
     * is Humm Payment active
     */
    public function isActive()
    {
        return $this->getConfig()->getFlag(Humm_Payments_Model_Config::CONFIG_ACTIVE_PATH);
    }

    /**
     * Retrieves the extension version.
     *
     * @return string
     */
    public function getCurrentVersion()
    {
        return trim((string) Mage::getConfig()->getNode()->modules->Humm_Payments->version);
    }

    /**
     * Get current store url
     *
     * @param  $route
     * @param  $param
     * @return string
     */
    public function getUrl($route, $param = array())
    {
        return Mage::getUrl($route, $param);
    }


    /**
     * Return checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Return customer session object
     *
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }


    /**
     * get payment method currently been used
     */
    public function getCurrentPaymentMethod()
    {
        return $this->getOnepage()->getQuote()->getPayment()->getMethodInstance();
    }


    /**
     * Empty customer's shopping cart
     */
    public function emptyShoppingCart()
    {
        try {
            $this->getCheckoutSession()->getQuote()->setIsActive(0)->save();
            $this->getCheckoutSession()->setQuoteId(null);
        } catch (Mage_Core_Exception $exception) {
            $this->getCheckoutSession()->addError($exception->getMessage());
        } catch (Exception $exception) {
            $this->getCheckoutSession()->addException($exception, $this->__('Could not empty shopping cart'));
        }
    }

    /**
     * Prepare JSON formatted data for response to client
     *
     * @param  $response
     * @return Zend_Controller_Response_Abstract
     */
    public function returnJsonResponse($response)
    {
        Mage::app()->getFrontController()->getResponse()->setHeader('Content-type', 'application/json', true);
        Mage::app()->getFrontController()->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    /**
     * check whether checkout display model is redirect
     */
    public function isRedirectCheckoutDisplayModel()
    {
        return $this->getConfig()->getValue(Humm_Payments_Model_Config::CONFIG_CHECKOUT_DISPLAY_MODE_PATH) ==
            Humm_Payments_Model_Adminhtml_System_Config_Source_DisplayMode::DISPLAY_MODE_REDIRECT;
    }


    /*******************************************
     * PAGE DETECTION
     *******************************************/

    /**
     * get full action name for current page
     */
    public function getPageIdentifier()
    {
        return Mage::app()->getFrontController()->getAction()->getFullActionName();
    }


    /**
     * get path for current page
     */
    public function getPagePath()
    {
        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        $url = Mage::getSingleton('core/url')->parseUrl($currentUrl);
        return rtrim($url->getPath(), '/');
    }


    /**
     * delete log file
     */
    public function removeLogFile($filename)
    {
        $path = Mage::getBaseDir('var') . DS . 'log' . DS . $filename;
        $io = new Varien_Io_File();
        $io->rm($path);
    }
}
