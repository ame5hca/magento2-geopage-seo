<?php

namespace GiftGroup\GeoPage\Model\DataProvider;

use GiftGroup\GeoPage\Model\Registry\CategoryPageView as CategoryPageViewRegistry;
use GiftGroup\GeoPage\Model\Config;
use GiftGroup\GeoPage\Model\Page\ShortCodeRenderer;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\View\Page\Config as PageConfig;

class CategoryPageView
{
    private $categoryPageRegistry;

    private $cityPageDataProvider;

    private $shortCodeRenderer;

    private $templateFilterProvider;

    private $pageConfig;

    private $config;

    public function __construct(
        CategoryPageViewRegistry $categoryPageRegistry,
        CityPage $cityPageDataProvider,
        ShortCodeRenderer $shortCodeRenderer,
        FilterProvider $templateFilterProvider,
        PageConfig $pageConfig,
        Config $config
    ) {
        $this->categoryPageRegistry = $categoryPageRegistry;
        $this->cityPageDataProvider = $cityPageDataProvider;
        $this->shortCodeRenderer = $shortCodeRenderer;
        $this->templateFilterProvider = $templateFilterProvider;
        $this->pageConfig = $pageConfig;
        $this->config = $config;
    }

    public function getCity()
    {
        return $this->categoryPageRegistry->getCity();
    }
    
    public function getCategoryPage()
    {
        return $this->categoryPageRegistry->getCategoryPage();
    }

    public function showSlider()
    {
        if (!$this->getCategoryPage()->getSliderBlockStatus()) {
            return false;
        }
        return true;
    }
    
    public function getSlider()
    {
        $sliderId = $this->getCategoryPage()->getSliderId();
        if (empty($sliderId)) {
            return $this->filterContent(
                $this->getCategoryPage()->getSlider()
            );
        }
        return $this->getSliderFromId($sliderId);
    }
    
