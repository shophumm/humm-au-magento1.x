<?php
/**
 * Class Humm_Payments_Model_Config
 */
class Humm_Payments_Model_HummCron
{
    const Log_file = 'humm.log';
    const paymentMethod = 'humm';

    public function execute()
    {
        $yesNo = intval(Mage::getStoreConfig('payment/humm_payments/pend_order'));

        if (!intval($yesNo)) {
             Mage::log("Clean Pend Order in Crontab Disable", 7, self::Log_file);
             return $this;
        }
        $daysSkip = intval(Mage::getStoreConfig('payment/humm_payments/pend_days'));
        $time = Mage::getModel('core/date')->timestamp(time());
        $dateNow = (new \DateTime())->setTimestamp($time);
        $to = $dateNow->format('Y-m-d H:i:s');
        $from = $dateNow->sub(new \DateInterval('P' . $daysSkip . 'D'))->format('Y-m-d H:i:s');
        Mage::log(sprintf("Start Crontab..time from%s to now%s enabled  [%s..]", $from, $to, $yesNo), 7, self::Log_file);
        $_collection = $this->_prepareCollection($from, $to);
        $this->processCollection($_collection);
        return $this;
    }


    /**
     * @param $from
     * @param $to
     * @return Object
     */
    protected function _prepareCollection($from, $to)
    {
        $orderStatus = Mage::getStoreConfig('payment/humm_payments/order_status');
        if (!$orderStatus) {
            $orderStatus = "processing";
        }
        $hummStatus = ['hummpending'];
        $hummFinish = [ Mage_Sales_Model_Order::STATE_PROCESSING ];
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->addFieldToSelect('*')
            ->addFieldToFilter('created_at', ['gteq' => $from])
            ->addFieldToFilter('created_at',
                ['lteq' => $to]
            )
            ->addFieldToFilter('status', ['in'  => $hummStatus]);

        $collection->getSelect()
            ->join(
                ["sop" => "sales_flat_order_payment"],
                'main_table.entity_id = sop.parent_id',
                array('method', 'amount_paid', 'amount_ordered')
            )
            ->where('sop.method like "%humm%" and sop.amount_paid is NULL');

        $collection->setOrder(
            'created_at',
            'desc'
        );
        Mage::log((string)$collection->getSelect(), 7, self::Log_file);
        return $collection;
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

    /**
     * @param $collection
     */

    public function processCollection($collection)
    {

        foreach ($collection as $key => $item) {
            Mage::log(sprintf("OrderID %s,Status %s", $item->getData('increment_id'), $item->getData('status')), 7, self::Log_file);
            $hummOrderId = $item->getData('increment_id');
            $this->processHummOrder($hummOrderId);
        }

    }

    /**
     * @param $hummOrderId
     */
    public function processHummOrder($hummOrderId)
    {

        try {
            $hummOrder = Mage::getModel('sales/order')->loadByIncrementId($hummOrderId);
            if ($hummOrder->getIncrementId() && $hummOrder->getStatus() != $hummOrder::STATE_CANCELED && $hummOrder->getStatus() == "hummpending") {
                $hummOrder->registerCancellation('cancelled by customer Cron Humm Payment ')->save();
            }
            $message = sprintf("OrderId %s is cancelled",$hummOrderId);
        } catch (Exception $e) {
            $message = $e->getMessage();

        }
        Mage::log($message,7,self::Log_file);
    }

    /**
     * @param array $statuses
     * @return mixed
     */
    public function getOrderCollectionByStatus($statuses = [])
    {
        $collection = $this->_orderCollectionFactory()->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('status',
                ['in' => $statuses]
            );
        return $collection;
    }
}