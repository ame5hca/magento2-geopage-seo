<?php

namespace GiftGroup\GeoPage\ViewModel;

use GiftGroup\GeoPage\Model\DataProvider\CityPage\PopularProducts as PopularProductsDataProvider;
use GiftGroup\GeoPage\Model\DataProvider\CityPageView as CityPageDataProvider;
use Magento\Catalog\Block\Product\ImageFactory;

class PopularProducts extends CityPage
{
    protected $popularProductsProvider;

    private $imageFactory;

    public function __construct(
        CityPageDataProvider $cityPageDataProvider,
        PopularProductsDataProvider $popularProductsProvider,
        ImageFactory $imageFactory
    ) {
        parent::__construct($cityPageDataProvider);
        $this->popularProductsProvider = $popularProductsProvider;
        $this->imageFactory = $imageFactory;
    }

    public function showPopularProductsBlock()
    {
        return $this->cityPageDataProvider->showPopularProductsBlock();
    }
    
    public function getPopularProductLimit()
    {
        return $this->cityPageDataProvider->getPopularProductLimit();
    }

    public function getProducts()
    {
        return $this->popularProductsProvider->getList(
            $this->getCategoryId(), 
            $this->getPopularProductLimit()
        );
    }

    public function getPopularProductsBlockTitle()
    {
        return __(
            'Popular Gift Baskets in %1', 
            $this->getCity()->getName()
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