<?php

namespace GiftGroup\GeoPage\ViewModel;

use GiftGroup\GeoPage\Model\DataProvider\CityPage\ProductReviews;
use GiftGroup\GeoPage\Model\DataProvider\CityPageView as CityPageDataProvider;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;

class ReviewProductListBlock extends CityPage
{
    private $productReviews;

    private $imageFactory;

    private $productRepository;

    public function __construct(
        CityPageDataProvider $cityPageDataProvider,
        ProductReviews $productReviews,
        ImageFactory $imageFactory,
        ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($cityPageDataProvider);
        $this->productReviews = $productReviews;
        $this->imageFactory = $imageFactory;
        $this->productRepository = $productRepository;
    }

    public function showReviewProductsBlock()
    {
        return $this->cityPageDataProvider->showReviewProductsBlock();
    }
    
    public function getReviewProductLimit()
    {
        return $this->cityPageDataProvider->getReviewProductLimit();
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