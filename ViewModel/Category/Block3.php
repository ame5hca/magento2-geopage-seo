<?php

namespace GiftGroup\GeoPage\ViewModel\Category;

use GiftGroup\GeoPage\Model\DataProvider\CategoryPageView as CategoryPageViewDataProvider;
use GiftGroup\GeoPage\ViewModel\CategoryPage;
use GiftGroup\GeoPage\Model\DataProvider\CityPage\Products;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

class Block3 extends CategoryPage
{
    protected $productsDataProvider;

    private $imageFactory;

    private $priceHelper;

    public function __construct(
        CategoryPageViewDataProvider $categoryPageDataProvider,
        Products $productsDataProvider,
        ImageFactory $imageFactory,
        PriceHelper $priceHelper
    ) {
        parent::__construct($categoryPageDataProvider);
        $this->productsDataProvider = $productsDataProvider;
        $this->imageFactory = $imageFactory;
        $this->priceHelper = $priceHelper;
    }

    public function showBlock()
    {
        return $this->categoryPageDataProvider->showBlock3();
    }
    
    public function getTitle()
    {
        return $this->categoryPageDataProvider->getBlock3Title();
    }
    
    public function getProducts()
    {
        $category = $this->categoryPageDataProvider->getBlock3Category();
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
