<?php

namespace GiftGroup\GeoPage\ViewModel\Category;

use GiftGroup\GeoPage\ViewModel\CategoryPage;
use GiftGroup\GeoPage\Model\DataProvider\CategoryPageView as CategoryPageDataProvider;
use GiftGroup\GeoPage\Model\DataProvider\CityPage\PopularProducts as PopularProductsDataProvider;
use Magento\Catalog\Block\Product\ImageFactory;

class PopularProducts extends CategoryPage
{
    protected $popularProductsProvider;

    private $imageFactory;

    public function __construct(
        CategoryPageDataProvider $categoryPageDataProvider,
        PopularProductsDataProvider $popularProductsProvider,
        ImageFactory $imageFactory
    ) {
        parent::__construct($categoryPageDataProvider);
        $this->popularProductsProvider = $popularProductsProvider;
        $this->imageFactory = $imageFactory;
    }

    public function getPopularProductsBlockTitle()
    {
        return __(
            'Popular %1 Gift Baskets in %2',
            $this->getCategory()->getName(),
            $this->getCity()->getName()
        );
    }

    public function showPopularProductsBlock()
    {
        return $this->categoryPageDataProvider->showPopularProductsBlock();
    }
    
    public function getPopularProductLimit()
    {
        return $this->categoryPageDataProvider->getPopularProductLimit();
    }

    public function getProducts()
    {
        return $this->popularProductsProvider->getList(
            $this->getCategoryId(), 
            $this->getPopularProductLimit()
        );
    }
    
    public function getCityName()
    {        
        return $this->getCity()->getName();
    }

    public function getProductImage($product)
    {
        return $this->imageFactory->create($product, 'product_thumbnail_image', []);
    }
}
