<?php

namespace Dovo\HeaderImage\Model\Source;

class HeroPosition
{
    public function toOptionArray() {
        return [
            ['value' => 'left',  'label' => __('Left')],
            ['value' => 'right',  'label' => __('Right')]
        ];
    }
}
