<?php

namespace GiftGroup\GeoPage\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use GiftGroup\GeoPage\Model\ResourceModel\City\Collection;

class Cities implements OptionSourceInterface
{
    private $cityCollection;

    public function __construct(
        Collection $cityCollection
    ) {
        $this->cityCollection = $cityCollection;
    }

    public function toOptionArray()
    {
        $options = [];
        $cities = $this->cityCollection->addFieldToSelect(['id', 'name'])
            ->addFieldToFilter('is_active', ['eq' => 1]);
        if ($cities->getSize()) {
            foreach ($cities as $city) {
                $options[] = ['label' => $city->getName(), 'value' => $city->getId()];
            }
        }
        return $options;
    }
}