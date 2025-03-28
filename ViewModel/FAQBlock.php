<?php

namespace GiftGroup\GeoPage\ViewModel;

class FAQBlock extends PopularProducts
{
    public function showFaqBlock()
    {
        return $this->cityPageDataProvider->showFaqBlock();
    }
    
    public function getPopularProducts()
    {
        $popularProductsCollection = $this->popularProductsProvider->getBestSellers();
        $popularProductsCollection = false;
        if (!$popularProductsCollection || !$popularProductsCollection->getSize()) {
            $popularProductsCollection = $this->popularProductsProvider->getRandomProducts();
        }
        return $popularProductsCollection;
    }

    public function getCityName()
    {
        return $this->getCity()->getName();
    }
    
    public function getRegionName()
    {
        return $this->getCity()->getRegionName();
    }
    
    public function getCountryName()
    {
        return $this->getCity()->getCountryName();
    }
    
    public function getCategoryName()
    {
        $category = $this->getCategory();
        return $category != null ? $category->getName() : '';
    }
    
    public function getFaqAns1()
    {
        return $this->cityPageDataProvider->getFaqAns1();
    }
    
    public function getFaqAns2()
    {
        return $this->cityPageDataProvider->getFaqAns2();
    }
    
    public function getFaqAns3()
    {
        return $this->cityPageDataProvider->getFaqAns3();
    }
    
    public function getFaqQn1()
    {
        return $this->cityPageDataProvider->getFaqQn1();
    }
    
    public function getFaqQn2()
    {
        return $this->cityPageDataProvider->getFaqQn2();
    }
    
    public function getFaqQn3()
    {
        return $this->cityPageDataProvider->getFaqQn3();
    }
}