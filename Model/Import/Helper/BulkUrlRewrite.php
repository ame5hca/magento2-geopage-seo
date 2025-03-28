<?php

namespace GiftGroup\GeoPage\Model\Import\Helper;

use GiftGroup\GeoPage\Model\ResourceModel\City\CollectionFactory;
use GiftGroup\GeoPage\Model\UrlRewriteManager;

class BulkUrlRewrite
{
    private $cityCollectionFactory;

    private $urlRewriteManager;

    public function __construct(
        CollectionFactory $cityCollectionFactory,
        UrlRewriteManager $urlRewriteManager
    ) {
        $this->cityCollectionFactory = $cityCollectionFactory;
        $this->urlRewriteManager = $urlRewriteManager;
    }

    public function addRewrites($cityCodes)
    {
        $collection = $this->cityCollectionFactory->create();
        $collection->addFieldToSelect(['id', 'code', 'store_id']);
        $collection->addFieldToFilter('code', ['in' => $cityCodes]);
        if ($collection->getSize()) {
            foreach ($collection as $city) {
                $this->urlRewriteManager->addCityUrlRewrite($city, $city->getStoreId());
            }
        }
    }
}