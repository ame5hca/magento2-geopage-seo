<?php

namespace GiftGroup\GeoPage\Model;

use Magento\Framework\Model\AbstractModel;
use GiftGroup\GeoPage\Model\ResourceModel\States as StatesResource;

/**
 * States model class
 */
class States extends AbstractModel
{
    /**
     * Cache tag
     */
    public const CACHE_TAG = 'geopage_city_states';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @var string
     */
    protected $_eventPrefix = self::CACHE_TAG;

    /**
     * Construct function
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(StatesResource::class);
    }
    
}
