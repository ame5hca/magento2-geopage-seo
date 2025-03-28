<?php

namespace GiftGroup\GeoPage\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use GiftGroup\GeoPage\Model\ResourceModel\States\CollectionFactory;

class States implements OptionSourceInterface
{
    private $stateCollectionFactory;

    public function __construct(
        CollectionFactory $stateCollectionFactory
    ) {
        $this->stateCollectionFactory = $stateCollectionFactory;
    }

    public function toOptionArray()
    {
        $options = [];
        $collection = $this->stateCollectionFactory->create();
        $collection->addFieldToSelect(['id', 'state_name']);
        if ($collection->getSize()) {
            foreach ($collection as $state) {
                $options[] = ['label' => $state->getData('state_name'), 'value' => $state->getData('id')];
            }
        }
        return $options;
    }
}