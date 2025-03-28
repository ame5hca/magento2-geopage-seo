<?php

declare(strict_types=1);

namespace GiftGroup\GeoPage\Block\CityPage;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use GiftGroup\GeoPage\Model\ResourceModel\CityPage\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class PageList extends Template
{
    protected $_template = "GiftGroup_GeoPage::city/homepage_list.phtml";

    private $cityPageCollectionFactory;

    private $storeManager;

    public function __construct(
        Context $context, 
        CollectionFactory $cityPageCollectionFactory,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->cityPageCollectionFactory = $cityPageCollectionFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    public function getList()
    {
        $stateId = $this->getData('state_id');
        $sameDayOnly = $this->getData('same_day');
        $nextDayOnly = $this->getData('next_day');
        $anyDayOnly = $this->getData('any_day');
        $sameOrNext = $this->getData('same_or_next');
        $collection = $this->cityPageCollectionFactory->create();
        $collection->addStateCodeToResult();
        $collection->addFieldToFilter('store_id', ['eq' => $this->storeManager->getStore()->getId()]);
        $collection->join(
            ['city' => $collection->getTable('giftgroup_cities')],
            'main_table.city_id = city.id',
            ['name', 'city_code' => 'code']
        );
        $wherCond = 'city.display_on_homepage = 1';
        $wherCond .= $stateId ? ' AND main_table.state_id = ?' : '';
        $wherCond .= $sameDayOnly ? ' AND main_table.same_day_delivery_block_status = 1' : '';
        $wherCond .= $nextDayOnly ? ' AND main_table.next_day_delivery_block_status = 1' : '';
        $wherCond .= $anyDayOnly ? ' AND main_table.any_day_delivery_block_status = 1' : '';
        $wherCond .= $sameOrNext ? ' AND (main_table.same_day_delivery_block_status = 1 OR main_table.next_day_delivery_block_status = 1)' : '';
        $collection->getSelect()->where($wherCond, $stateId);

        return $collection;
    }
}
