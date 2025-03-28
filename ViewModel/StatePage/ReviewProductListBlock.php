<?php

namespace GiftGroup\GeoPage\ViewModel\StatePage;

use GiftGroup\GeoPage\Model\DataProvider\CityPage\ProductReviews;
use GiftGroup\GeoPage\Model\DataProvider\StatePageView as StatePageViewDataProvider;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use GiftGroup\GeoPage\ViewModel\StatePage;

class ReviewProductListBlock extends StatePage
{
    private $productReviews;

    private $imageFactory;

    private $productRepository;

    public function __construct(
        StatePageViewDataProvider $statePageDataProvider,
        ProductReviews $productReviews,
        ImageFactory $imageFactory,
        ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($statePageDataProvider);
        $this->productReviews = $productReviews;
        $this->imageFactory = $imageFactory;
        $this->productRepository = $productRepository;
    }

    public function showReviewProductsBlock()
    {
        return $this->statePageDataProvider->showReviewProductsBlock();
    }
    
    public function getReviewProductLimit()
    {
        return $this->statePageDataProvider->getReviewProductLimit();
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