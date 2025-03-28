<?php

namespace GiftGroup\GeoPage\ViewModel\StatePage;

class FAQBlock extends PopularProducts
{
    public function showFaqBlock()
    {
        return $this->statePageDataProvider->showFaqBlock();
    }
    
    public function getPopularProducts()
    {
        $popularProductsCollection = $this->popularProductsProvider->getBestSellers();
        if (!$popularProductsCollection || !$popularProductsCollection->getSize()) {
            $popularProductsCollection = $this->popularProductsProvider->getRandomProducts();
        }
        return $popularProductsCollection;
    }
    
    public function getRegionName()
    {
        return $this->getStateName();
    }
    
    public function getCountryName()
    {
        return $this->getStatePage()->getCountryName();
    }

    public function getFaqAns1()
    {
        return $this->statePageDataProvider->getFaqAns1();
    }
    
    public function getFaqAns2()
    {
        return $this->statePageDataProvider->getFaqAns2();
    }
    
    public function getFaqAns3()
    {
        return $this->statePageDataProvider->getFaqAns3();
    }

    public function getFaqQn1()
    {
        return $this->statePageDataProvider->getFaqQn1();
    }
    
    public function getFaqQn2()
    {
        return $this->statePageDataProvider->getFaqQn2();
    }
    
    public function getFaqQn3()
    {
        return $this->statePageDataProvider->getFaqQn3();
    }
}