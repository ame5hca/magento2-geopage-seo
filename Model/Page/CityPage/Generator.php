<?php

namespace GiftGroup\GeoPage\Model\Page\CityPage;

use GiftGroup\GeoPage\Model\City;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\Store;
use GiftGroup\GeoPage\Model\CityPageFactory;
use GiftGroup\GeoPage\Model\ResourceModel\CityPage\CollectionFactory as CityPageCollectionFactory;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use GiftGroup\GeoPage\Model\DataProvider\City as CityDataProvider;
use Magento\Framework\Exception\LocalizedException;
use GiftGroup\GeoPage\Model\Config;
use GiftGroup\GeoPage\Model\Page\ProductsGenerator;

class Generator
{
    protected $serializer;

    protected $storeRepository;

    protected $cityPageFactory;

    protected $cityPageCollectionFactory;

    protected $productsGenerator;

    protected $eventManager;

    protected $cityDataProvider;

    public function __construct(
        SerializerInterface $serializer,
        StoreRepositoryInterface $storeRepository,
        CityPageFactory $cityPageFactory,
        CityPageCollectionFactory $cityPageCollectionFactory,
        ProductsGenerator $productsGenerator,
        EventManagerInterface $eventManager,
        CityDataProvider $cityDataProvider
    ) {
        $this->serializer = $serializer;
        $this->storeRepository = $storeRepository;
        $this->cityPageFactory = $cityPageFactory;
        $this->cityPageCollectionFactory = $cityPageCollectionFactory;
        $this->productsGenerator = $productsGenerator;
        $this->eventManager = $eventManager;
        $this->cityDataProvider = $cityDataProvider;
    }

    public function fromCity(City $city)
    {
        $cityPage = $this->generatePage($city, $city->getPageStoreId());
        $this->updateCityPageGenerateStatus($city);
        return $cityPage;
    }

    public function fromImport(array $data)
    {
        $cityPage = $this->generatePage($data, $data['page_store_id']);
        $this->updateCityPageGenerateStatus($data['code']);
        return $cityPage;
    }
    
    protected function generatePage($item, $storeId)
    {
        $cityPage = null;
        $pageData = $this->prepareCommonPageData($item);
        $pageStoreIds = $this->getFinalPageStoreIds($storeId);
        foreach ($pageStoreIds as $storeId) {
            if ($storeId == Store::DEFAULT_STORE_ID) {
                continue;
            }
            $pageData['store_id'] = $storeId;
            $pageData = $this->preparePageDataForStore($pageData);
            $cityPage = $this->createOrUpdateCityPage($pageData);
        }
        return $cityPage;
    }

    private function prepareCommonPageData($item)
    {
        $pageData = $item;
        if ($item instanceof City) {
            $pageData = $item->getData();
            $pageData['city_id'] = $pageData['id'];
            $pageData['city_code'] = $pageData['code'];
            $pageData['city_name'] = $pageData['name'];
            $state = $item->getState();
            $pageData['state_id'] = $state ? $state->getId() : 0;
            $pageData['state_code'] = $state ? $state->getData('state_code') : '';
            unset($pageData['id']);
            unset($pageData['code']);
            unset($pageData['name']);
        } else {
            $city = $this->cityDataProvider->getCityByCode($item['code']);
            if (!$city) {
                throw new LocalizedException(
                    __('City with the code %1 not exist. Please check.', $item['code'])
                );
            }
            $pageData['city_id'] = $city->getData('id');
            $pageData['city_code'] = $item['code'];
            $pageData['city_name'] = $city->getData('name');
            $state = $city->getState();
            $pageData['state_id'] = $state ? $state->getData('id') : 0;
            $pageData['state_code'] = $state ? $state->getData('state_code') : '';
        }

        return $pageData;
    }
    
    private function preparePageDataForStore($pageData)
    {
        $productLimit = !empty($pageData['product_limit']) ? $pageData['product_limit'] : Config::CITY_PAGE_DEFAULT_PRODUCT_LIST_LIMIT;
        $pageData['product_ids'] = $this->serializer->serialize(
            $this->productsGenerator->generate($pageData['store_id'], $productLimit)
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

    private function createOrUpdateCityPage($pageData)
    {
        $cityPageCollection = $this->cityPageCollectionFactory->create();
        $cityPageCollection->addFieldToFilter('city_id', ['eq' => $pageData['city_id']]);
        $cityPageCollection->addFieldToFilter('store_id', ['eq' => $pageData['store_id']]);
        if ($cityPageCollection->getSize()) {
            $cityPage = $cityPageCollection->getFirstItem();
            $pageData['id'] = $cityPage->getId();
        } else {
            $cityPage = $this->cityPageFactory->create();
        }
        $cityPage->setData($pageData);
        $cityPage->setCityCode($pageData['city_code']);
        $cityPage->setStateCode($pageData['state_code']);
        $cityPage->save();
        
        $this->eventManager->dispatch(
            'giftgroup_geopage_new_city_page_generated', 
            ['city_page' => $cityPage]
        );

        return $cityPage;   
    }
    
    private function updateCityPageGenerateStatus($city) {
        if (!$city instanceof City) {
            $city = $this->cityDataProvider->getCityByCode($city);
        }
        $city->setData('is_page_generated', true);
        $city->save();
        
        return true;
    }
}
