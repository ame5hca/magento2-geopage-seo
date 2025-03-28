<?php

namespace GiftGroup\GeoPage\Model\ResourceModel\City;

use GiftGroup\GeoPage\Model\ResourceModel\City as CityResource;
use GiftGroup\GeoPage\Model\City as CityModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;

/**
 * City collection class
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * @var string
     */
    protected $_eventPrefix = 'city_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'city_collection_obj';


    /**
     * Construct function
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CityModel::class, CityResource::class);
    }
}
