<?php

namespace GiftGroup\GeoPage\Model\DataProvider;

use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory  as RegionCollectionFactory;

class CityLocation
{
    private $countryFactory;

    private $regionCollectionFactory;

    public function __construct(
        CountryFactory $countryFactory,
        RegionCollectionFactory $regionCollectionFactory
    ) {
        $this->countryFactory = $countryFactory;
        $this->regionCollectionFactory = $regionCollectionFactory;
    }

    public function getCountryName($countryCode)
    {
        try {
            $country = $this->countryFactory->create()->loadByCode($countryCode);
            $countryName = $country->getName();
        } catch (\Exception $e) {
            $countryName = '';
        }
        return $countryName;
    }
    
    public function getRegionName($region)
    {
        if (is_numeric($region)) {
            $regionModel = $this->regionCollectionFactory->create()->getItemById($region);
            $region = $regionModel->getName();
        }
        return $region;
    }
    
    public function getRegionIdFromName($regionName, $countryCode)
    {
        $collection = $this->regionCollectionFactory->create();
        $collection->addFieldToSelect('region_id');
        $collection->addFieldToFilter('default_name', ['like' => '%' . $regionName . '%']);
        $collection->addCountryCodeFilter($countryCode);
        if ($collection->getSize()) {
            return $collection->getFirstItem()->getData('region_id');
        }
        return null;
    }
}