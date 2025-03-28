<?php

namespace GiftGroup\GeoPage\ViewModel\StatePage;

use GiftGroup\GeoPage\Model\DataProvider\StatePageView as StatePageViewDataProvider;
use GiftGroup\GeoPage\ViewModel\StatePage;
use GiftGroup\GeoPage\Model\DataProvider\CityPage\Products;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

class Block3 extends StatePage
{
    protected $productsDataProvider;

    protected $imageFactory;

    protected $priceHelper;

    public function __construct(
        StatePageViewDataProvider $statePageDataProvider,
        Products $productsDataProvider,
        ImageFactory $imageFactory,
        PriceHelper $priceHelper
    ) {
        parent::__construct($statePageDataProvider);
        $this->productsDataProvider = $productsDataProvider;
        $this->imageFactory = $imageFactory;
        $this->priceHelper = $priceHelper;
    }

    public function showBlock()
    {
        return $this->statePageDataProvider->showBlock3();
    }
    
    public function getTitle()
    {
        return $this->statePageDataProvider->getBlock3Title();
    }
    
    public function getProducts()
    {
        $category = $this->statePageDataProvider->getBlock3Category();
        return $this->productsDataProvider->getItems($category);
    }

    public function getProductImage($product)
    {
        return $this->imageFactory->create($product, 'category_page_grid', []);
    }
    
    public function getPrice($product)
    {
        return $this->priceHelper->currency($product->getFinalPrice(), true, false);
    }
}
