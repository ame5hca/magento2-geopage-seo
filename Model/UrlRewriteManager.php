<?php

namespace GiftGroup\GeoPage\Model;

use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use GiftGroup\GeoPage\Model\City;
use GiftGroup\GeoPage\Model\CityPage;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use GiftGroup\GeoPage\Logger\Logger;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Api\StoreRepositoryInterface;
use GiftGroup\GeoPage\Model\Config;
use Magento\Store\Model\Store;

class UrlRewriteManager
{
    private $urlRewriteCollectionFactory;

    private $urlRewriteFactory;

    private $logger;

    private $storeRepository;

    public function __construct(
        UrlRewriteCollectionFactory $urlRewriteCollectionFactory,
        UrlRewriteFactory $urlRewriteFactory,
        Logger $logger,
        StoreRepositoryInterface $storeRepository
    ) {
        $this->urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->logger = $logger;
        $this->storeRepository = $storeRepository;
    }

    public function addCityUrlRewrite(City $city, $storeId = 1)
    {
        // This fun is not needed, need to be removed after deep checking
        $cityTargetPath = sprintf(Config::CITY_PAGE_TARGET_URL, $city->getId());
        $cityRequestPath = sprintf(Config::CITY_PAGE_URL, $city->getState()->getData('state_code'), $city->getCode());
        try {
            if (!$this->isUrlRewriteExist($cityRequestPath, $storeId)) {                
                $this->addStoreRewrite(
                    $cityRequestPath,
                    $cityTargetPath,
                    $storeId
                );
            }
            return true;
        } catch (\Exception $e) {
            $this->logger->info('UrlRewriteError : ' . $e->getMessage());
            throw new LocalizedException(__('Failed to add the url rewrite for the city page'));
        }        
    }
    
    public function addCityPageUrlRewrite(CityPage $cityPage)
    {
        $cityPageTargetPath = sprintf(Config::CITY_PAGE_TARGET_URL, $cityPage->getId());
        $cityPageRequestPath = sprintf(Config::CITY_PAGE_URL, $cityPage->getData('state_code'), $cityPage->getData('city_code'));
        try {
            if (!$this->isUrlRewriteExist($cityPageRequestPath, $cityPage->getStoreId())) {                
                $this->addStoreRewrite(
                    $cityPageRequestPath,
                    $cityPageTargetPath,
                    $cityPage->getStoreId()
                );
            }
            return true;
        } catch (\Exception $e) {
            $this->logger->info('UrlRewriteError : ' . $e->getMessage());
            throw new LocalizedException(__('Failed to add the url rewrite for the city page'));
        }        
    }
    
    public function addCityCategoryUrlRewrite(CityPage $cityPage, $categoryData)
    {
        $targetPath = sprintf(
            Config::CITY_CATEGORY_PAGE_TARGET_URL, 
            $cityPage->getCityId(),
            $categoryData['id']
        );
        $requestPath = sprintf(
            Config::CITY_CATEGORY_PAGE_URL, 
            $cityPage->getCityCode(),
            $categoryData['url_key']
        );
        try {
            if (!$this->isUrlRewriteExist($requestPath, $cityPage->getStoreId())) {                
                $this->addStoreRewrite(
                    $requestPath,
                    $targetPath,
                    $cityPage->getStoreId()
                );
            }
            return true;
        } catch (\Exception $e) {
            $this->logger->info('CityCategoryPageUrlRewriteError : ' . $e->getMessage());
            throw new LocalizedException(__('Failed to add the url rewrite for the category page'));
        }        
    }

