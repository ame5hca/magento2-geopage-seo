<?php

namespace GiftGroup\GeoPage\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use GiftGroup\GeoPage\Model\DataProvider\StatePageView as StatePageViewDataProvider;

class StatePage implements ArgumentInterface
{
    protected $statePageDataProvider;

    public function __construct(
        StatePageViewDataProvider $statePageDataProvider
    ) {
        $this->statePageDataProvider = $statePageDataProvider;
    }

    public function getStatePage()
    {
        return $this->statePageDataProvider->getStatePage();
    }

    public function showSlider()
    {
        return $this->statePageDataProvider->showSlider();
    }

    public function getSlider()
    {
        return $this->statePageDataProvider->getSlider();
    }

    public function showSendingGiftBasketInfo()
    {
        return $this->statePageDataProvider->showSendingGiftBasketInfo();
    }

    public function getSendingGiftBasketContent()
    {
        return $this->statePageDataProvider->getSendingGiftBasketContent();
    }

    public function showFreeShippingInfo()
    {
        return $this->statePageDataProvider->showFreeShippingInfo();
    }

    public function getFreeShippingContent()
    {
        return $this->statePageDataProvider->getFreeShippingContent();
    }

    public function showSameDayDeliveryInfo()
    {
        return $this->statePageDataProvider->showSameDayDeliveryInfo();
    }

    public function getSameDayDeliveryContent()
    {
        return $this->statePageDataProvider->getSameDayDeliveryContent();
    }

    public function showNextDayDeliveryInfo()
    {
        return $this->statePageDataProvider->showNextDayDeliveryInfo();
    }

    public function getNextDayDeliveryContent()
    {
        return $this->statePageDataProvider->getNextDayDeliveryContent();
    }

    public function showAnyDayDeliveryInfo()
    {
        return $this->statePageDataProvider->showAnyDayDeliveryInfo();
    }

    public function getAnyDayDeliveryContent()
    {
        return $this->statePageDataProvider->getAnyDayDeliveryContent();
    }

    public function showCategoryPageList()
    {
        return false;
        return $this->statePageDataProvider->showCategoryPageList();
    }

    public function getCityCategories()
    {
        //return $this->statePageDataProvider->getCityCategories();
        return null;
    }

    public function showRelatedStatesList()
    {
        return $this->statePageDataProvider->showRelatedStatesList();
    }

    public function getRelatedStates()
    {
        return $this->statePageDataProvider->getRelatedStates();
    }

    public function getPageMetaDescription()
    {
        return $this->statePageDataProvider->getPageMetaDescription();
    }

    public function getFreeShippingBlockTitle()
    {
        $freeShippingTitle = $this->statePageDataProvider->getFreeShippingBlockTitle();
        return __(
            $freeShippingTitle,
            $this->getStatePage()->getData('state_name'),
            $this->getStatePage()->getCountryName()
        );
    }

    public function getNextDayDeliveryBlockTitle()
    {
        $title = $this->statePageDataProvider->getNextDayDeliveryBlockTitle();
        return __(
            $title,
            $this->getStatePage()->getData('state_name')
        );
    }

    public function getAnyDayDeliveryBlockTitle()
    {
        return $this->statePageDataProvider->getAnyDayDeliveryTitle();
    }

    public function getCategoryPageBlockTitle()
    {
        return __('Popular Collections in %1', $this->getStatePage()->getData('state_name'));
    }

    public function getRelatedStatesBlockTitle()
    {
        return __('Other %1 Delivery Areas', $this->getStatePage()->getCountryName());
    }

    public function getPageTitle()
    {
        return __(
            $this->statePageDataProvider->getPageTitle(),
            $this->getStatePage()->getData('state_name')
        );
    }

    public function getSendingGiftBasketBlockTitle()
    {
        return __(
            'Sending a gift basket to %1, %2',
            $this->getStatePage()->getData('state_name'),
            $this->getStatePage()->getCountryName()
        );
    }

    public function getSameDayDeliveryBlockTitle()
    {
        $sameDayBlockTitle = $this->statePageDataProvider->getSameDayBlockTitle();
        return __(
            $sameDayBlockTitle,
            $this->getStatePage()->getData('state_name')
        );
    }

    public function showBlock2()
    {
        return $this->statePageDataProvider->showBlock2();
    }

    public function getBlock2Title()
    {
        return $this->statePageDataProvider->getBlock2Title();
    }

    public function getBlock2Content()
    {
        return $this->statePageDataProvider->getBlock2Content();
    }

    public function showThreeColumnBlock()
    {
        return $this->statePageDataProvider->showThreeColumnBlock();
    }

    public function getThreeColumnContent()
    {
        return $this->statePageDataProvider->getThreeColumnContent();
    }
    
    public function getStoreCurrencyCode()
    {
        return $this->statePageDataProvider->getStoreCurrencyCode();
    }

    public function getThreeColumnHeading1()
    {
        return $this->statePageDataProvider->getThreeColumnHeading1();
    }
    
    public function getThreeColumnHeading2()
    {
        return $this->statePageDataProvider->getThreeColumnHeading2();
    }
    
    public function getThreeColumnHeading3()
    {
        return $this->statePageDataProvider->getThreeColumnHeading3();
    }
}
