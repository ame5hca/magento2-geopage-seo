<?php

namespace GiftGroup\GeoPage\Model\DataProvider;

use GiftGroup\GeoPage\Model\ResourceModel\City\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use GiftGroup\GeoPage\Model\CityFactory;
use Magento\Framework\App\RequestInterface;

class City
{
    private $cityCollectionFactory;

    private $storeManager;

    private $cityFactory;

    private $request;

    private $city = null;

    public function __construct(
        CollectionFactory $cityCollectionFactory,
        StoreManagerInterface $storeManager,
        CityFactory $cityFactory,
        RequestInterface $request
    ) {
        $this->cityCollectionFactory = $cityCollectionFactory;
        $this->storeManager = $storeManager;
        $this->cityFactory = $cityFactory;
        $this->request = $request;
    }

    public function getAllCities($storeId = null)
    {
        if ($storeId == null) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        $collection = $this->cityCollectionFactory->create();
        $collection->addFieldToSelect(['name', 'code']);
        $collection->addFieldToFilter('is_active', ['eq' => 1]);
        $collection->addFieldToFilter(
            ['store_id'],
            [
                ['eq' => 0],
                ['eq' => $storeId]
            ]
        );
        return $collection;
    }
    
    public function getCityById($cityId)
    {
        if (!$this->city || ($cityId != $this->city->getId())) {
            $this->city = $this->cityFactory->create()->load($cityId);
        }
        return $this->city;
    }
    
    public function getCityByCode($cityCode)
    {
        $collection = $this->cityCollectionFactory->create();
        $collection->addFieldToSelect(['id', 'name']);
        $collection->addFieldToFilter('code', ['eq' => $cityCode]);
        if ($collection->getSize()) {
            return $collection->getFirstItem();
        }
        return null;
    }
    
    public function getCurrentCity()
    {
        $cityId = $this->request->getParam('id', null);
        if (!$cityId) {
            return null;
        }
        return $this->getCityById($cityId);
    }

    public function getRelatedCities($city, $limit = null, $storeId = null)
    {
        $collection = $this->getAllCities($storeId);
        $collection->addFieldToFilter('country_id', ['eq' => $city->getCountryId()]);
        $collection->addFieldToFilter('id', ['neq' => $city->getId()]);
        if ($limit) {
            $collection->setPageSize($limit);
            $collection->setCurPage(1);
        }
        $collection->getSelect()->orderRand();
        
        return $collection;
    }
}