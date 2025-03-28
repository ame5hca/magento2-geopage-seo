<?php

namespace GiftGroup\GeoPage\Model\ResourceModel\CityCategoryPage;

use GiftGroup\GeoPage\Model\ResourceModel\CityCategoryPage as CityCategoryPageResource;
use GiftGroup\GeoPage\Model\CityCategoryPage as CityCategoryPageModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use GiftGroup\GeoPage\Model\ResourceModel\City as CityResource;
use GiftGroup\GeoPage\Model\ResourceModel\States as StateResource;

/**
 * CityCategoryPage collection class
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
    protected $_eventPrefix = 'city_category_page_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'city_category_page_collection_obj';


    /**
     * Construct function
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CityCategoryPageModel::class, CityCategoryPageResource::class);
        $this->_map['fields']['is_active'] = 'main_table.is_active';
        $this->_map['fields']['store_id'] = 'main_table.store_id';
    }

    public function addCityCodeToResult()
    {
        $this->getSelect()->joinLeft(
            ['city' => $this->getTable(CityResource::TABLE)],
            'main_table.city_id = city.id',
            ['city_code' => 'code']
        );

        return $this;        
    }
    
    public function addStateCodeToResult()
    {
        $this->getSelect()->joinLeft(
            ['state' => $this->getTable(StateResource::TABLE)],
            'main_table.state_id = state.id',
            ['state_code']
        );
        
        return $this;        
    }
    
    public function selectMandatoryFields()
    {
        $this->addFieldToSelect(['id', 'category_id','store_id','city_id', 'state_id']);   
        return $this;
    }
}
