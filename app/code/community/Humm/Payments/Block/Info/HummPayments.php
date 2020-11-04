<?php

/**
 * Class Humm_HummPayments_Info_Form_HummPayments
 * @Description Code behind for the custom Humm payment info block.
 *
 */
class Humm_Payments_Block_Info_HummPayments extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('humm/payments/method/info/default.phtml');
    }

}