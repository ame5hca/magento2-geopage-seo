<?php

namespace GiftGroup\GeoPage\Model\Import\Helper;

use Magento\Framework\App\ResourceConnection;
use GiftGroup\GeoPage\Model\ResourceModel\States;
use GiftGroup\GeoPage\Model\ResourceModel\City;
use GiftGroup\GeoPage\Model\ResourceModel\CityPage;
use GiftGroup\GeoPage\Model\ResourceModel\CityCategoryPage;
use GiftGroup\GeoPage\Model\ResourceModel\StatePage;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use GiftGroup\GeoPage\Model\Config;
use GiftGroup\GeoPage\Model\Page\ProductsGenerator;

class AdditionalDataProvider
{
    private $resourceConnection;

    private $storeRepository;

    private $categoryCollectionFactory;

    private $productsGenerator;

    private $allStoreCode = null;

    public function __construct(
        ResourceConnection $resourceConnection,
        StoreRepositoryInterface $storeRepository,
        CollectionFactory $categoryCollectionFactory,
        ProductsGenerator $productsGenerator
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->storeRepository = $storeRepository;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->productsGenerator = $productsGenerator;
    }

    public function getAllStates()
    {
        $states = [];
        $connection = $this->resourceConnection->getConnection();
        $states = $connection->fetchAll(
            'SELECT state_code, id, state_name, country_code FROM ' . $connection->getTableName(States::TABLE)
        );
        return $states;
    }
    
    public function getAllStatesDataByIndexAndId()
    {
        $statesByIndex = $statesById = [];
        $states = $this->getAllStates();
        foreach ($states as $state) {
            $stateIndex = $state['state_name'] . '|' . $state['country_code'];
            $statesByIndex[$stateIndex] = ['state_code' => $state['state_code'], 'id' => $state['id']];
            $statesById[$state['id']] = $state['state_code'];
        }
        return ['by_index' => $statesByIndex, 'by_id' => $statesById];

    }
    
    public function getAllCities()
    {
        $cities = [];
        $connection = $this->resourceConnection->getConnection();
        $cities = $connection->fetchAll(
            'SELECT id,code FROM ' . $connection->getTableName(City::TABLE)
        );
        return $cities;
    }
    
    public function getAllCitiesByCodeAndId()
    {
        $citiesByCode = $citiesById = [];
        $cities = $this->getAllCities();
        foreach ($cities as $city) {
            $citiesByCode[$city['code']] = $city['id'];
            $citiesById[$city['id']] = $city['code'];
        }
        return ['by_code' => $citiesByCode, 'by_id' => $citiesById];;
    }

    public function getLastAddedCityPages()
    {
        $connection = $this->resourceConnection->getConnection();
        $cityPages = $connection->fetchAll(
            'SELECT id,city_id,state_id,store_id FROM '. $connection->getTableName(CityPage::TABLE) .' WHERE created_at > :current_date',
            ['current_date' => date('Y-m-d 00:00:00')]
        );

        return $cityPages;
    }
    
    public function getLastAddedCategoryPages()
    {
        $connection = $this->resourceConnection->getConnection();
        $categoryPages = $connection->fetchAll(
            'SELECT id,city_id,state_id,store_id,category_id FROM '. $connection->getTableName(CityCategoryPage::TABLE) .' WHERE created_at > :current_date',
            ['current_date' => date('Y-m-d 00:00:00')]
        );

        return $categoryPages;
    }
    
    public function getLastAddedStatePages()
    {
        $connection = $this->resourceConnection->getConnection();
        $categoryPages = $connection->fetchAll(
            'SELECT id,state_id,store_id FROM '. $connection->getTableName(StatePage::TABLE) .' WHERE created_at > :current_date',
            ['current_date' => date('Y-m-d 00:00:00')]
        );

        return $categoryPages;
    }

    public function getAllStores()
    {
        $allStoreIds = [];
        $allStores = $this->storeRepository->getList();
        foreach ($allStores as $store) {
            if ($store->getId() == 0) {
                continue;
            }
            $allStoreIds[] = $store->getId();
        }

        return $allStoreIds;
    }
    
    public function getAllStoresCode()
    {
        if (!$this->allStoreCode) {
            $this->allStoreCode = [];
            $allStores = $this->storeRepository->getList();
            foreach ($allStores as $store) {
                if ($store->getId() == 0) {
                    continue;
                }
                $this->allStoreCode[$store->getId()] = $store->getCode();
            }
        }
        return $this->allStoreCode;
    }

    public function getCategoryCollection()
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect(['id', 'url_key']);
        if ($collection->getSize()) {
            return $collection;
        }
        return null;
    }

    public function getProducts($storeId, $categoryId = null)
    {
        return $this->productsGenerator->generate(
            $storeId, 
            Config::CITY_PAGE_DEFAULT_PRODUCT_LIST_LIMIT, 
            $categoryId
        );
    }
}