    public function addCityCategoryPageUrlRewrite(CityCategoryPage $categoryPage)
    {
        $targetPath = sprintf(
            Config::CITY_CATEGORY_PAGE_TARGET_URL, 
            $categoryPage->getData('id')
        );
        $requestPath = sprintf(
            Config::CITY_CATEGORY_PAGE_URL, 
            $categoryPage->getStateCode(),
            $categoryPage->getCityCode(),
            $categoryPage->getCategoryUrlKey()
        );
        try {
            if (!$this->isUrlRewriteExist($requestPath, $categoryPage->getStoreId())) {                
                $this->addStoreRewrite(
                    $requestPath,
                    $targetPath,
                    $categoryPage->getStoreId()
                );
            }
            return true;
        } catch (\Exception $e) {
            $this->logger->info('CityCategoryPageUrlRewriteError : ' . $e->getMessage());
            throw new LocalizedException(__('Failed to add the url rewrite for the category page'));
        }        
    }

    public function addStatePageUrlRewrite(StatePage $statePage)
    {
        $statePageTargetPath = sprintf(Config::STATE_PAGE_TARGET_URL, $statePage->getData('id'));
        $statePageRequestPath = sprintf(Config::STATE_PAGE_URL, $statePage->getStateCode());
        try {
            if (!$this->isUrlRewriteExist($statePageRequestPath, $statePage->getStoreId())) {                
                $this->addStoreRewrite(
                    $statePageRequestPath,
                    $statePageTargetPath,
                    $statePage->getStoreId()
                );
            }
            return true;
        } catch (\Exception $e) {
            $this->logger->info('UrlRewriteError : ' . $e->getMessage());
            throw new LocalizedException(__('Failed to add the url rewrite for the state page'));
        }        
    }

    public function addStoreRewrite($request, $target, $storeId = 1)
    {
        /**
         * If the store id = 0 means it will be All store view scope.
         * So we will create the rewrite for all store views for the city page
         */
        if ($storeId == Store::DEFAULT_STORE_ID) {
            foreach ($this->storeRepository->getList() as $store) {
                $this->createRewrite($request, $target, $store->getId());
            }
            return true;
        }
        return $this->createRewrite($request, $target, $storeId);
    }

    private function createRewrite($request, $target, $storeId)
    {
        $urlRewrite = $this->urlRewriteFactory->create();
        $urlRewrite->setStoreId($storeId);
        $urlRewrite->setIsSystem(0);
        $urlRewrite->setIdPath(rand(1, 100000));
        $urlRewrite->setTargetPath($target);
        $urlRewrite->setRequestPath($request);
        $urlRewrite->setRedirectType(0);
        $urlRewrite->save();

        return true;
    }
    
    public function isUrlRewriteExist($requestPath, $storeId = 1)
    {
        $collection = $this->urlRewriteCollectionFactory->create();
        $collection->addFieldToFilter('request_path', ['eq' => $requestPath]);
        if (!empty($storeId)) {
            $collection->addFieldToFilter('store_id', ['eq' => $storeId]);
        }        
        if ($collection->getSize()) {
            return $collection;
        }
        return false;
    }
    
    public function isUrlRewriteExistWithPattern($requestPath, $storeId = null)
    {
        $collection = $this->urlRewriteCollectionFactory->create();
        $collection->addFieldToFilter('request_path', ['like' => '%' . $requestPath . '%']);
        if (!empty($storeId)) {
            $collection->addFieldToFilter('store_id', ['eq' => $storeId]);
        }        
        if ($collection->getSize()) {
            return $collection;
        }
        return false;
    }

    public function deleteCityUrlRewrite(City $city)
    {
        try {
            /**
             * When a city is deleted, then delete the corresponding city page and category page
             * url rewrites also. All these urls have a general format and by using the pattern
             * match, it can be deleted in single query.
             */
            $cityRequestPath = sprintf(
                Config::PATTERN_FOR_URLREWRITE_OF_CITY_AND_CATEGORY,
                '%',
                $city->getCode(),
                '%'
            );
            $collection = $this->isUrlRewriteExistWithPattern($cityRequestPath);
            if ($collection) {
                foreach ($collection as $rewrite) {
                    $rewrite->delete();
                }
            }
            return true;
        } catch (\Exception $e) {
            $this->logger->info('UrlRewriteDeleteError : ' . $e->getMessage());
            throw new LocalizedException(__('Something went wrong while removing the rewrites related to the city.'));
        }
        
    }
}