<?php

namespace GiftGroup\GeoPage\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use GiftGroup\GeoPage\Model\DataProvider\CategoryPageView as CategoryPageDataProvider;

class CategoryPage implements ArgumentInterface
{
    protected $categoryPageDataProvider;

    public function __construct(
        CategoryPageDataProvider $categoryPageDataProvider
    ) {
        $this->categoryPageDataProvider = $categoryPageDataProvider;
    }

    public function getCity()
    {
        return $this->categoryPageDataProvider->getCity();
    }

    public function getCategory()
    {
        return $this->categoryPageDataProvider->getCategoryPage();
    }

    public function getCategoryId()
    {
        $category = $this->getCategory();
        return $category != null ? $category->getData('category_id') : null;
    }

    public function showSlider()
    {
        return $this->categoryPageDataProvider->showSlider();
    }

    public function getSlider()
    {
        return $this->categoryPageDataProvider->getSlider();
    }

    public function showSendingGiftBasketInfo()
    {
        return $this->categoryPageDataProvider->showSendingGiftBasketInfo();
    }

    public function getSendingGiftBasketContent()
    {
        return $this->categoryPageDataProvider->getSendingGiftBasketContent();
    }

    public function showFreeShippingInfo()
    {
        return $this->categoryPageDataProvider->showFreeShippingInfo();
    }

    public function getFreeShippingContent()
    {
        return $this->categoryPageDataProvider->getFreeShippingContent();
    }

    public function showSameDayDeliveryInfo()
    {
        return $this->categoryPageDataProvider->showSameDayDeliveryInfo();
    }

    public function getSameDayDeliveryContent()
    {
        return $this->categoryPageDataProvider->getSameDayDeliveryContent();
    }

    public function showNextDayDeliveryInfo()
    {
        return $this->categoryPageDataProvider->showNextDayDeliveryInfo();
    }

    public function getNextDayDeliveryContent()
    {
        return $this->categoryPageDataProvider->getNextDayDeliveryContent();
    }

    public function showAnyDayDeliveryInfo()
    {
        return $this->categoryPageDataProvider->showAnyDayDeliveryInfo();
    }

    public function getAnyDayDeliveryContent()
    {
        return $this->categoryPageDataProvider->getAnyDayDeliveryContent();
    }

    public function showCategoryPageList()
    {
        return false;
        return $this->categoryPageDataProvider->showCategoryPageList();
    }

    public function getCityCategories()
    {
        //return $this->categoryPageDataProvider->getCityCategories();
        return null;
    }

    public function showRelatedCityList()
    {
        return $this->categoryPageDataProvider->showRelatedCityList();
    }

    public function getRelatedCities()
    {
        return $this->categoryPageDataProvider->getRelatedCities();
    }

    public function getPageMetaDescription()
    {
        return $this->categoryPageDataProvider->getPageMetaDescription();
    }

    public function getFreeShippingBlockTitle()
    {
        $freeShippingTitle = $this->categoryPageDataProvider->getFreeShippingBlockTitle();
        $city = $this->getCity();
        return __(
            $freeShippingTitle,
            $city->getName(),
            $city->getCountryName()
        );
    }

    public function getNextDayDeliveryBlockTitle()
    {
        $title = $this->categoryPageDataProvider->getNextDayDeliveryBlockTitle();
        return __(
            $title,
            $this->getCity()->getName()
        );
    }

    public function getAnyDayDeliveryBlockTitle()
    {
        return $this->categoryPageDataProvider->getAnyDayDeliveryTitle();
    }

    public function getCategoryPageBlockTitle()
    {
        return __('Popular Collections in %1', $this->getCity()->getName());
    }

    public function getRelatedCitiesBlockTitle()
    {
        return __('Other %1 Delivery Areas', $this->getCity()->getCountryName());
    }

    public function getPageTitle()
    {
        return __(
            $this->categoryPageDataProvider->getPageTitle(),
            $this->getCategory()->getCategoryName(),
            $this->getCity()->getName()
        );
    }

    public function getSendingGiftBasketBlockTitle()
    {
        $city = $this->getCity();
        return __(
            'Sending a %1 gift basket to %2, %3, %4',
            $this->getCategory()->getCategoryName(),
            $city->getName(),
            $city->getRegionName(),
            $city->getCountryName()
        );
    }

    public function getSameDayDeliveryBlockTitle()
    {
        $sameDayDeliveryTitle = $this->categoryPageDataProvider->getSameDayBlockTitle();
        $city = $this->getCity();
        return __(
            $sameDayDeliveryTitle,
            ucwords($this->getCategory()->getCategoryName()),
            $city->getName()
        );
    }

    public function showBlock2()
    {
        return $this->categoryPageDataProvider->showBlock2();
    }

    public function getBlock2Title()
    {
        return $this->categoryPageDataProvider->getBlock2Title();
    }

    public function getBlock2Content()
    {
        return $this->categoryPageDataProvider->getBlock2Content();
    }

    public function showThreeColumnBlock()
    {
        return $this->categoryPageDataProvider->showThreeColumnBlock();
    }

    public function getThreeColumnContent()
    {
        return $this->categoryPageDataProvider->getThreeColumnContent();
    }

    public function getStoreCurrencyCode()
    {
        return $this->categoryPageDataProvider->getStoreCurrencyCode();
    }

    public function getThreeColumnHeading1()
    {
        return $this->categoryPageDataProvider->getThreeColumnHeading1();
    }
    
    public function getThreeColumnHeading2()
    {
        return $this->categoryPageDataProvider->getThreeColumnHeading2();
    }
    
    public function getThreeColumnHeading3()
    {
        return $this->categoryPageDataProvider->getThreeColumnHeading3();
    }
}
