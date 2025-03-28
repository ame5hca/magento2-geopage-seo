<?php

namespace GiftGroup\GeoPage\Model\DataProvider;

use GiftGroup\GeoPage\Model\Registry\StatePageView as StatePageViewRegistry;
use GiftGroup\GeoPage\Model\Config;
use GiftGroup\GeoPage\Model\Page\State\ShortCodeRenderer;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\View\Page\Config as PageConfig;

class StatePageView
{
    private $statePageRegistry;

    private $statePageDataProvider;

    private $shortCodeRenderer;

    private $templateFilterProvider;

    private $pageConfig;

    private $config;

    public function __construct(
        StatePageViewRegistry $statePageRegistry,
        StatePage $statePageDataProvider,
        ShortCodeRenderer $shortCodeRenderer,
        FilterProvider $templateFilterProvider,
        PageConfig $pageConfig,
        Config $config
    ) {
        $this->statePageRegistry = $statePageRegistry;
        $this->statePageDataProvider = $statePageDataProvider;
        $this->shortCodeRenderer = $shortCodeRenderer;
        $this->templateFilterProvider = $templateFilterProvider;
        $this->pageConfig = $pageConfig;
        $this->config = $config;
    }

    public function getStatePage()
    {
        return $this->statePageRegistry->getStatePage();
    }

    public function showSlider()
    {
        if (!$this->getStatePage()->getSliderBlockStatus()) {
            return false;
        }
        return true;
    }
    
    public function getSlider()
    {
        $sliderId = $this->getStatePage()->getSliderId();
        if (empty($sliderId)) {
            return $this->filterContent(
                $this->getStatePage()->getSlider()
            );
        }
        return $this->getSliderFromId($sliderId);
    }
    
