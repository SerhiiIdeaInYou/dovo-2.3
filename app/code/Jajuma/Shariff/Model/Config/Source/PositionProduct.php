<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Jajuma\Shariff\Model\Config\Source;

class PositionProduct implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Do not show')],
            ['value' => 'left', 'label' => __('Floating left')],
            ['value' => 'right', 'label' => __('Floating right')],
            ['value' => 'below', 'label' => __('Below Wishlist / Compare')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            0 => __('Do not show'),
            'left' => __('Floating left'),
            'right' => __('Floating right'),
            'below' => __('Below Wishlist / Compare')
        ];
    }
}
