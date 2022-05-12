<?php

namespace Dovo\Amazon\Observer;

use Magento\Framework\Event\ObserverInterface;
use Dovo\Amazon\Logger\Logger;

class OrderImporter implements ObserverInterface
{

    protected  $_logger;

    public function __construct(Logger $logger)
    {
        // Observer initialization code...
        // You can use dependency injection to get any class this observer may need.
        $this->_logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->_logger->info('Observer Called');
        try {
            $order = $observer->getData('order');
            $this->_logger->info('Order Loaded with: ' . $order->getId());
            // Additional observer execution code...
            $grandTotal = $order->getGrandTotal();
            $this->_logger->info($grandTotal);
        } catch (\Throwable $e) {
            $this->_logger->info('Error: ' . $e->getMessage());
        }
        return $this;
    }
}