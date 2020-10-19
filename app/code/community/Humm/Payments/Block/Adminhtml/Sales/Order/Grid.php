<?php

class Humm_Payments_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('humm_order_grid');
        $this->setDefaultSort('increment_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection
            ->join(array('sales' => 'sales/order'), 'main_table.entity_id = sales.entity_id', array('state'=>'state'))
            ->join(array('payment' => 'sales/order_payment'), 'main_table.entity_id = payment.parent_id', array('method'=>'method'))
            ->join(array('payStatus' => 'sales/order_status_history'), 'payment.parent_id = payStatus.parent_id', '*')
            ->addFieldToFilter('comment',array('neq'=>''))
            ->getselect()
            ->where("`payment`.`method` like '%humm%'")->group(array('payStatus.parent_id','payStatus.status','payStatus.comment'));



        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_grid_collection';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', array(
            'header' => Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'increment_id',
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
            'filter_index' => 'main_table.created_at'
        ));
        $this->addColumn('method', array(
            'header' => Mage::helper('sales')->__('method'),
            'index' => 'method',
        ));
        $this->addColumn('state', array(
            'header' => Mage::helper('catalog')->__('State'),
            'index' => 'state',
            'type' => 'text'
        ));
        $this->addColumn('comment', array(
            'header' => Mage::helper('sales')->__('Payment_comment'),
            'index' => 'comment',
            'type' => 'text',
        ));
        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type' => 'currency',
            'currency' => 'order_currency_code',
        ));
        $this->addColumn('main_table.status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));
        return $this;
    }
}