    public function showSendingGiftBasketInfo()
    {
        if (!$this->getCategoryPage()->getSendGiftBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getSendingGiftBasketContent()
    {
        $giftBlockInfo = $this->getCategoryPage()->getSendGiftBlockContent();
        if ($giftBlockInfo != '') {
            return $this->renderShortCode($giftBlockInfo);
        }
        return '';
    }
    
    public function showFreeShippingInfo()
    {
        if (!$this->getCategoryPage()->getFreeShippingBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getFreeShippingContent()
    {
        $freeShippinginfo = $this->getCategoryPage()->getFreeShippingBlockContent();
        if ($freeShippinginfo != '') {
            return $this->renderShortCode($freeShippinginfo);
        }
        return '';
    }
    
    public function showSameDayDeliveryInfo()
    {
        if (!$this->getCategoryPage()->getSameDayDeliveryBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getSameDayDeliveryContent()
    {
        $sameDayDeliveryInfo = $this->getCategoryPage()->getSameDayDeliveryBlockContent();
        if ($sameDayDeliveryInfo != '') {
            return $this->renderShortCode($sameDayDeliveryInfo);
        }
        return '';
    }
    
    public function showNextDayDeliveryInfo()
    {
        if (!$this->getCategoryPage()->getNextDayDeliveryBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getNextDayDeliveryContent()
    {
        $nextDayDeliveryInfo = $this->getCategoryPage()->getNextDayDeliveryBlockContent();
        if ($nextDayDeliveryInfo != '') {
            return $this->renderShortCode($nextDayDeliveryInfo);
        }
        return '';
    }
    
    public function showAnyDayDeliveryInfo()
    {
        if (!$this->getCategoryPage()->getAnyDayDeliveryBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getAnyDayDeliveryTitle()
    {
        $anyDayDeliveryInfo = $this->getCategoryPage()->getAnyDayDeliveryBlockTitle();
        if ($anyDayDeliveryInfo != '') {
            return $this->renderShortCode($anyDayDeliveryInfo);
        }
        return '';
    }

    public function getAnyDayDeliveryContent()
    {
        $anyDayDeliveryInfo = $this->getCategoryPage()->getAnyDayDeliveryBlockContent();
        if ($anyDayDeliveryInfo != '') {
            return $this->renderShortCode($anyDayDeliveryInfo);
        }
        return '';
    }

    public function showReviewProductsBlock()
    {
        if (!$this->getCategoryPage()->getReviewProductBlockStatus()) {
            return false;
        }
        return true;
    }
    
    public function getReviewProductLimit()
    {
        $limit = $this->getCategoryPage()->getReviewProductLimit();
        if ($limit) {
            return $limit;
        }
        return ($limit ? $limit : Config::CITY_PAGE_DEFAULT_REVIEW_PRODUCT_LIMIT);
    }
    
    public function showPopularProductsBlock()
    {
        if (!$this->getCategoryPage()->getPopularProductBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getPopularProductLimit()
    {
        $limit = $this->getCategoryPage()->getPopularProductLimit();
        if ($limit) {
            return $limit;
        }
        return ($limit ? $limit : Config::CITY_PAGE_DEFAULT_POPULAR_PRODUCT_LIMIT);
    }

    public function showFaqBlock()
    {
        if (!$this->getCategoryPage()->getFaqBlockStatus()) {
            return false;
        }
        return true;
    }    

    public function showCategoryPageList()
    {
        if (!$this->getCategoryPage()->getCategoryCityBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getCategoryPageListLimit()
    {
        return Config::CITY_PAGE_DEFAULT_CATEGORY_PAGE_LIST_LIMIT;
        $limit = $this->getCategoryPage()->getCategoryCityPageLimit();
        if ($limit) {
            return $limit;
        }
        return ($limit ? $limit : Config::CITY_PAGE_DEFAULT_CATEGORY_PAGE_LIST_LIMIT);
    }
    
    public function getRelatedCategories()
    {
        $limit = $this->getCategoryPageListLimit();
        return $this->getCategoryPage()->getRelatedCategoryPages($limit);
    }
    
    public function showRelatedCityList()
    {
        if (!$this->getCategoryPage()->getRelatedCityBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getRelatedCityListingLimit()
    {
        $limit = $this->getCategoryPage()->getRelatedCityLimit();
        if ($limit) {
            return $limit;
        }
        return ($limit ? $limit : Config::RELATED_CITY_LIST_DEFAULT_LIMIT);
    }
    
    public function getRelatedCities()
    {
        $limit = $this->getRelatedCityListingLimit();
        return $this->cityPageDataProvider->getRelatedCityPagesForCategoryPage($this->categoryPageRegistry, $limit);
    }
    
    private function renderShortCode($data)
    {
        return $this->shortCodeRenderer->render(
            $this->getCity(),
            $data,
            $this->getCategoryPage()->getData('store_id')
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
        if (!$this->getCategoryPage()->getData('block2_status')) {
            return false;
        }
        return true;
    }
    
    public function getBlock2Title()
    {
        return $this->renderShortCode($this->getCategoryPage()->getData('block2_content_title'));
    }
    
    public function getBlock2Content()
    {
        return $this->renderShortCode($this->getCategoryPage()->getData('block2_content'));
    }
    
    public function showBlock3()
    {
        if (!$this->getCity()->getData('block3_status')) {
            return false;
        }
        if (!$this->getCategoryPage()->getData('block3_status')) {
            return false;
        }
        return true;
    }
    
    public function getBlock3Title()
    {
        return $this->renderShortCode($this->getCategoryPage()->getData('block3_title'));
    }
    
    public function getBlock3Category()
    {
        return $this->getCategoryPage()->getBlock3Category();
    }
    
    public function showThreeColumnBlock()
    {
        if (!$this->getCity()->getData('three_col_block_status')) {
            return false;
        }
        if (!$this->getCategoryPage()->getData('three_col_block_status')) {
            return false;
        }
        return true;
    }
    
    public function getThreeColumnContent()
    {
        $content = [];
        $content['col1'] = $this->renderShortCode(
            $this->getCategoryPage()->getData('three_col_block_col1')
        );
        $content['col2'] = $this->renderShortCode(
            $this->getCategoryPage()->getData('three_col_block_col2')
        );
        $content['col3'] = $this->renderShortCode(
            $this->getCategoryPage()->getData('three_col_block_col3')
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
            'same_day_category_block_title',
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
            'category_page_title',
            $this->cityPageDataProvider->getCurrentStoreCode()
        );
    }
}