<?php

namespace GiftGroup\GeoPage\Model\DataProvider\CityPage;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

class Products
{
    private const DEFAULT_LIMIT = 15;

    private $productCollectionFactory;

    public function __construct(
        CollectionFactory $productCollectionFactory
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
    }

    public function getItems($categoryId = [], $limit = self::DEFAULT_LIMIT)
    {
        /** @var Collection $collection */
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        if (!is_array($categoryId)) {
            $categoryId = [$categoryId];
        }
        if (count($categoryId)) {
            $collection->addCategoriesFilter(['in' => $categoryId]);
        }
        $collection->addAttributeToFilter('visibility', Visibility::VISIBILITY_BOTH);
        $collection->addAttributeToFilter('status',Status::STATUS_ENABLED);
        $collection->addAttributeToFilter('price', ['gt' => 0]);
        $collection->addFinalPrice();
        if ($limit) {
            $collection->setPageSize($limit);
            $collection->setCurPage(1);
        }

        return $collection;
    }
}
