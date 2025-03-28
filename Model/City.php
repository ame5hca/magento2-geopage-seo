<?php

namespace GiftGroup\GeoPage\Model;

use Magento\Framework\Model\AbstractModel;
use GiftGroup\GeoPage\Model\ResourceModel\City as CityResource;
use GiftGroup\GeoPage\Model\Config;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use GiftGroup\GeoPage\Model\ResourceModel\CityPage\CollectionFactory as CityPageCollectionFactory;
use GiftGroup\GeoPage\Model\ResourceModel\States\CollectionFactory as StatesCollectionFactory;
use GiftGroup\GeoPage\Model\DataProvider\CityLocation;

/**
 * City model class
 */
class City extends AbstractModel
{
    /**
     * Cache tag
     */
    public const CACHE_TAG = 'geopage_city';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @var string
     */
    protected $_eventPrefix = self::CACHE_TAG;

    private $serializer;

    private $storeManager;

    private $cityPageCollectionFactory;

    protected $cityPage = null;

    private $cityLocation;

    private $statesCollectionFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        SerializerInterface $serializer,
        StoreManagerInterface $storeManager,
        CityPageCollectionFactory $cityPageCollectionFactory,
        CityLocation $cityLocation,
        StatesCollectionFactory $statesCollectionFactory
    ) {
        parent::__construct($context, $registry);
        $this->serializer = $serializer;
        $this->storeManager = $storeManager;
        $this->cityPageCollectionFactory = $cityPageCollectionFactory;
        $this->cityLocation = $cityLocation;
        $this->statesCollectionFactory = $statesCollectionFactory;
    }

    /**
     * Construct function
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CityResource::class);
    }

    public function getUrl()
    {
        return sprintf(Config::CITY_PAGE_URL, $this->getState()->getData('state_code'), $this->getCode());
    }
    
    public function getCategoryPageUrl($categoryUrlKey)
    {
        return sprintf(Config::CITY_CATEGORY_PAGE_URL, $this->getCode(), $categoryUrlKey);
    }

    public function getCategories()
    {
        $categories = $this->getData('categories');
        if (is_array($categories)) {
            return $categories;
        }
        if (is_null($categories)) {
            return [];
        }
        return $this->serializer->unserialize($categories);
    }
    
    public function getBlock3Category()
    {
        $categories = $this->getData('block3_category');
        if (is_array($categories)) {
            return $categories;
        }
        if (is_null($categories)) {
            return [];
        }
        return $this->serializer->unserialize($categories);
    }
    
    public function getPageStoreId()
    {
        $pageStoreIds = $this->getData('page_store_id');
        if (is_array($pageStoreIds)) {
            return $pageStoreIds;
        }
        /**
         * If the page store id is not selected, then just return the
         * default admin store id (all view store id) as default value
         */
        if (is_null($pageStoreIds)) {
            return [Store::DEFAULT_STORE_ID];
        }
        return $this->serializer->unserialize($pageStoreIds);
    }

    public function loadByCode($value)
    {
        $this->load($value, 'code');
        return $this;
    }

    protected function _afterLoad()
    {
        $this->setData('categories', $this->getCategories());
        $this->setData('page_store_id', $this->getPageStoreId());
        $this->setData('block3_category', $this->getBlock3Category());
        return parent::_afterLoad();
    }

    public function beforeSave()
    {
        $categories = $this->getData('categories');
        if (is_array($categories)) {
            $categories = $this->serializer->serialize($categories);
            $this->setData('categories', $categories);
        }
        $pageStoreIds = $this->getData('page_store_id');
        if (is_array($pageStoreIds)) {
            $pageStoreIds = $this->serializer->serialize($pageStoreIds);
            $this->setData('page_store_id', $pageStoreIds);
        }
        $block3Categories = $this->getData('block3_category');
        if (is_array($block3Categories)) {
            $block3Categories = $this->serializer->serialize($block3Categories);
            $this->setData('block3_category', $block3Categories);
        }
        return parent::beforeSave();
    }

    public function getPage($storeId = null)
    {
        if (!$this->cityPage) {
            if (!$storeId) {
                $storeId = $this->storeManager->getStore()->getId();
            }
            $pageCollection = $this->cityPageCollectionFactory->create();
            $pageCollection->addFieldToFilter('city_id', ['eq' => $this->getId()]);
            $pageCollection->addFieldToFilter('store_id', ['eq' => $storeId]);
            if ($pageCollection->getSize()) {
                $this->cityPage = $pageCollection->getFirstItem();
            }
        }
        return $this->cityPage;
    }
    
    public function getState()
    {
        $collection = $this->statesCollectionFactory->create();
        $collection->addFieldToSelect(['state_code', 'id']);
        if (is_numeric($this->getRegion())) {
            $collection->addFieldToFilter('magento_region_id', ['eq' => $this->getRegion()]);
        } else {
            $collection->addFieldToFilter('state_name', ['like' => $this->getRegion()]);
        }
        $collection->addFieldToFilter('country_code', ['eq' => $this->getCountryId()]);
        if ($collection->getSize()) {
            return $collection->getFirstItem();
        }
        return null;
    }

    public function getCountryName()
    {
        return $this->cityLocation->getCountryName($this->getData('country_id'));
    }
    
    public function getRegionName()
    {
        return $this->cityLocation->getRegionName($this->getData('region'));
    }

    public function clearCityPageData()
    {
        $this->cityPage = null;
        return $this;
    }
}
