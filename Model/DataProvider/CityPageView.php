<?php

namespace GiftGroup\GeoPage\Model\DataProvider;

use GiftGroup\GeoPage\Model\Registry\CityPageView as CityPageViewRegistry;
use GiftGroup\GeoPage\Model\Config;
use GiftGroup\GeoPage\Model\Page\ShortCodeRenderer;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\View\Page\Config as PageConfig;

class CityPageView
{
    private $cityPageRegistry;

    private $cityPageDataProvider;

    private $shortCodeRenderer;

    private $templateFilterProvider;

    private $pageConfig;

    private $config;

    public function __construct(
        CityPageViewRegistry $cityPageRegistry,
        CityPage $cityPageDataProvider,
        ShortCodeRenderer $shortCodeRenderer,
        FilterProvider $templateFilterProvider,
        PageConfig $pageConfig,
        Config $config
    ) {
        $this->cityPageRegistry = $cityPageRegistry;
        $this->cityPageDataProvider = $cityPageDataProvider;
        $this->shortCodeRenderer = $shortCodeRenderer;
        $this->templateFilterProvider = $templateFilterProvider;
        $this->pageConfig = $pageConfig;
        $this->config = $config;
    }

    public function getCity()
    {
        return $this->cityPageRegistry->getCity();
    }
    
    public function getCityPage()
    {
        return $this->cityPageRegistry->getCityPage();
    }
    
    public function getCategory()
    {
        return $this->cityPageRegistry->getCategory();
    }

    public function showSlider()
    {
        if (!$this->getCity()->getSliderBlockStatus()) {
            return false;
        }
        if (!$this->getCityPage()->getSliderBlockStatus()) {
            return false;
        }
        return true;
    }
    
    public function getSlider()
    {
        $sliderId = $this->getCityPage()->getSliderId();
        if (empty($sliderId)) {
            return $this->filterContent(
                $this->getCityPage()->getSlider()
            );
        }
        return $this->getSliderFromId($sliderId);
    }
    
