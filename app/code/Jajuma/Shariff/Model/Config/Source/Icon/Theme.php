<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Jajuma\Shariff\Model\Config\Source\Icon;

class Theme implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'standard', 'label' => __('Standard')],
            ['value' => 'grey', 'label' => __('Grey')],
            ['value' => 'white', 'label' => __('White')],
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
            'standard' => __('Standard'),
            'grey' => __('Grey'),
            'white' => __('White'),
        ];
    }
}
