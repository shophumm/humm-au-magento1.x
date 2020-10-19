<?php

/**
 * Class Humm_HummPayments_Info_Form_HummPayments
 * @Description Code behind for the custom Humm payment info block.
 * @Remarks Invokes the view template in a distant folder structure: ~/app/design/frontend/base/default/template/HummPayments/info.phtml
 *
 */
class Humm_Payments_Block_Info_HummPayments extends Mage_Payment_Block_Info
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('humm/payments/info.phtml');
    }
}