    public function showSendingGiftBasketInfo()
    {
        if (!$this->getCity()->getSendGiftBlockStatus()) {
            return false;
        }
        if (!$this->getCityPage()->getSendGiftBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getSendingGiftBasketContent()
    {
        $giftBlockInfo = $this->getCityPage()->getSendGiftBlockContent();
        if ($giftBlockInfo != '') {
            return $this->renderShortCode($giftBlockInfo);
        }
        return $this->renderShortCode($this->getCity()->getSendGiftBlockContent());
    }
    
    public function showFreeShippingInfo()
    {
        if (!$this->getCity()->getFreeShippingBlockStatus()) {
            return false;
        }
        if (!$this->getCityPage()->getFreeShippingBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getFreeShippingContent()
    {
        $freeShippinginfo = $this->getCityPage()->getFreeShippingBlockContent();
        if ($freeShippinginfo != '') {
            return $this->renderShortCode($freeShippinginfo);
        }
        return $this->renderShortCode($this->getCity()->getFreeShippingBlockContent());
    }
    
    public function showSameDayDeliveryInfo()
    {
        if (!$this->getCity()->getSameDayDeliveryBlockStatus()) {
            return false;
        }
        if (!$this->getCityPage()->getSameDayDeliveryBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getSameDayDeliveryContent()
    {
        $sameDayDeliveryInfo = $this->getCityPage()->getSameDayDeliveryBlockContent();
        if ($sameDayDeliveryInfo != '') {
            return $this->renderShortCode($sameDayDeliveryInfo);
        }
        return $this->renderShortCode($this->getCity()->getSameDayDeliveryBlockContent());
    }
    
    public function showNextDayDeliveryInfo()
    {
        if (!$this->getCity()->getNextDayDeliveryBlockStatus()) {
            return false;
        }
        if (!$this->getCityPage()->getNextDayDeliveryBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getNextDayDeliveryContent()
    {
        $nextDayDeliveryInfo = $this->getCityPage()->getNextDayDeliveryBlockContent();
        if ($nextDayDeliveryInfo != '') {
            return $this->renderShortCode($nextDayDeliveryInfo);
        }
        return $this->renderShortCode($this->getCity()->getNextDayDeliveryBlockContent());
    }
    
    public function showAnyDayDeliveryInfo()
    {
        if (!$this->getCity()->getAnyDayDeliveryBlockStatus()) {
            return false;
        }
        if (!$this->getCityPage()->getAnyDayDeliveryBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getAnyDayDeliveryTitle()
    {
        $anyDayDeliveryInfo = $this->getCityPage()->getAnyDayDeliveryBlockTitle();
        if ($anyDayDeliveryInfo != '') {
            return $this->renderShortCode($anyDayDeliveryInfo);
        }
        return '';
    }

    public function getAnyDayDeliveryContent()
    {
        $anyDayDeliveryInfo = $this->getCityPage()->getAnyDayDeliveryBlockContent();
        if ($anyDayDeliveryInfo != '') {
            return $this->renderShortCode($anyDayDeliveryInfo);
        }
        return $this->renderShortCode($this->getCity()->getAnyDayDeliveryBlockContent());
    }

    public function showReviewProductsBlock()
    {
        if (!$this->getCity()->getReviewProductBlockStatus()) {
            return false;
        }
        if (!$this->getCityPage()->getReviewProductBlockStatus()) {
            return false;
        }
        return true;
    }
    
    public function getReviewProductLimit()
    {
        $limit = $this->getCityPage()->getReviewProductLimit();
        if ($limit) {
            return $limit;
        }
        $limit = $this->getCity()->getReviewProductLimit();
        return ($limit ? $limit : Config::CITY_PAGE_DEFAULT_REVIEW_PRODUCT_LIMIT);
    }
    
    public function showPopularProductsBlock()
    {
        if (!$this->getCity()->getPopularProductBlockStatus()) {
            return false;
        }
        if (!$this->getCityPage()->getPopularProductBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getPopularProductLimit()
    {
        $limit = $this->getCityPage()->getPopularProductLimit();
        if ($limit) {
            return $limit;
        }
        $limit = $this->getCity()->getPopularProductLimit();
        return ($limit ? $limit : Config::CITY_PAGE_DEFAULT_POPULAR_PRODUCT_LIMIT);
    }

    public function showFaqBlock()
    {
        if (!$this->getCity()->getFaqBlockStatus()) {
            return false;
        }
        if (!$this->getCityPage()->getFaqBlockStatus()) {
            return false;
        }
        return true;
    }    

    public function showCategoryPageList()
    {
        if (!$this->getCity()->getCategoryCityBlockStatus()) {
            return false;
        }
        if (!$this->getCityPage()->getCategoryCityBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getCategoryPageListLimit()
    {
        return Config::CITY_PAGE_DEFAULT_CATEGORY_PAGE_LIST_LIMIT;
        $limit = $this->getCityPage()->getCategoryCityPageLimit();
        if ($limit) {
            return $limit;
        }
        $limit = $this->getCity()->getCategoryCityPageLimit();
        return ($limit ? $limit : Config::CITY_PAGE_DEFAULT_CATEGORY_PAGE_LIST_LIMIT);
    }
    
    public function getCityCategories()
    {
        $limit = $this->getCategoryPageListLimit();
        return $this->getCityPage()->getCategoryCollection($limit);
    }
    
    public function showRelatedCityList()
    {
        if (!$this->getCity()->getRelatedCityBlockStatus()) {
            return false;
        }
        if (!$this->getCityPage()->getRelatedCityBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getRelatedCityListingLimit()
    {
        $limit = $this->getCityPage()->getRelatedCityLimit();
        if ($limit) {
            return $limit;
        }
        $limit = $this->getCity()->getRelatedCityLimit();
        return ($limit ? $limit : Config::RELATED_CITY_LIST_DEFAULT_LIMIT);
    }
    
    public function getRelatedCities()
    {
        $limit = $this->getRelatedCityListingLimit();
        return $this->cityPageDataProvider->getRelatedCityPages($this->cityPageRegistry, $limit);
    }
    
    private function renderShortCode($data)
    {
        return $this->shortCodeRenderer->render(
            $this->getCity(), 
            $data,
            $this->getCityPage()->getStoreId()
        );
    }

    private function filterContent($content)
    {
        return $this->templateFilterProvider->getPageFilter()->filter($content);
    }

    public function getPageMetaDescription()
    {
        return $this->pageConfig->getDescription();
    }

    private function getSliderFromId($sliderId)
    {
        return $this->filterContent('{{widget type="Ubertheme\UbContentSlider\Block\Widget\MediaWidget" content_type="slider" js_lib="owl1" slider_id="'. $sliderId .'" show_title="0" single_item="1" auto_height="1" show_item_title="1" show_item_desc="1" show_navigation="1" infinite="1" show_paging="1" paging_numbers="0" auto_run="1" slide_speed="3000" stop_on_hover="1" pause_on_focus="1" pause_on_dots_hover="1" start_pos="1" addition_class="fullwidth"}}');
    }

    public function showBlock2()
    {
        if (!$this->getCity()->getData('block2_status')) {
            return false;
        }
        if (!$this->getCityPage()->getData('block2_status')) {
            return false;
        }
        return true;
    }
    
    public function getBlock2Title()
    {
        return $this->renderShortCode($this->getCityPage()->getData('block2_content_title'));
    }
    
    public function getBlock2Content()
    {
        return $this->renderShortCode($this->getCityPage()->getData('block2_content'));
    }
    
    public function showBlock3()
    {
        if (!$this->getCity()->getData('block3_status')) {
            return false;
        }
        if (!$this->getCityPage()->getData('block3_status')) {
            return false;
        }
        return true;
    }
    
    public function getBlock3Title()
    {
        return $this->renderShortCode($this->getCityPage()->getData('block3_title'));
    }
    
    public function getBlock3Category()
    {
        return $this->getCityPage()->getBlock3Category();
    }
    
    public function showThreeColumnBlock()
    {
        if (!$this->getCity()->getData('three_col_block_status')) {
            return false;
        }
        if (!$this->getCityPage()->getData('three_col_block_status')) {
            return false;
        }
        return true;
    }
    
    public function getThreeColumnContent()
    {
        $content = [];
        $content['col1'] = $this->renderShortCode(
            $this->getCityPage()->getData('three_col_block_col1')
        );
        $content['col2'] = $this->renderShortCode(
            $this->getCityPage()->getData('three_col_block_col2')
        );
        $content['col3'] = $this->renderShortCode(
            $this->getCityPage()->getData('three_col_block_col3')
        );

        return $content;
    }

    public function getStoreCurrencyCode()
    {
        return $this->cityPageDataProvider->getStoreCurrencyCode();
    }
    
    public function getFreeShippingBlockTitle()
    {
        $value = $this->config->getDefaultValue(
            'freeshipping_block_title',
            $this->cityPageDataProvider->getCurrentStoreCode()
        );
        return $this->renderShortCode($value);
    }

    public function getNextDayDeliveryBlockTitle()
    {
        return $this->config->getDefaultValue(
            'next_day_delivery_block_title',
            $this->cityPageDataProvider->getCurrentStoreCode()
        );
    }

    public function getSameDayBlockTitle()
    {
        $value = $this->config->getDefaultValue(
            'same_day_block_title',
            $this->cityPageDataProvider->getCurrentStoreCode()
        );
        return $this->renderShortCode($value);
    }
    
    public function getFaqAns1()
    {
        $value = $this->config->getDefaultValue(
            'faq_ans_1',
            $this->cityPageDataProvider->getCurrentStoreCode()
        );
        return $this->renderShortCode($value);
    }
    
    public function getFaqAns2()
    {
        $value = $this->config->getDefaultValue(
            'faq_ans_2',
            $this->cityPageDataProvider->getCurrentStoreCode()
        );
        return $this->renderShortCode($value);
    }
    
    public function getFaqAns3()
    {
        $value = $this->config->getDefaultValue(
            'faq_ans_3',
            $this->cityPageDataProvider->getCurrentStoreCode()
        );
        return $this->renderShortCode($value);
    }
    
    public function getFaqQn1()
    {
        $value = $this->config->getDefaultValue(
            'faq_qn_1',
            $this->cityPageDataProvider->getCurrentStoreCode()
        );
        return $this->renderShortCode($value);
    }
    
    public function getFaqQn2()
    {
        $value = $this->config->getDefaultValue(
            'faq_qn_2',
            $this->cityPageDataProvider->getCurrentStoreCode()
        );
        return $this->renderShortCode($value);
    }
    
    public function getFaqQn3()
    {
        $value = $this->config->getDefaultValue(
            'faq_qn_3',
            $this->cityPageDataProvider->getCurrentStoreCode()
        );
        return $this->renderShortCode($value);
    }
    
    public function getThreeColumnHeading1()
    {
        $value = $this->config->getDefaultValue(
            'three_col_heading_1',
            $this->cityPageDataProvider->getCurrentStoreCode()
        );
        return $this->renderShortCode($value);
    }
    
    public function getThreeColumnHeading2()
    {
        $value = $this->config->getDefaultValue(
            'three_col_heading_2',
            $this->cityPageDataProvider->getCurrentStoreCode()
        );
        return $this->renderShortCode($value);
    }
    
    public function getThreeColumnHeading3()
    {
        $value = $this->config->getDefaultValue(
            'three_col_heading_3',
            $this->cityPageDataProvider->getCurrentStoreCode()
        );
        return $this->renderShortCode($value);
    }
    
    public function getPageTitle()
    {
        return $this->config->getDefaultValue(
            'city_page_title',
            $this->cityPageDataProvider->getCurrentStoreCode()
        );
    }
}