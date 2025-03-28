<?php

namespace GiftGroup\GeoPage\Model\Page;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use GiftGroup\GeoPage\Model\Config;
use Magento\Catalog\Model\CategoryRepository;

class ProductsGenerator
{
    private $childCategories = [];

    private $loadedCategoryId = null;

    protected $productCollectionFactory;

    protected $categoryRepository;

    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        CategoryRepository $categoryRepository
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryRepository = $categoryRepository;
    }

    public function generate($storeId, $limit, $categoryId = null)
    {
        $productIds = [];
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('entity_id');
        $collection->addAttributeToFilter('status', ['eq' => 1]);
        $collection->addAttributeToFilter('price', ['gt' => 0]);
        $collection->setStoreId($storeId);
        $collection->addStoreFilter();
        if ($categoryId) {
            $categoryIds = $this->getCategoryIdsInclChildren($categoryId);
            $collection->addCategoriesFilter(
                ['in' => $categoryIds]
            );
        }
        $collection->getSelect()->limit(
            ($limit == null ? Config::CITY_PAGE_DEFAULT_PRODUCT_LIST_LIMIT : $limit)
        )->orderRand();
        if ($collection->getSize()) {
            foreach ($collection as $product) {
                $productIds[] = $product->getId();
            }
        }
        return $productIds;
    }
    
    private function getCategoryIdsInclChildren($categoryId)
    {
        if (!$this->loadedCategoryId || $this->loadedCategoryId != $categoryId) {
            $this->childCategories = [];
            $this->childCategories[] = $categoryId;
            try {
                $category = $this->categoryRepository->get($categoryId);
                foreach ($category->getChildrenCategories() as $childCategory) {
                    $this->childCategories[] = $childCategory->getId(); 
                }
                $this->loadedCategoryId = $categoryId;                 
            } catch (\Exception $e) {
                return $this->childCategories;
            }
        }
        return $this->childCategories;      
    }
}
