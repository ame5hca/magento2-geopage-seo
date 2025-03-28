<?php

namespace GiftGroup\GeoPage\Model\Page\CityCategoryPage;

use GiftGroup\GeoPage\Model\City;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\Store;
use GiftGroup\GeoPage\Model\CityCategoryPageFactory;
use GiftGroup\GeoPage\Model\ResourceModel\CityCategoryPage\CollectionFactory as CategoryPageCollectionFactory;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use GiftGroup\GeoPage\Model\DataProvider\City as CityDataProvider;
use Magento\Framework\Exception\LocalizedException;
use GiftGroup\GeoPage\Model\Config;
use GiftGroup\GeoPage\Model\Page\ProductsGenerator;
use GiftGroup\GeoPage\Model\CityPage;

class Generator
{
    protected $serializer;

    protected $storeRepository;

    protected $categoryPageFactory;

    protected $categoryPageCollectionFactory;

    protected $productsGenerator;

    protected $eventManager;

    protected $cityDataProvider;

    public function __construct(
        SerializerInterface $serializer,
        StoreRepositoryInterface $storeRepository,
        CityCategoryPageFactory $categoryPageFactory,
        CategoryPageCollectionFactory $categoryPageCollectionFactory,
        ProductsGenerator $productsGenerator,
        EventManagerInterface $eventManager,
        CityDataProvider $cityDataProvider
    ) {
        $this->serializer = $serializer;
        $this->storeRepository = $storeRepository;
        $this->categoryPageFactory = $categoryPageFactory;
        $this->categoryPageCollectionFactory = $categoryPageCollectionFactory;
        $this->productsGenerator = $productsGenerator;
        $this->eventManager = $eventManager;
        $this->cityDataProvider = $cityDataProvider;
    }

    public function fromCity(City $city)
    {
        $categories = $city->getCategories();
        if (count($categories)) {
            $pageData = $this->prepareCommonPageData($city);
            foreach ($categories as $categoryId) {
                $pageData['category_id'] = $categoryId;
                $this->generatePage($pageData, $city->getPageStoreId()); 
            }
        }
        return true;
    }
    
    public function fromCityPage(CityPage $cityPage, $pageStoreIds)
    {
        $categories = $cityPage->getCategories();
        if (count($categories)) {
            $pageData = $this->prepareCommonPageData($cityPage);
            foreach ($categories as $categoryId) {
                $pageData['category_id'] = $categoryId;
                $this->generatePage($pageData, $pageStoreIds);
            }
        }
        return true;
    }

    public function fromImport(array $data)
    {
        $pageData = $this->prepareCommonPageData($data);
        $pageData['category_id'] = $data['category_id'];
        $categoryPage = $this->generatePage($pageData, $data['store_id']);
        return $categoryPage;
    }
    
    protected function generatePage($pageData, $storeId)
    {
        $categoryPage = null;
        $pageStoreIds = $this->getFinalPageStoreIds($storeId);
        foreach ($pageStoreIds as $storeId) {
            if ($storeId == Store::DEFAULT_STORE_ID) {
                continue;
            }
            $pageData['store_id'] = $storeId;
            $pageData = $this->preparePageDataForStore($pageData);
            $categoryPage = $this->createOrUpdateCityCategoryPage($pageData);
        }
        return $categoryPage;
    }

    private function prepareCommonPageData($item)
    {
        $pageData = $item;
        if ($item instanceof City) {
            $pageData = $item->getData();
            $pageData['city_id'] = $pageData['id'];
            $pageData['city_code'] = $pageData['code'];
            $state = $item->getState();
            $pageData['state_id'] = $state ? $state->getId() : 0;
            $pageData['state_code'] = $state ? $state->getData('state_code') : '';
            unset($pageData['id']);
        } elseif ($item instanceof CityPage){
            $pageData = $item->getData();
            $state = $item->getState();
            $pageData['state_id'] = $state ? $state->getId() : 0;
            $pageData['state_code'] = $state ? $state->getData('state_code') : '';
            unset($pageData['id']);
        } else {
            $city = $this->cityDataProvider->getCityByCode($item['code']);
            if (!$city) {
                throw new LocalizedException(
                    __('City with the code %1 not exist. Please check.', $item['code'])
                );
            }
            $pageData['city_id'] = $city->getData('id');
            $state = $city->getState();
            $pageData['state_id'] = $state ? $state->getId() : 0;
            $pageData['state_code'] = $state ? $state->getData('state_code') : '';
        }

        return $pageData;
    }
    
    private function preparePageDataForStore($pageData)
    {
        $productLimit = !empty($pageData['product_limit']) ?? Config::CITY_PAGE_DEFAULT_PRODUCT_LIST_LIMIT;
        $pageData['product_ids'] = $this->serializer->serialize(
            $this->productsGenerator->generate($pageData['store_id'], $productLimit, $pageData['category_id'])
        );
        $pageData['product_limit'] = $productLimit;

        return $pageData;
    }

    private function getFinalPageStoreIds($storeIds)
    {
        if (!is_array($storeIds)) {
            $storeIds = $this->serializer->unserialize($storeIds);
        }
        if (in_array("0", $storeIds)) {
            $allStoreIds = [];
            $allStores = $this->storeRepository->getList();
            foreach ($allStores as $store) {
                $allStoreIds[] = $store->getId();
            }
            $storeIds = array_merge($storeIds, $allStoreIds);
            $storeIds = array_unique($storeIds);
        }
        return $storeIds;
    }

    private function createOrUpdateCityCategoryPage($pageData)
    {
        $collection = $this->categoryPageCollectionFactory->create();
        $collection->addFieldToFilter('city_id', ['eq' => $pageData['city_id']]);
        $collection->addFieldToFilter('category_id', ['eq' => $pageData['category_id']]);
        $collection->addFieldToFilter('store_id', ['eq' => $pageData['store_id']]);
        if ($collection->getSize()) {
            $categoryPage = $collection->getFirstItem();
            $pageData['id'] = $categoryPage->getId();
        } else {
            $categoryPage = $this->categoryPageFactory->create();
        }
        $categoryPage->setData($pageData);
        $categoryPage->setCityCode($pageData['city_code']);
        $categoryPage->setStateCode($pageData['state_code']);
        $categoryPage->save();
        
        $this->eventManager->dispatch(
            'giftgroup_geopage_new_category_page_generated', 
            ['category_page' => $categoryPage]
        );

        return $categoryPage;   
    }
}
