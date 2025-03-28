<?php

namespace GiftGroup\GeoPage\Model\ResourceModel\CityPage;

use GiftGroup\GeoPage\Model\ResourceModel\CityPage as CityPageResource;
use GiftGroup\GeoPage\Model\CityPage as CityPageModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use GiftGroup\GeoPage\Model\ResourceModel\City as CityResource;
use GiftGroup\GeoPage\Model\ResourceModel\States as StateResource;

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
    protected $_eventPrefix = 'city_page_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'city_page_collection_obj';


    /**
     * Construct function
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CityPageModel::class, CityPageResource::class);
        $this->_map['fields']['is_active'] = 'main_table.is_active';
        $this->_map['fields']['store_id'] = 'main_table.store_id';
        $this->_map['fields']['id'] = 'main_table.id';
    }
   
    public function addCityCodeToResult()
    {
        $this->getSelect()->joinLeft(
            ['city' => $this->getTable(CityResource::TABLE)],
            'main_table.city_id = city.id',
            ['city_code' => 'code', 'city_name' => 'name']
        );

        return $this;        
    }
    
    public function addStateCodeToResult()
    {
        $this->getSelect()->joinLeft(
            ['state' => $this->getTable(StateResource::TABLE)],
            'main_table.state_id = state.id',
            ['state_code','state_name']
        );
        
        return $this;        
    }
}
