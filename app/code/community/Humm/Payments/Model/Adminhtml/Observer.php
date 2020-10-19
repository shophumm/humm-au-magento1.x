<?php
/**
 * Class Humm_Payments_Model_Adminhtml_Observer
 */
class Humm_Payments_Model_Adminhtml_Observer
{

    /**
     * get model helper
     */
    protected function getHelper()
    {
        return Mage::helper('humm_payments');
    }

    /**
     * Load admin config dynamically
     */
    public function loadConfig(Varien_Event_Observer $observer)
    {
        $paymentGroups = $observer->getEvent()->getConfig()->getNode('sections/payment/groups');

        $payments = $paymentGroups->xpath('humm_payments_solution/*');

        foreach ($payments as $payment) {
            $fields = $paymentGroups->xpath((string) $payment->group . '/fields');

            if (isset($fields[0])) {
                $fields[0]->appendChild($payment, true);
            }
        }
    }

}
