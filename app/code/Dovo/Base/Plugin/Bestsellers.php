<?php
namespace Dovo\Base\Plugin;

use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Catalog\Model\Config;
use Magento\Framework\Data\Collection;

class Bestsellers
{
    /**
     * Bestsellers sorting attribute
     */
    const BESTSELLERS_SORT_BY = 'bestsellers';

    /**
     * @param Config $subject
     * @param $result
     * @return array
     */
    public function afterGetAttributeUsedForSortByArray(Config $subject, $result)
    {
        return array_merge($result, [self::BESTSELLERS_SORT_BY => __('Bestsellers')]);
    }

    /**
     * @param Toolbar $subject
     * @param Collection $collection
     */
    public function beforeSetCollection(Toolbar $subject, Collection $collection)
    {
        if ($subject->getCurrentOrder() == self::BESTSELLERS_SORT_BY) {
            $collection->getSelect()->joinLeft(
                'ds_sales_order_item',
                'e.entity_id = ds_sales_order_item.product_id',
                array('qty_ordered'=>'SUM(ds_sales_order_item.qty_ordered)'))
                ->group('e.entity_id')
                ->order('qty_ordered '.$subject->getCurrentDirection());
        }
    }
}