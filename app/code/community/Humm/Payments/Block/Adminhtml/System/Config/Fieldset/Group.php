<?php

/**
 * Class Humm_Payments_Block_Adminhtml_System_Config_Fieldset_Group
 */
class Humm_Payments_Block_Adminhtml_System_Config_Fieldset_Group
extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    protected $_noticeTemplate = 'humm/payments/system/config/fieldset/group/notice.phtml';

    protected $_notificationFeedModel = null;
    protected $_currentVersion = '';
    protected $_notificationData = array();

    protected function _construct()
    {
        $this->_currentVersion = Mage::helper('humm_payments')->getCurrentVersion();
        parent::_construct();
    }

    protected function _getHeaderCommentHtml($element)
    {
        $block = Mage::app()->getLayout()->createBlock('core/template');
        $block->setTemplate($this->_noticeTemplate);
        $block->setData(
            array(
                'version_notification' => 'good condition',
                'latest_news' => 'good performance'
            )
        );

        return $block->toHtml();
    }

    /**
     * get latest news from news feed
     */
    protected function getLatestNews()
    {
        $notificationField = Humm_Payments_Model_Adminhtml_Notification_Feed::NOTIFICATION_FIELD;

        if (isset($this->_notificationData[$notificationField])) {
            $feedData = array_reverse($this->_notificationData[$notificationField]);

            if (!empty($feedData)) {
                return $feedData[0];
            }
        }

        return null;
    }

    /**
     * Return collapse state
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return bool
     */
    protected function _getCollapseState($element)
    {
        $extra = Mage::getSingleton('admin/session')->getUser()->getExtra();
        if (isset($extra['configState'][$element->getId()])) {
            return $extra['configState'][$element->getId()];
        }

        if ($element->getExpanded() !== null) {
            return 1;
        }

        return false;
    }

}
