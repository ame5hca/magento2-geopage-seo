<?php

namespace GiftGroup\GeoPage\ViewModel\StatePage;

use GiftGroup\GeoPage\Model\DataProvider\CityPage\PopularProducts as PopularProductsDataProvider;
use GiftGroup\GeoPage\Model\DataProvider\StatePageView as StatePageViewDataProvider;
use Magento\Catalog\Block\Product\ImageFactory;
use GiftGroup\GeoPage\ViewModel\StatePage;

class PopularProducts extends StatePage
{
    protected $popularProductsProvider;

    private $imageFactory;

    public function __construct(
        StatePageViewDataProvider $statePageDataProvider,
        PopularProductsDataProvider $popularProductsProvider,
        ImageFactory $imageFactory
    ) {
        parent::__construct($statePageDataProvider);
        $this->popularProductsProvider = $popularProductsProvider;
        $this->imageFactory = $imageFactory;
    }

    public function showPopularProductsBlock()
    {
        return $this->statePageDataProvider->showPopularProductsBlock();
    }
    
    public function getPopularProductLimit()
    {
        return $this->statePageDataProvider->getPopularProductLimit();
    }

    public function getProducts()
    {
        return $this->popularProductsProvider->getList(
            null, 
            $this->getPopularProductLimit()
        );
    }

    public function getPopularProductsBlockTitle()
    {
        return __(
            'Popular Gift Baskets in %1', 
            $this->getStatePage()->getData('state_name')
        );
    }
    
    public function getStateName()
    {        
        return $this->getStatePage()->getData('state_name');
    }

    public function getProductImage($product)
    {
        return $this->imageFactory->create($product, 'product_thumbnail_image', []);
    }
}