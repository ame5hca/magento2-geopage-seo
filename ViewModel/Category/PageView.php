<?php

namespace GiftGroup\GeoPage\ViewModel\Category;

use GiftGroup\GeoPage\Model\Page\Category\ShortCodeRenderer;
use GiftGroup\GeoPage\Model\DataProvider\CityPageView as CityPageDataProvider;
use GiftGroup\GeoPage\ViewModel\CityPage;

class PageView extends CityPage
{
    private $categoryShortCodeRender;

    public function __construct(
        CityPageDataProvider $cityPageDataProvider,
        ShortCodeRenderer $categoryShortCodeRender
    ) {
        parent::__construct($cityPageDataProvider);
        $this->categoryShortCodeRender = $categoryShortCodeRender;
    }

    public function getPageTitle()
    {
        return __(
            '%1 Gift Baskets Delivery In %2', 
            $this->getCategory()->getName(), 
            $this->getCity()->getName()
        );
    }

    public function getSendingGiftBasketBlockTitle()
    {        
        $city = $this->getCity();
        return __(
            'Sending a %1 gift basket to %2, %3, %4', 
            $this->getCategory()->getName(),
            $city->getName(), 
            $city->getRegionName(), 
            $city->getCountryName()
        );
    }

    public function getSameDayDeliveryBlockTitle()
    {
        $city = $this->getCity();
        return __(
            'Same-day %1 gift delivery to %2', 
            $this->getCategory()->getName(),
            $city->getName()
        );
    }

    public function getSendingGiftBasketContent()
    {
        $content = parent::getSendingGiftBasketContent();
        return $this->categoryShortCodeRender->render(
            $this->getCategory(),
            $content
        );
    }

    public function getFreeShippingContent()
    {
        $content = parent::getFreeShippingContent();
        return $this->categoryShortCodeRender->render(
            $this->getCategory(),
            $content
        );
    }

    public function getSameDayDeliveryContent()
    {
        $content = parent::getSameDayDeliveryContent();
        return $this->categoryShortCodeRender->render(
            $this->getCategory(),
            $content
        );
    }

    public function getCityCategories()
    {
        $categoryCollection = parent::getCityCategories();
        if ($categoryCollection) {
            $categoryId = $this->getCategoryId();
            $categoryCollection->addFieldToFilter('entity_id', ['neq' => $categoryId]);
        }
        return $categoryCollection;
    }
}