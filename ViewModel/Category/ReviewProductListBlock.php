<?php

namespace GiftGroup\GeoPage\ViewModel\Category;

use GiftGroup\GeoPage\Model\DataProvider\CityPage\ProductReviews;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use GiftGroup\GeoPage\ViewModel\CategoryPage;
use GiftGroup\GeoPage\Model\DataProvider\CategoryPageView as CategoryPageDataProvider;

class ReviewProductListBlock extends CategoryPage
{
    private $productReviews;

    private $imageFactory;

    private $productRepository;

    public function __construct(
        CategoryPageDataProvider $categoryPageDataProvider,
        ProductReviews $productReviews,
        ImageFactory $imageFactory,
        ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($categoryPageDataProvider);
        $this->productReviews = $productReviews;
        $this->imageFactory = $imageFactory;
        $this->productRepository = $productRepository;
    }

    public function showReviewProductsBlock()
    {
        return $this->categoryPageDataProvider->showReviewProductsBlock();
    }
    
    public function getReviewProductLimit()
    {
        return $this->categoryPageDataProvider->getReviewProductLimit();
    }

    public function getProductsReviews()
    {
        return $this->productReviews->getReviews($this->getReviewProductLimit());
    }
    
    public function getRatingSummary($reviewId)
    {
        return $this->productReviews->getRatingSummary($reviewId);
    }
    
    public function getProduct($productId)
    {
        try {
            return $this->productRepository->getById($productId);
        } catch (\Exception $e) {
            return null;
        }        
    }
    
    public function getProductImage($product)
    {
        return $this->imageFactory->create($product, 'category_page_grid', []);
    }
}