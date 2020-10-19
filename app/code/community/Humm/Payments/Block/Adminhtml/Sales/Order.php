<?php
class Humm_Payments_Block_Adminhtml_Sales_Order extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'humm_payments';
        $this->_controller = 'adminhtml_sales_order';
        $this->_headerText = Mage::helper('humm_payments')->__('Orders - Humm');
        parent::__construct();
        $this->_removeButton('add');
    }
}