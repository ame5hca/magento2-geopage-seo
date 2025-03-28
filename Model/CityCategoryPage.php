<?php

namespace GiftGroup\GeoPage\Model;

use Magento\Framework\Model\AbstractModel;
use GiftGroup\GeoPage\Model\ResourceModel\CityCategoryPage as CityCategoryPageResource;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use GiftGroup\GeoPage\Model\ResourceModel\City\CollectionFactory as CityCollectionFactory;
use GiftGroup\GeoPage\Model\ResourceModel\States\CollectionFactory as StateCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category as CategoryResourceModel;
use GiftGroup\GeoPage\Model\Config;
use GiftGroup\GeoPage\Model\ResourceModel\CityCategoryPage\CollectionFactory as CategoryPageCollectionFactory;

/**
 * CityCategoryPage model class
 */
class CityCategoryPage extends AbstractModel
{
    /**
     * Cache tag
     */
    public const CACHE_TAG = 'geopage_city_category_page';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @var string
     */
    protected $_eventPrefix = self::CACHE_TAG;

    private $serializer;

    protected $categoriesCollection = null;

    private $categoryCollectionFactory;

    private $storeManager;

    private $cityCollectionFactory;

    private $stateCollectionFactory;

    private $categoryResourceModel;

    private $collectionFactory;

    private $config;

    public function __construct(
        Context $context,
        Registry $registry,
        SerializerInterface $serializer,
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        CityCollectionFactory $cityCollectionFactory,
        StateCollectionFactory $stateCollectionFactory,
        CategoryResourceModel $categoryResourceModel,
        CategoryPageCollectionFactory $collectionFactory,
        Config $config
    ) {
        parent::__construct($context, $registry);
        $this->serializer = $serializer;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
        $this->cityCollectionFactory = $cityCollectionFactory;
        $this->stateCollectionFactory = $stateCollectionFactory;
        $this->categoryResourceModel = $categoryResourceModel;
        $this->collectionFactory = $collectionFactory;
        $this->config = $config;
    }

