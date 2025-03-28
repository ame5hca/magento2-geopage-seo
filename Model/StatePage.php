<?php

namespace GiftGroup\GeoPage\Model;

use Magento\Framework\Model\AbstractModel;
use GiftGroup\GeoPage\Model\ResourceModel\StatePage as StatePageResource;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use GiftGroup\GeoPage\Model\ResourceModel\States\CollectionFactory as StateCollectionFactory;
use GiftGroup\GeoPage\Model\Config;
use Magento\Directory\Model\CountryFactory;

/**
 * StatePage model class
 */
class StatePage extends AbstractModel
{
    /**
     * Cache tag
     */
    public const CACHE_TAG = 'geopage_state_page';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @var string
     */
    protected $_eventPrefix = self::CACHE_TAG;

    private $serializer;

    private $state = null;

    private $stateCollectionFactory;

    private $countryFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        SerializerInterface $serializer,
        StateCollectionFactory $stateCollectionFactory,
        CountryFactory $countryFactory
    ) {
        parent::__construct($context, $registry);
        $this->serializer = $serializer;
        $this->stateCollectionFactory = $stateCollectionFactory;
        $this->countryFactory = $countryFactory;
    }

    /**
     * Construct function
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(StatePageResource::class);
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

    protected function _afterLoad()
    {
        $this->setData('product_ids', $this->getProductIds());
        $this->setData('block3_category', $this->getBlock3Category());
        $stateData = $this->getStateDataFromStateId($this->getData('state_id'));
        if ($stateData) {
            $this->setData('state_code', $stateData->getData('state_code'));
            $this->setData('country_code', $stateData->getData('country_code'));
            $this->setData('state_name', $stateData->getData('state_name'));
        }
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

    public function getStateCode()
    {
        $stateCode = $this->getData('state_code');
        if (!$stateCode) {
            $state = $this->getStateDataFromStateId($this->getData('state_id'));
            $stateCode = $state->getData('state_code');
            $this->setStateCode($stateCode);
        }
        return $stateCode;
    }

    public function setStateCode($code)
    {
        return $this->setData('state_code', $code);
    }

    public function getCountryCode()
    {
        $countryCode = $this->getData('country_code');
        if (!$countryCode) {
            $state = $this->getStateDataFromStateId($this->getData('state_id'));
            $countryCode = $state->getData('country_code');
            $this->setCountryCode($countryCode);
        }
        return $countryCode;
    }

    public function setCountryCode($code)
    {
        return $this->setData('country_code', $code);
    }
    
    public function getStateName()
    {
        $stateName = $this->getData('state_name');
        if (!$stateName) {
            $state = $this->getStateDataFromStateId($this->getData('state_id'));
            $stateName = $state->getData('state_name');
            $this->setStateName($stateName);
        }
        return $stateName;
    }

    public function setStateName($name)
    {
        return $this->setData('state_name', $name);
    }

    public function getUrl()
    {
        return sprintf(
            Config::STATE_PAGE_URL,
            $this->getData('state_code')
        );
    }
    
    private function getStateDataFromStateId($stateId)
    {
        if (!$this->state || $this->state->getId() != $stateId) {
            $collection = $this->stateCollectionFactory->create();
            $collection->addFieldToSelect(['state_code', 'country_code', 'state_name']);
            $collection->addFieldToFilter('id', ['eq' => $stateId]);
            if ($collection->getSize()) {
                $this->state = $collection->getFirstItem();
            }
        }
        return $this->state;
    }

    public function getCountryName()
    {
        $countryCode = $this->getData('country_code');
        if ($countryCode) {
            $country = $this->countryFactory->create()->loadByCode($countryCode);
            return $country->getName();
        }
        return $countryCode;
    }
}
