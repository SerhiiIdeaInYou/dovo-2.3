<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Jajuma\Shariff\Model\Config\Source\Icon;

class Orientation implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'horizontal', 'label' => __('Horizontal')],
            ['value' => 'vertical', 'label' => __('Vertical')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'horizontal' => __('Horizontal'),
            'vertical' => __('Vertical')
        ];
    }
}
