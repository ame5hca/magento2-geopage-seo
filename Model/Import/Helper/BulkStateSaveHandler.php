<?php

namespace GiftGroup\GeoPage\Model\Import\Helper;

use GiftGroup\GeoPage\Model\State\DataManager;
use GiftGroup\GeoPage\Model\ResourceModel\States\CollectionFactory as StateCollectionFactory;
use Magento\Framework\App\ResourceConnection;
use GiftGroup\GeoPage\Model\ResourceModel\States as StateResource;

class BulkStateSaveHandler
{
    private $stateDataManager;

    private $stateCollectionFactory;

    private $connection;

    public function __construct(
        DataManager $stateDataManager,
        StateCollectionFactory $stateCollectionFactory,
        ResourceConnection $resource
    ) {
        $this->stateDataManager = $stateDataManager;
        $this->stateCollectionFactory = $stateCollectionFactory;
        $this->connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
    }

    public function save($states)
    {
        $existingStates = $this->getExistingStates();
        foreach ($states as $key => $value) {
            if (isset($existingStates[$key])) {
                unset($states[$key]);
                continue;
            }
            $states[$key]['state_code'] = $this->stateDataManager->getStateCode($value['state_name']);
        }
        return $this->saveStates($states);

    }

    private function saveStates($states) 
    {
        if (!count($states)) {
            return true;
        }
        $this->connection->insertOnDuplicate(StateResource::TABLE, $states, $this->getAvailableColumns());

        return true;
    }

    public function getExistingStates()
    {
        $existingStates = [];
        $collection = $this->stateCollectionFactory->create();
        $collection->addFieldToSelect(['state_name', 'country_code']);
        if ($collection->getSize()) {
            foreach ($collection as $state) {
                $index = $state['state_name'] . "|" . $state['country_code'];
                $existingStates[$index] = $state['state_name'];
            }
        }
        
        return $existingStates;
    }

    private function getAvailableColumns()
    {
        return ['state_name','state_code','country_code','magento_region_id'];
    }
}