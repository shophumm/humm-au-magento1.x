<?php

class Humm_Payments_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()

    {
        $this->_title($this->__('Sales'))->_title($this->__('Orders HummPayments'));
        $this->loadLayout();
        $this->_setActiveMenu('sales/sales');
        $this->_addContent($this->getLayout()->createBlock('humm_payments/adminhtml_sales_order'));
        $this->renderLayout();
    }
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('humm_payments/adminhtml_sales_order_grid')->toHtml()
        );
    }

    public function exportHummCsvAction()
    {
        $fileName = 'orders_Humm.csv';
        $grid = $this->getLayout()->createBlock('humm_payments/adminhtml_sales_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    public function exportHummExcelAction()
    {
        $fileName = 'orders_Humm.xml';
        $grid = $this->getLayout()->createBlock('humm_payments/adminhtml_sales_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}

