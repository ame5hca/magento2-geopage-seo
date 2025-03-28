<?php

namespace GiftGroup\GeoPage\Model\State;

use GiftGroup\GeoPage\Model\ResourceModel\States\CollectionFactory as StateCollectionFactory;

class DataManager
{
    private $stateCollectionFactory;

    public function __construct(
        StateCollectionFactory $stateCollectionFactory
    ) {
        $this->stateCollectionFactory = $stateCollectionFactory;
    }

    public function getStateCode($stateName)
    {
        $stateCode = strtolower(preg_replace('/\s+/', '-', $stateName));
        return $this->getUniqueCode($stateCode);
    }

    public function getUniqueCode($code, $suffix = '')
    {
        $code .= $suffix;
        if ($this->isCodeExist($code)) {
            return $this->getUniqueCode($code, '_1');
        }

        return $code;
    }
    
    private function isCodeExist($code)
    {
        $collection = $this->stateCollectionFactory->create();
        $collection->addFieldToFilter('state_code', ['eq' => $code]);
        if ($collection->getSize()) {
            return true;
        }
        return false;
    }
    
    public function getStateIdFromCountryRegion($regionName, $countryId)
    {
        $collection = $this->stateCollectionFactory->create();
        $collection->addFieldToSelect('id');
        $collection->addFieldToFilter('state_name', ['like' => $regionName]);
        $collection->addFieldToFilter('country_code', ['eq' => $countryId]);
        if ($collection->getSize()) {
            return $collection->getFirstItem()->getId();
        }
        return null;
    }
}