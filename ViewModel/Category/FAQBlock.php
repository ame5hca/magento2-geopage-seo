<?php

namespace GiftGroup\GeoPage\ViewModel\Category;

class FAQBlock extends PopularProducts
{
    public function showFaqBlock()
    {
        return $this->categoryPageDataProvider->showFaqBlock();
    }
    
    public function getPopularProducts()
    {
        $popularProductsCollection = $this->popularProductsProvider->getBestSellers(
            $this->getCategoryId()
        );
        if (!$popularProductsCollection || !$popularProductsCollection->getSize()) {
            $popularProductsCollection = $this->popularProductsProvider->getRandomProducts(
                $this->getCategoryId()
            );
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
        return $this->getCategory()->getCategoryName();
    }

    public function getFaqAns1()
    {
        return $this->categoryPageDataProvider->getFaqAns1();
    }
    
    public function getFaqAns2()
    {
        return $this->categoryPageDataProvider->getFaqAns2();
    }
    
    public function getFaqAns3()
    {
        return $this->categoryPageDataProvider->getFaqAns3();
    }

    public function getFaqQn1()
    {
        return $this->categoryPageDataProvider->getFaqQn1();
    }
    
    public function getFaqQn2()
    {
        return $this->categoryPageDataProvider->getFaqQn2();
    }
    
    public function getFaqQn3()
    {
        return $this->categoryPageDataProvider->getFaqQn3();
    }
}