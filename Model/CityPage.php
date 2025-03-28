<?php

namespace GiftGroup\GeoPage\Model;

use Magento\Framework\Model\AbstractModel;
use GiftGroup\GeoPage\Model\ResourceModel\CityPage as CityPageResource;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use GiftGroup\GeoPage\Model\ResourceModel\States\CollectionFactory as StatesCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use GiftGroup\GeoPage\Model\Config;
use GiftGroup\GeoPage\Model\ResourceModel\CityCategoryPage\CollectionFactory as CategoryPageCollectionFactory;

/**
 * City model class
 */
class CityPage extends AbstractModel
{
    /**
     * Cache tag
     */
    public const CACHE_TAG = 'geopage_city_page';

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

    private $categoryPageCollectionFactory;

    private $storeManager;

    private $statesCollectionFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        SerializerInterface $serializer,
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        StatesCollectionFactory $statesCollectionFactory,
        CategoryPageCollectionFactory $categoryPageCollectionFactory
    ) {
        parent::__construct($context, $registry);
        $this->serializer = $serializer;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
        $this->statesCollectionFactory = $statesCollectionFactory;
        $this->categoryPageCollectionFactory = $categoryPageCollectionFactory;
    }

    /**
     * Construct function
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CityPageResource::class);
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
        $this->setData('categories', $this->getCategories());
        $this->setData('product_ids', $this->getProductIds());
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

    public function getCategoryCollection($limit = null, $storeId = null)
    {
        if (!$this->categoriesCollection) {
            $collection = $this->categoryPageCollectionFactory->create();
            $collection->selectMandatoryFields();
            $collection->addFieldToFilter('city_id', ['eq' => $this->getData('city_id')]);
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
            /* $collection = $this->categoryCollectionFactory->create();
            $collection->addAttributeToSelect('url_key');
            $collection->getSelect()->joinRight(
                ['city_cat' => $collection->getTable('giftgroup_city_category_page')],
                'e.entity_id = city_cat.category_id',
                ['city_id', 'state_id']
            );
            $collection->getSelect()->columns(
                [
                    'state_code' => new \Zend_Db_Expr(
                        '(SELECT state_code FROM giftgroup_states AS states WHERE city_cat.state_id = states.id)'
                    ),
                    'city_code' => new \Zend_Db_Expr(
                        '(SELECT city_code FROM giftgroup_cities AS cities WHERE city_cat.city_id = cities.id)'
                    )
                ]
            );
            $collection->getSelect()->where('city_cat.city_id = ?', $this->getData('city_id'));
            if (!$storeId) {
                $storeId = $this->storeManager->getStore()->getId();
                //$collection->setStoreId($storeId);
                $collection->getSelect()->where('city_cat.store_id = ?', $storeId);
            } */
            /* if ($limit != null) {
                $collection->setPageSize($limit);
                $collection->setCurPage(1);
            }
            if ($collection->getSize()) {
                $this->categoriesCollection = $collection;
            } */
            //$query = $collection->getSelect()->__toString();
        }
        return $this->categoriesCollection;
    }

    public function clearCategoryData()
    {
        $this->categoriesCollection = null;
        return $this;
    }

    public function getState()
    {
        $collection = $this->statesCollectionFactory->create();
        $collection->addFieldToSelect(['state_code', 'id']);
        $collection->addFieldToFilter('id', ['eq' => $this->getData('state_id')]);        
        if ($collection->getSize()) {
            return $collection->getFirstItem();
        }
        return null;
    }

    public function getCityCode()
    {
        return $this->getData('city_code');
    }
    
    public function setCityCode($code)
    {
        return $this->setData('city_code', $code);
    }
    
    public function getStateCode()
    {
        return $this->getData('state_code');
    }
    
    public function setStateCode($code)
    {
        return $this->setData('state_code', $code);
    }

    public function getUrl()
    {
        return sprintf(Config::CITY_PAGE_URL, $this->getData('state_code'), $this->getData('city_code'));
    }
}
