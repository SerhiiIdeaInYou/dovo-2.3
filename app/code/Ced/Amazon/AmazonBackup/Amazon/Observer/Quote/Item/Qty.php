<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement(EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Amazon
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Amazon\Observer\Quote\Item;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Qty implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        try {
            $item = $observer->getItem();
            if ($item->getQuote()->getAmazonOrderId() && $item->getQuote()->getData('amazon_back_order')) {
                $item->setHasError(false);
            }
        } catch (\Exception $e) {
            //silence
        }
    }
}
