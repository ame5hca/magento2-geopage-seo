<?php

namespace GiftGroup\GeoPage\Model\ResourceModel\StatePage;

use GiftGroup\GeoPage\Model\ResourceModel\StatePage as StatePageResource;
use GiftGroup\GeoPage\Model\StatePage as StatePageModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use GiftGroup\GeoPage\Model\ResourceModel\States as StateResource;

/**
 * StatePage collection class
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
    protected $_eventPrefix = 'state_page_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'state_page_collection_obj';


    /**
     * Construct function
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(StatePageModel::class, StatePageResource::class);
        $this->_map['fields']['is_active'] = 'main_table.is_active';
        $this->_map['fields']['store_id'] = 'main_table.store_id';
    }
    
    public function addStateCodeToResult()
    {
        $this->getSelect()->joinLeft(
            ['state' => $this->getTable(StateResource::TABLE)],
            'main_table.state_id = state.id',
            ['state_code', 'state_name']
        );
        
        return $this;        
    }
    
    public function addCountryFilter($countryCode)
    {
        $this->getSelect()->joinLeft(
            ['state' => $this->getTable(StateResource::TABLE)],
            'main_table.state_id = state.id',
            ['state_code', 'state_name']
        )->where('state.country_code = ?', $countryCode);
        
        return $this;        
    }
    
    public function selectMandatoryFields()
    {
        $this->addFieldToSelect(['id', 'store_id', 'state_id']);   
        return $this;
    }
}
