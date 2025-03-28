<?php

namespace GiftGroup\GeoPage\ViewModel;

use GiftGroup\GeoPage\Model\DataProvider\CityPageView as CityPageViewDataProvider;
use GiftGroup\GeoPage\ViewModel\CityPage;
use GiftGroup\GeoPage\Model\DataProvider\CityPage\Products;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

class Block3 extends CityPage
{
    protected $productsDataProvider;

    protected $imageFactory;

    protected $priceHelper;

    public function __construct(
        CityPageViewDataProvider $cityPageDataProvider,
        Products $productsDataProvider,
        ImageFactory $imageFactory,
        PriceHelper $priceHelper
    ) {
        parent::__construct($cityPageDataProvider);
        $this->productsDataProvider = $productsDataProvider;
        $this->imageFactory = $imageFactory;
        $this->priceHelper = $priceHelper;
    }

    public function showBlock()
    {
        return $this->cityPageDataProvider->showBlock3();
    }
    
    public function getTitle()
    {
        return $this->cityPageDataProvider->getBlock3Title();
    }
    
    public function getProducts()
    {
        $category = $this->cityPageDataProvider->getBlock3Category();
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
