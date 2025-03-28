<?php

namespace GiftGroup\GeoPage\Model\DataProvider\CityPage;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use GiftGroup\TopProducts\Model\TopProducts;
use GiftGroup\GeoPage\Model\Config;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Reports\Model\ResourceModel\Product\CollectionFactory as MostViewedProductsCollectionFactory;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestSellerCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

/**
 * Class to provide the top products list.
 */
class PopularProducts extends TopProducts
{
    private const BEST_SELLERS_DEFAULT_LIMIT = 6;

    private const MOST_VIEWED_DEFAULT_LIMIT = 6;

    protected $productsDataProvider;

    public function __construct(
        BestSellerCollectionFactory         $bestSellerCollectionFactory,
        StoreManagerInterface               $storeManager,
        MostViewedProductsCollectionFactory $mostViewedProductCollectionFactory,
        ProductCollectionFactory            $productCollectionFactory,
        CategoryCollectionFactory           $categoryCollectionFactory,
        LoggerInterface $logger,
        Products $productsDataProvider
    ) {
        parent::__construct(
            $bestSellerCollectionFactory, 
            $storeManager, 
            $mostViewedProductCollectionFactory, 
            $productCollectionFactory, 
            $categoryCollectionFactory, 
            $logger
        );
        $this->productsDataProvider = $productsDataProvider;
    }

    /**
     * Get the top products list.
     *
     * Logic of getting the top products is, if there is best-seller products available in the category
     * then that products will be listed in front-end and if there is no best-seller products/count is less than
     * the limit, then the rest of the count of items are taken from the most viewed products list.
     *
     * @param null|int|string $categoryId
     * @param int $limit
     * @return Collection|null
     */
    public function getList($categoryId = null, $limit = Config::CITY_PAGE_DEFAULT_POPULAR_PRODUCT_LIMIT): ?Collection
    {
        try {
            $bestSellerProductIds = $this->getBestSellingProducts($categoryId, 40);
            $mostViewedProductsIds = $this->getMostViewedProducts(40);
            $topProductIds = array_merge($bestSellerProductIds, $mostViewedProductsIds);
            return $this->getFinalProducts($topProductIds, $categoryId, $limit);
        } catch (\Exception $ex) {
            $this->logger->info('PopularProductError : ' . $ex->getMessage());
            return null;
        }
    }

    public function getBestSellers($categoryId = null, $limit = self::BEST_SELLERS_DEFAULT_LIMIT)
    {
        $bestSellerProductIds = $this->getBestSellingProducts($categoryId, 20);
        return $this->getFinalProducts($bestSellerProductIds, $categoryId, $limit);
    }
    
    public function getRandomProducts($categoryId = null, $limit = self::BEST_SELLERS_DEFAULT_LIMIT)
    {
        $categoryId = $categoryId ? [$categoryId] : [];
        $collection = $this->productsDataProvider->getItems($categoryId, $limit);
        if ($collection->getSize()) {
            $collection->getSelect()->orderRand();
        }
        return $collection;
    }
    
    public function getMostViewed($categoryId = null, $limit = self::MOST_VIEWED_DEFAULT_LIMIT)
    {
        $mostViewedProductsIds = $this->getMostViewedProducts(60);
        return $this->getFinalProducts($mostViewedProductsIds, $categoryId, $limit);
    }

    /**
     * Get the final products objects
     *
     * @param mixed[] $productIds
     * @param null|int|string $categoryId
     * @return Collection
     */
    protected function getFinalProducts($productIds, $categoryId, $limit): Collection
    {
        $collection = parent::getProducts($productIds, $categoryId);
        $collection->setPageSize($limit);
        $collection->setCurPage(1);
        $collection->getSelect()->orderRand();

        return $collection;
    }
}
