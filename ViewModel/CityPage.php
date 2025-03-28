<?php

namespace GiftGroup\GeoPage\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use GiftGroup\GeoPage\Model\DataProvider\CityPageView as CityPageDataProvider;

class CityPage implements ArgumentInterface
{
    protected $cityPageDataProvider;

    public function __construct(
        CityPageDataProvider $cityPageDataProvider
    ) {
        $this->cityPageDataProvider = $cityPageDataProvider;
    }

    public function getCity()
    {
        return $this->cityPageDataProvider->getCity();
    }

    public function getCategory()
    {
        return $this->cityPageDataProvider->getCategory();
    }

    public function getCategoryId()
    {
        $category = $this->getCategory();
        return $category != null ? $category->getData('category_id') : null;
    }

    public function showSlider()
    {
        return $this->cityPageDataProvider->showSlider();
    }

    public function getSlider()
    {
        return $this->cityPageDataProvider->getSlider();
    }

    public function showSendingGiftBasketInfo()
    {
        return $this->cityPageDataProvider->showSendingGiftBasketInfo();
    }

    public function getSendingGiftBasketContent()
    {
        return $this->cityPageDataProvider->getSendingGiftBasketContent();
    }

    public function showFreeShippingInfo()
    {
        return $this->cityPageDataProvider->showFreeShippingInfo();
    }

    public function getFreeShippingContent()
    {
        return $this->cityPageDataProvider->getFreeShippingContent();
    }

    public function showSameDayDeliveryInfo()
    {
        return $this->cityPageDataProvider->showSameDayDeliveryInfo();
    }

    public function getSameDayDeliveryContent()
    {
        return $this->cityPageDataProvider->getSameDayDeliveryContent();
    }

    public function showNextDayDeliveryInfo()
    {
        return $this->cityPageDataProvider->showNextDayDeliveryInfo();
    }

    public function getNextDayDeliveryContent()
    {
        return $this->cityPageDataProvider->getNextDayDeliveryContent();
    }

    public function showAnyDayDeliveryInfo()
    {
        return $this->cityPageDataProvider->showAnyDayDeliveryInfo();
    }

    public function getAnyDayDeliveryContent()
    {
        return $this->cityPageDataProvider->getAnyDayDeliveryContent();
    }

    public function showRelatedCityList()
    {
        return $this->cityPageDataProvider->showRelatedCityList();
    }

    public function getRelatedCities()
    {
        return $this->cityPageDataProvider->getRelatedCities();
    }

    public function getPageMetaDescription()
    {
        return $this->cityPageDataProvider->getPageMetaDescription();
    }

    public function getSendingGiftBasketBlockTitle()
    {
        $city = $this->getCity();
        return __(
            'Sending a gift basket to %1, %2, %3',
            $city->getName(),
            $city->getRegionName(),
            $city->getCountryName()
        );
    }

    public function getFreeShippingBlockTitle()
    {
        $freeShippingTitle = $this->cityPageDataProvider->getFreeShippingBlockTitle();
        $city = $this->getCity();
        return __(
            $freeShippingTitle,
            $city->getName(),
            $city->getCountryName()
        );
    }

    public function getSameDayDeliveryBlockTitle()
    {
        $sameDayDeliveryTitle = $this->cityPageDataProvider->getSameDayBlockTitle();
        return __(
            $sameDayDeliveryTitle,
            $this->getCity()->getName()
        );
    }

    public function getNextDayDeliveryBlockTitle()
    {
        $title = $this->cityPageDataProvider->getNextDayDeliveryBlockTitle();
        return __(
            $title,
            $this->getCity()->getName()
        );
    }

    public function getAnyDayDeliveryBlockTitle()
    {
        return $this->cityPageDataProvider->getAnyDayDeliveryTitle();
    }

    public function getPageTitle()
    {
        return __($this->cityPageDataProvider->getPageTitle(), $this->getCity()->getName());
    }

    public function getRelatedCitiesBlockTitle()
    {
        return __('Other %1 Delivery Areas', $this->getCity()->getCountryName());
    }

    public function showBlock2()
    {
        return $this->cityPageDataProvider->showBlock2();
    }

    public function getBlock2Title()
    {
        return $this->cityPageDataProvider->getBlock2Title();
    }

    public function getBlock2Content()
    {
        return $this->cityPageDataProvider->getBlock2Content();
    }

    public function showThreeColumnBlock()
    {
        return $this->cityPageDataProvider->showThreeColumnBlock();
    }

    public function getThreeColumnContent()
    {
        return $this->cityPageDataProvider->getThreeColumnContent();
    }

    public function getStoreCurrencyCode()
    {
        return $this->cityPageDataProvider->getStoreCurrencyCode();
    }
    
    public function getThreeColumnHeading1()
    {
        return $this->cityPageDataProvider->getThreeColumnHeading1();
    }
    
    public function getThreeColumnHeading2()
    {
        return $this->cityPageDataProvider->getThreeColumnHeading2();
    }
    
    public function getThreeColumnHeading3()
    {
        return $this->cityPageDataProvider->getThreeColumnHeading3();
    }
}
