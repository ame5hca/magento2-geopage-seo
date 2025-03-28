<?php

namespace GiftGroup\GeoPage\Model\Import\Helper;

use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use Magento\Framework\Serialize\SerializerInterface;
use GiftGroup\GeoPage\Model\Config;

class DataProcessor
{
    private $regionCollectionFactory;

    private $serializer;

    private $config;

    private $allStores = null;

    public function __construct(
        RegionCollectionFactory $regionCollectionFactory,
        SerializerInterface $serializer,
        Config $config
    ) {
        $this->regionCollectionFactory = $regionCollectionFactory;
        $this->serializer = $serializer;
        $this->config = $config;
    }

    public function region($row)
    {
        $region = $row['region'];
        $countryId = $row['country_id'];
        try {
            $collection = $this->regionCollectionFactory->create();
            $collection->addFieldToSelect('region_id');
            $collection->addCountryFilter($countryId);
            $collection->getSelect()->where('main_table.default_name like ?', "%" . $region . "%");
            if ($collection->getSize()) {
                return $collection->getFirstItem()->getRegionId();
            }
            return $region;
        } catch (\Exception $e) {
            return $region;
        }
    }
    
    public function removeSpaces($value)
    {
        $value = preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $value);
        $value = str_replace("\xc2\xa0", '', $value);

        return $this->trimSpaces($value);
    }
    
    public function formatCode($value)
    {
        $value = strtolower($value);
        return $this->removeSpaces($value);
    }
    
    public function trimSpaces($value)
    {
        return trim($value);
    }
    
    public function formatCityName($value)
    {
        $value = trim($value);
        $value = ucwords($value);

        return $value;
    }
    
    public function removeRegionNameFromCityName($regionName, $cityName)
    {
        $cityName = $this->removeSpaces($cityName);
        $cityName = preg_replace('/'.$regionName.'$/', '', $cityName);        

        return $cityName;
    }
    
    public function getSerializedValue($value)
    {
        if (empty($value)) {
            return $value;
        }
        $value = preg_replace('/\s+/', '', $value);
        $valueArray = explode(',', $value);
        return $this->serializer->serialize($valueArray);
    }
    
    public function getSerializedArray($value)
    {
        if (empty($value)) {
            $value = [];
        }
        return $this->serializer->serialize($value);
    }
    
    public function unserialize($value)
    {
        if (empty($value)) {
            return $value;
        }
        return $this->serializer->unserialize($value);
    }
    
    public function getCityCode($value)
    {
        return preg_replace('/\s+/', '-', $value);
    }
    
    public function getDefaultValue($fieldName, $storeId = 0)
    {
        $allStores = $this->getAllStores();
        $storeCode = isset($allStores[$storeId]) ? $allStores[$storeId] : null;
        return $this->config->getDefaultValue($fieldName, $storeCode);
    }
    
    public function convertToInt($value)
    {
        return (int) $value;
    }
    
    public function getAllStores()
    {
        return $this->allStores;
    }
    
    public function setAllStores($allStores)
    {
        $this->allStores = $allStores;
        return $this;
    }
    
    public function getSliderBlockDefaultContent($blockId)
    {
        $sliderContent = $this->config->getDefaultValue('slider');
        $sliderContent = sprintf($sliderContent, $blockId);

        return $sliderContent;
    }
}