<?php

$installer = $this;

$installer->startSetup();


$hummProcessingStatus = 'hummpending';
$installer->run( "DELETE FROM `{$installer->getTable('sales_order_status')}` WHERE status='{$hummProcessingStatus}';" );
$installer->run( "INSERT INTO `{$this->getTable('sales_order_status')}` (`status`, `label`) VALUES ('{$hummProcessingStatus}', 'HummPending');" );

$installer->endSetup();