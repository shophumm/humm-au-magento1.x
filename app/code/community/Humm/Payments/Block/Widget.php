<?php

/**
 * Class Humm_Payments_Block_Widget
 */
class Humm_Payments_Block_Widget extends Mage_Core_Block_Template
{

    const CONFIG_WIDGET_PATH_PREFIX = 'payment/humm_payments/widgets/';
    const CONFIG_WIDGETS_ENABLED_PATH = 'payment/humm_payments/widgets/enabled';
    const CONFIG_WIDGETS_DEBUG_PATH = 'payment/humm_payments/widgets/debug';
    const CONFIG_WIDGETS_LIB_SCRIPT_PATH = 'payment/humm_payments/widgets/js_lib';
    const CONFIG_SPECIFIC_COUNTRIES = 'payment/humm_payments/country_currency/specific_countries';

    const CONFIG_PUBLIC_KEY_PATH = 'payment/humm_payments/public_key';
    const CONFIG_ENVIRONMENT_PATH = 'payment/humm_payments/environment';
    const CONFIG_HOME_PAGE_PATH = 'web/default/cms_home_page';

    protected $_supportedWidgetTypes = array('widget', 'banner');

    /**
     * @var null
     */
    protected $_config = null;

    /**
     * get merchant id from public key
     */
    public function getMerchantId()
    {
        return $this->getConfig()->getValue(self::CONFIG_PUBLIC_KEY_PATH);
    }

    /**
     * @return null
     */
    public function getConfig()
    {
        if ($this->_config == null) {
            $this->_config = Mage::helper('humm_payments')->getConfig();
        }

        return $this->_config;
    }

    /**
     * get current environment
     */
    public function getEnvironment()
    {
        return $this->getConfig()->getValue(self::CONFIG_ENVIRONMENT_PATH);
    }

    /**
     * get url of widget js library
     */
    public function getLibScript()
    {
        return $this->getConfig()->getValue(self::CONFIG_WIDGETS_LIB_SCRIPT_PATH);
    }

    /**
     * is debug mode enabled
     */
    protected function isDebugModeEnabled()
    {
        return $this->getConfig()->getFlag(self::CONFIG_WIDGETS_DEBUG_PATH);
    }

    /**
     * check is one widget type is enabled / active
     */
    protected function isActive()
    {
        if (Mage::helper('humm_payments')->isActive() && $this->getConfig()->getFlag(self::CONFIG_WIDGETS_ENABLED_PATH)) {
            $pageType = $this->getWidgetPageType();

            if ($pageType === null) {
                return false;
            }

            if ($pageType == 'checkout' || $pageType == 'landing') {
                return true;
            }

            foreach ($this->_supportedWidgetTypes as $widgetType) {
                $enabled = $this->getConfig()
                    ->getValue(self::CONFIG_WIDGET_PATH_PREFIX . $pageType . '_page/' . $widgetType . '/enabled');
                /**
                 * Make sure there one widget type is enable for current page type
                 */
                if ($enabled !== null && $enabled) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns the current page type.
     *
     * @return string
     */
    protected function getWidgetPageType()
    {
        $helper = Mage::helper('humm_payments');
        $pageIdentifier = $helper->getPageIdentifier();

        if ($pageIdentifier == 'cms_index_index') {
            return 'home';
        }
        if ($pageIdentifier == 'catalog_product_view') {
            return 'product';
        }
        if ($pageIdentifier == 'catalog_category_view') {
            return 'category';
        }
        if ($pageIdentifier == 'checkout_cart_index') {
            return 'cart';
        }
        if ($pageIdentifier == 'cms_page_view') {
            return 'landing';
        }

        return null;
    }

    /**
     * @return null
     */

    protected function getProduct()
    {
        $productPrice = Mage::registry('current_product')->getPrice();
        return isset($productPrice) ? $productPrice : null;
    }

    /**
     * @return null
     */

    protected function getCart()
    {
        $cartAmount = Mage::getSingleton('checkout/session')->getQuote()->getGrandTotal();
        return isset($cartAmount) ? $cartAmount : null;
    }

    /**
     * get element selectors for current widgets
     */
    protected function getElementSelectors()
    {
        $selectors = array();
        $helper = Mage::helper('humm_payments');

        foreach ($this->_supportedWidgetTypes as $widgetType) {
            $pageType = $this->getWidgetPageType();
            $path = self::CONFIG_WIDGET_PATH_PREFIX . $pageType . '_page/' . $widgetType;
            $enabled = $helper->getConfig()->getValue($path . '/enabled');

            if ($enabled !== null && $enabled) {
                $widgetType = $widgetType == 'widget' ? $pageType . '_' . $widgetType : $widgetType;
                $selectors[$widgetType] = $helper->getConfig()->getValue($path . '/selector');
            }
        }
        return $selectors;
    }

    /**
     * @return mixed
     */
    protected function getCountry()
    {

        return $this->getConfig()->getValue(self::CONFIG_SPECIFIC_COUNTRIES);
    }


}
