<?php

namespace GiftGroup\GeoPage\Model\DataProvider;

use GiftGroup\GeoPage\Model\ResourceModel\CityPage\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\RequestInterface;
use GiftGroup\GeoPage\Model\CityPageFactory;
use Magento\Framework\Exception\LocalizedException;
use GiftGroup\GeoPage\Model\Config;
use GiftGroup\GeoPage\Model\Page\ShortCodeRenderer;

class CityPage
{
    private $cityPageCollectionFactory;

    private $storeManager;

    private $request;

    private $cityPageFactory;

    private $config;

    private $shortCodeRenderer;

    private $cityPage = null;

    private $storeCode = null;

    public function __construct(
        CollectionFactory $cityPageCollectionFactory,
        StoreManagerInterface $storeManager,
        RequestInterface $request,
        CityPageFactory $cityPageFactory,
        Config $config,
        ShortCodeRenderer $shortCodeRenderer
    ) {
        $this->cityPageCollectionFactory = $cityPageCollectionFactory;
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->cityPageFactory = $cityPageFactory;
        $this->config = $config;
        $this->shortCodeRenderer = $shortCodeRenderer;
    }

    public function getAllCities($storeId = null)
    {
        if ($storeId == null) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        $collection = $this->cityPageCollectionFactory->create();
        $collection->addFieldToSelect(['city_name', 'state_id']);
        $collection->addFieldToFilter('is_active', ['eq' => 1]);
        $collection->addFieldToFilter('store_id', ['eq' => $storeId]);
        $collection->addCityCodeToResult();
        $collection->addStateCodeToResult();

        return $collection;
    }

    public function getCurrentCityPage()
    {
        $pageId = $this->request->getParam('id', null);
        if (!$pageId) {
            throw new LocalizedException(
                __('Invalid page request, city id not found.')
            );
        }
        return $this->getCityPageById($pageId);
    }
    
    public function getCityPageById($cityPageId)
    {
        if (!$this->cityPage || $this->cityPage->getData('id') != $cityPageId) {
            $this->cityPage = $this->cityPageFactory->create()->load($cityPageId);
        }
        return $this->cityPage;
    }

    public function getMetaRobot($cityPageRegistry)
    {
        $cityPage = $cityPageRegistry->getCityPage();
        if ($cityPage->getMetaRobot() == 'INDEX,FOLLOW') {
            $sorting = $this->request->getParam('product_list_order', null);
            if ($sorting) {
                return 'NOINDEX,FOLLOW';
            }
        }
        return $cityPage->getMetaRobot();
    }

    public function getTitle($cityPageRegistry)
    {
        /* return __(
            '%1 Gift Baskets Delivery | Send Gift Basket Delivery To %1, %2 | %3', 
            $city->getName(),
            $city->getRegionName(),
            $this->config->getWebsiteName()); */
            return $this->shortCodeRenderer->render(
                $cityPageRegistry->getCity(),
                $cityPageRegistry->getCityPage()->getData('meta_title'),
                $cityPageRegistry->getCityPage()->getData('store_id')
            );
    }
    
    public function getDescription($cityPageRegistry)
    {
        /* return __(
            'Buy Gift Basket Online - Free Shipping To %1(%2, %3) Same-Day Delivery in %1 | Over 3200 Custom Designs ⏩ Order Now!',
            $city->getName(),
            $city->getRegionName(),
            $city->getCountryName()
        ); */
        return $this->shortCodeRenderer->render(
            $cityPageRegistry->getCity(),
            $cityPageRegistry->getCityPage()->getData('meta_description'),
            $cityPageRegistry->getCityPage()->getData('store_id')
        );
    }  
    
    public function getRelatedCityPages($cityRegistry, $limit)
    {
        $collection = $this->getAllCities($cityRegistry->getCityPage()->getData('store_id'));
        $collection->addFieldToFilter('country_id', ['eq' => $cityRegistry->getCity()->getCountryId()]);
        $collection->addFieldToFilter('id', ['neq' => $cityRegistry->getCityPage()->getData('id')]);
        if ($limit) {
            $collection->setPageSize($limit);
            $collection->setCurPage(1);
        }
        $collection->getSelect()->orderRand();
        
        return $collection;
    }
    
    public function getRelatedCityPagesForCategoryPage($categoryRegistry, $limit)
    {
        $collection = $this->getAllCities($categoryRegistry->getCategoryPage()->getData('store_id'));
        $collection->addFieldToFilter('country_id', ['eq' => $categoryRegistry->getCity()->getCountryId()]);
        if ($limit) {
            $collection->setPageSize($limit);
            $collection->setCurPage(1);
        }
        $collection->getSelect()->orderRand();
        
        return $collection;
    }

    public function getStoreCurrencyCode()
    {
        return $this->storeManager->getStore()->getCurrentCurrencyCode();
    }
    
    public function getCurrentStoreCode()
    {
        if (!$this->storeCode) {
            $this->storeCode = $this->storeManager->getStore()->getCode();
        }
        return $this->storeCode;
    }
}