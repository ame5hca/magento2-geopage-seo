<?php

namespace GiftGroup\GeoPage\Model\ResourceModel\States;

use GiftGroup\GeoPage\Model\ResourceModel\States as StatesResource;
use GiftGroup\GeoPage\Model\States as StatesModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * States collection class
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
    protected $_eventPrefix = 'city_states_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'city_states_collection_obj';


    /**
     * Construct function
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(StatesModel::class, StatesResource::class);
    }
}