    public function showSendingGiftBasketInfo()
    {
        if (!$this->getStatePage()->getSendGiftBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getSendingGiftBasketContent()
    {
        $giftBlockInfo = $this->getStatePage()->getSendGiftBlockContent();
        if ($giftBlockInfo != '') {
            return $this->renderShortCode($giftBlockInfo);
        }
        return '';
    }
    
    public function showFreeShippingInfo()
    {
        if (!$this->getStatePage()->getFreeShippingBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getFreeShippingContent()
    {
        $freeShippinginfo = $this->getStatePage()->getFreeShippingBlockContent();
        if ($freeShippinginfo != '') {
            return $this->renderShortCode($freeShippinginfo);
        }
        return '';
    }
    
    public function showSameDayDeliveryInfo()
    {
        if (!$this->getStatePage()->getSameDayDeliveryBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getSameDayDeliveryContent()
    {
        $sameDayDeliveryInfo = $this->getStatePage()->getSameDayDeliveryBlockContent();
        if ($sameDayDeliveryInfo != '') {
            return $this->renderShortCode($sameDayDeliveryInfo);
        }
        return '';
    }
    
    public function showNextDayDeliveryInfo()
    {
        if (!$this->getStatePage()->getNextDayDeliveryBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getNextDayDeliveryContent()
    {
        $nextDayDeliveryInfo = $this->getStatePage()->getNextDayDeliveryBlockContent();
        if ($nextDayDeliveryInfo != '') {
            return $this->renderShortCode($nextDayDeliveryInfo);
        }
        return '';
    }
    
    public function showAnyDayDeliveryInfo()
    {
        if (!$this->getStatePage()->getAnyDayDeliveryBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getAnyDayDeliveryContent()
    {
        $anyDayDeliveryInfo = $this->getStatePage()->getAnyDayDeliveryBlockContent();
        if ($anyDayDeliveryInfo != '') {
            return $this->renderShortCode($anyDayDeliveryInfo);
        }
        return '';
    }
    
    public function getAnyDayDeliveryTitle()
    {
        $anyDayDeliveryInfo = $this->getStatePage()->getAnyDayDeliveryBlockTitle();
        if ($anyDayDeliveryInfo != '') {
            return $this->renderShortCode($anyDayDeliveryInfo);
        }
        return '';
    }

    public function showReviewProductsBlock()
    {
        if (!$this->getStatePage()->getReviewProductBlockStatus()) {
            return false;
        }
        return true;
    }
    
    public function getReviewProductLimit()
    {
        $limit = $this->getStatePage()->getReviewProductLimit();
        if ($limit) {
            return $limit;
        }
        return ($limit ? $limit : Config::CITY_PAGE_DEFAULT_REVIEW_PRODUCT_LIMIT);
    }
    
    public function showPopularProductsBlock()
    {
        if (!$this->getStatePage()->getPopularProductBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getPopularProductLimit()
    {
        $limit = $this->getStatePage()->getPopularProductLimit();
        if ($limit) {
            return $limit;
        }
        return ($limit ? $limit : Config::CITY_PAGE_DEFAULT_POPULAR_PRODUCT_LIMIT);
    }

    public function showFaqBlock()
    {
        if (!$this->getStatePage()->getFaqBlockStatus()) {
            return false;
        }
        return true;
    }    

    public function showCategoryPageList()
    {
        if (!$this->getStatePage()->getCategoryCityBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getStatePageListLimit()
    {
        $limit = $this->getStatePage()->getCategoryCityPageLimit();
        if ($limit) {
            return $limit;
        }
        return ($limit ? $limit : Config::CITY_PAGE_DEFAULT_CATEGORY_PAGE_LIST_LIMIT);
    }
    
    public function getRelatedCategories()
    {
        $limit = $this->getStatePageListLimit();
        return $this->getStatePage()->getRelatedCategoryPages($limit);
    }
    
    public function showRelatedStatesList()
    {
        if (!$this->getStatePage()->getRelatedCityBlockStatus()) {
            return false;
        }
        return true;
    }

    public function getRelatedStateListingLimit()
    {
        $limit = $this->getStatePage()->getRelatedCityLimit();
        if ($limit) {
            return $limit;
        }
        return ($limit ? $limit : Config::RELATED_CITY_LIST_DEFAULT_LIMIT);
    }
    
    public function getRelatedStates()
    {
        $limit = $this->getRelatedStateListingLimit();
        return $this->statePageDataProvider->getRelatedStates($this->statePageRegistry, $limit);
    }
    
    private function renderShortCode($data)
    {
        return $this->shortCodeRenderer->renderForPageView($data);
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
        if (!$this->getStatePage()->getData('block2_status')) {
            return false;
        }
        return true;
    }
    
    public function getBlock2Title()
    {
        return $this->renderShortCode($this->getStatePage()->getData('block2_content_title'));
    }
    
    public function getBlock2Content()
    {
        return $this->renderShortCode($this->getStatePage()->getData('block2_content'));
    }
    
    public function showBlock3()
    {
        if (!$this->getStatePage()->getData('block3_status')) {
            return false;
        }
        return true;
    }
    
    public function getBlock3Title()
    {
        return $this->renderShortCode($this->getStatePage()->getData('block3_title'));
    }
    
    public function getBlock3Category()
    {
        return $this->getStatePage()->getBlock3Category();
    }
    
    public function showThreeColumnBlock()
    {
        if (!$this->getStatePage()->getData('three_col_block_status')) {
            return false;
        }
        return true;
    }
    
    public function getThreeColumnContent()
    {
        $content = [];
        $content['col1'] = $this->renderShortCode(
            $this->getStatePage()->getData('three_col_block_col1')
        );
        $content['col2'] = $this->renderShortCode(
            $this->getStatePage()->getData('three_col_block_col2')
        );
        $content['col3'] = $this->renderShortCode(
            $this->getStatePage()->getData('three_col_block_col3')
        );

        return $content;
    }

    public function getStoreCurrencyCode()
    {
        return $this->statePageDataProvider->getStoreCurrencyCode();
    }

    public function getFreeShippingBlockTitle()
    {
        $value = $this->config->getDefaultValue(
            'freeshipping_block_title',
            $this->statePageDataProvider->getCurrentStoreCode()
        );
        return $this->renderShortCode($value);
    }
    
    public function getNextDayDeliveryBlockTitle()
    {
        return $this->config->getDefaultValue(
            'next_day_delivery_block_title',
            $this->statePageDataProvider->getCurrentStoreCode()
        );
    }

    public function getSameDayBlockTitle()
    {
        $value = $this->config->getDefaultValue(
            'same_day_block_title',
            $this->statePageDataProvider->getCurrentStoreCode()
        );
        return $this->renderShortCode($value);
    }

    public function getFaqAns1()
    {
        $value = $this->config->getStatePageDefaultValue(
            'faq_ans_1',
            $this->statePageDataProvider->getCurrentStoreCode()
        );
        return $this->renderShortCode($value);
    }
    
    public function getFaqAns2()
    {
        $value = $this->config->getStatePageDefaultValue(
            'faq_ans_2',
            $this->statePageDataProvider->getCurrentStoreCode()
        );
        return $this->renderShortCode($value);
    }
    
    public function getFaqAns3()
    {
        $value = $this->config->getStatePageDefaultValue(
            'faq_ans_3',
            $this->statePageDataProvider->getCurrentStoreCode()
        );
        return $this->renderShortCode($value);
    }

    public function getFaqQn1()
    {
        $value = $this->config->getStatePageDefaultValue(
            'faq_qn_1',
            $this->statePageDataProvider->getCurrentStoreCode()
        );
        return $this->renderShortCode($value);
    }
    
    public function getFaqQn2()
    {
        $value = $this->config->getStatePageDefaultValue(
            'faq_qn_2',
            $this->statePageDataProvider->getCurrentStoreCode()
        );
        return $this->renderShortCode($value);
    }
    
    public function getFaqQn3()
    {
        $value = $this->config->getStatePageDefaultValue(
            'faq_qn_3',
            $this->statePageDataProvider->getCurrentStoreCode()
        );
        return $this->renderShortCode($value);
    }

    public function getThreeColumnHeading1()
    {
        $value = $this->config->getDefaultValue(
            'three_col_heading_1',
            $this->statePageDataProvider->getCurrentStoreCode()
        );
        return $this->renderShortCode($value);
    }
    
    public function getThreeColumnHeading2()
    {
        $value = $this->config->getDefaultValue(
            'three_col_heading_2',
            $this->statePageDataProvider->getCurrentStoreCode()
        );
        return $this->renderShortCode($value);
    }
    
    public function getThreeColumnHeading3()
    {
        $value = $this->config->getDefaultValue(
            'three_col_heading_3',
            $this->statePageDataProvider->getCurrentStoreCode()
        );
        return $this->renderShortCode($value);
    }

    public function getPageTitle()
    {
        return $this->config->getDefaultValue(
            'state_page_title',
            $this->statePageDataProvider->getCurrentStoreCode()
        );
    }
}