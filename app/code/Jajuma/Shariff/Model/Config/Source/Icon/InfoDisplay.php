<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Jajuma\Shariff\Model\Config\Source\Icon;

class InfoDisplay implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'blank', 'label' => __('Blank')],
            ['value' => 'popup', 'label' => __('Popup')],
            ['value' => 'self', 'label' => __('Self')],
        ];
    }
}
