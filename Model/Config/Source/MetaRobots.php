<?php

namespace GiftGroup\GeoPage\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class MetaRobots implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => 'INDEX, FOLLOW', 'value' => 'INDEX,FOLLOW'],
            ['label' => 'NOINDEX, FOLLOW', 'value' => 'NOINDEX,FOLLOW'],
            ['label' => 'INDEX, NOFOLLOW', 'value' => 'INDEX,NOFOLLOW'],
            ['label' => 'NOINDEX, NOFOLLOW', 'value' => 'NOINDEX,NOFOLLOW']
        ];
    }
}