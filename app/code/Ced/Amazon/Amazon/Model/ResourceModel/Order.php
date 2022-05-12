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
 * @category  Ced
 * @package   Ced_Amazon
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Amazon\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Order
 * @package Ced\Amazon\Model\ResourceModel
 * @method load(\Ced\Amazon\Api\Data\OrderInterface $object, $value, $field = null)
 * @method save(\Ced\Amazon\Api\Data\OrderInterface $object)
 */
class Order extends AbstractDb
{
    /**
     * @return void
     */
    public function _construct()
    {
        //amazon_orders is table and id is primary key of this table
        $this->_init(\Ced\Amazon\Model\Order::NAME, 'id');
    }

    public function isMarkeplaceOrder($id)
    {
        return (1 == $this->getConnection()->fetchOne(
            $this->getConnection()
                ->select()
                ->from(['sss' => $this->getMainTable()], [])
                ->where(\Ced\Amazon\Model\Order::COLUMN_MAGENTO_ORDER_ID . ' = ?', $id)
                ->columns(['COUNT(1)'])
            )
        );
    }
}