    /**
     * Construct function
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CityCategoryPageResource::class);
    }
    
    public function getProductIds()
    {
        $productIds = $this->getData('product_ids');
        if (is_array($productIds)) {
            return $productIds;
        }
        if (is_null($productIds)) {
            return [];
        }
        return $this->serializer->unserialize($productIds);
    }

    protected function _afterLoad()
    {        
        $this->setData('product_ids', $this->getProductIds());
        $this->setData('block3_category', $this->getBlock3Category());
        return parent::_afterLoad();
    }

    public function beforeSave()
    {
        $productIds = $this->getData('product_ids');
        if (is_array($productIds)) {
            $productIds = $this->serializer->serialize($productIds);
            $this->setData('product_ids', $productIds);
        }
        $block3Categories = $this->getData('block3_category');
        if (is_array($block3Categories)) {
            $block3Categories = $this->serializer->serialize($block3Categories);
            $this->setData('block3_category', $block3Categories);
        }
        return parent::beforeSave();
    }

    public function getCityCode()
    {
        $cityCode = $this->getData('city_code');       
        return $cityCode;
    }
    
    public function setCityCode($code)
    {        
        return $this->setData('city_code', $code);
    }
    
    public function getStateCode()
    {
        $stateCode = $this->getData('state_code');       
        return $stateCode;
    }
    
    public function setStateCode($code)
    {
        return $this->setData('state_code', $code);
    }

    public function getCategoryUrlKey()
    {
        $urlKey = $this->getData('category_url_key');
        if (!$urlKey) {
            $urlKey = $this->categoryResourceModel->getAttributeRawValue(
                $this->getData('category_id'),
                'url_key',
                $this->getData('store_id')
            );
            $urlKey = $this->getFinalUrlKey($urlKey);
            //$urlKey = $this->getCategoryUrlKeyFromCategoryId($this->getData('category_id'));
            $this->setCategoryUrlKey($urlKey);
        }
        return $urlKey;
    }
    
    public function setCategoryUrlKey($urlKey)
    {        
        return $this->setData('category_url_key', $urlKey);
    }
    
    public function getCategoryName()
    {
        $catName = $this->getData('category_name');
        if (!$catName) {
            $catName = $this->categoryResourceModel->getAttributeRawValue(
                $this->getData('category_id'),
                'name',
                $this->getData('store_id')
            );
            $catName = strtolower($catName);
            $catName = $this->checkForMapping($catName);
            $catName = str_replace('gift baskets', '', $catName);
            $catName = str_replace('gift basket', '', $catName);
            $catName = str_replace('baskets', '', $catName);
            $catName = str_replace('gifts', '', $catName);
            //$catName = ucwords($catName);
            
            $this->setCategoryName($catName);
        }
        return $catName;
    }
    
    public function setCategoryName($name)
    {        
        return $this->setData('category_name', $name);
    }

    public function getUrl()
    {
        return sprintf(
            Config::CITY_CATEGORY_PAGE_URL,
            $this->getData('state_code'),
            $this->getData('city_code'),
            $this->getCategoryUrlKey()
        );
    }
    
    private function getCityCodeFromCityId($cityId)
    {
        $collection = $this->cityCollectionFactory->create();
        $collection->addFieldToSelect('code');
        $collection->addFieldToFilter('id', ['eq' => $cityId]);
        if ($collection->getSize()) {
            return $collection->getFirstItem()->getCode();
        }
        return null;
    }
    
    private function getStateCodeFromStateId($stateId)
    {
        $collection = $this->stateCollectionFactory->create();
        $collection->addFieldToSelect('state_code');
        $collection->addFieldToFilter('id', ['eq' => $stateId]);
        if ($collection->getSize()) {
            return $collection->getFirstItem()->getStateCode();
        }
        return null;
    }
    
    private function getCategoryUrlKeyFromCategoryId($categoryId)
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addFieldToSelect('url_key');
        $collection->addFieldToFilter('entity_id', ['eq' => $categoryId]);
        if ($collection->getSize()) {
            return $collection->getFirstItem()->getUrlKey();
        }
        return null;
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

    public function getRelatedCategoryPages($limit = null, $storeId = null)
    {
        if (!$this->categoriesCollection) {
            $collection = $this->collectionFactory->create();
            $collection->selectMandatoryFields();
            $collection->addFieldToFilter('city_id', ['eq' => $this->getData('city_id')]);
            $collection->addFieldToFilter('category_id', ['neq' => $this->getData('category_id')]);
            $collection->addFieldToFilter('is_active', ['eq' => 1]);
            if (!$storeId) {
                $storeId = $this->storeManager->getStore()->getId();
                $collection->addFieldToFilter('store_id', ['eq' => $storeId]);
            }
            $collection->addCityCodeToResult()->addStateCodeToResult();
            if ($limit != null) {
                $collection->setPageSize($limit);
                $collection->setCurPage(1);
            }
            if ($collection->getSize()) {
                $this->categoriesCollection = $collection;
            }
        }
        return $this->categoriesCollection;
    }
    
    public function getFinalUrlKey($urlKey)
    {
        $mappingUrlKeys = $this->config->getCategoryUrlKeyMapping();
        if ($mappingUrlKeys && count($mappingUrlKeys)) {
            foreach ($mappingUrlKeys as $mapping) {
                if (count($mapping) != 2) {
                    continue;
                }
                if($mapping[0] == $urlKey) {
                    return $mapping[1];
                }
            }
        }
        return $urlKey;
    }
    
    public function checkForMapping($catName)
    {
        $mapping = $this->config->getCategoryNameMapping();
        if ($mapping && count($mapping)) {
            foreach ($mapping as $name) {
                if (count($name) != 2) {
                    continue;
                }
                $currentName = strtolower($name[0]);
                if($currentName == $catName) {
                    return strtolower($name[1]);;
                }
            }
        }
        return $catName;
    }
    
}
