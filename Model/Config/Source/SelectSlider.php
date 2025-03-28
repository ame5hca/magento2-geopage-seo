<?php

namespace GiftGroup\GeoPage\Model\Config\Source;

use Ubertheme\UbContentSlider\Model\Config\Source\SelectSlider as CoreSelectSlider;

class SelectSlider extends CoreSelectSlider
{
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        $placeHolderOption = ['label' => 'Please select', 'value' => 0];
        array_unshift($options, $placeHolderOption);
        
        return $options;
    }
}
