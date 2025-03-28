<?php

namespace GiftGroup\GeoPage\Model\Page;

use GiftGroup\GeoPage\Model\City;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\Store;
use GiftGroup\GeoPage\Model\CityPageFactory;
use GiftGroup\GeoPage\Model\ResourceModel\CityPage\CollectionFactory as CityPageCollectionFactory;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;

class Generator
{
    protected $shortCodeRenderer;

    protected $serializer;

    protected $storeRepository;

    protected $cityPageFactory;

    protected $cityPageCollectionFactory;

    protected $productsGenerator;

    protected $eventManager;

    protected $city = null;

    protected $storeId = null;

    public function __construct(
        ShortCodeRenderer $shortCodeRenderer,
        SerializerInterface $serializer,
        StoreRepositoryInterface $storeRepository,
        CityPageFactory $cityPageFactory,
        CityPageCollectionFactory $cityPageCollectionFactory,
        ProductsGenerator $productsGenerator,
        EventManagerInterface $eventManager
    ) {
        $this->shortCodeRenderer = $shortCodeRenderer;
        $this->serializer = $serializer;
        $this->storeRepository = $storeRepository;
        $this->cityPageFactory = $cityPageFactory;
        $this->cityPageCollectionFactory = $cityPageCollectionFactory;
        $this->productsGenerator = $productsGenerator;
        $this->eventManager = $eventManager;
    }

    public function build(City $city)
    {
        $this->city = $city;
        $pageStoreIds = $this->getPageStoreIds();
        foreach ($pageStoreIds as $storeId) {
            if ($storeId == Store::DEFAULT_STORE_ID) {
                continue;
            }
            $this->storeId = $storeId;
            $pageData = $this->preparePageDataForStore();
            $this->createOrUpdateCityPage($pageData);
        }
        $this->updateCityPageGenerateStatus();
        $this->clearAll();
        return true;
    }

    public function preparePageDataForStore()
    {
        $pageData = [];
        $pageData['city_id'] = $this->city->getId();
        $pageData['city_code'] = $this->city->getCode();
        $pageData['city_name'] = $this->city->getName();
        $pageData['store_id'] = $this->storeId;
        $pageData['is_active'] = $this->city->getIsActive();
        $pageData['categories'] = $this->city->getCategories();
        $pageData['description'] = $this->city->getDescription();
        $pageData['meta_title'] = $this->renderShortCodes($this->city->getMetaTitle());
        $pageData['meta_description'] = $this->renderShortCodes($this->city->getMetaDescription());
        $pageData['meta_robot'] = $this->city->getMetaRobot();
        $pageData['product_ids'] = $this->serializer->serialize(
            $this->productsGenerator->generate($this->storeId, $this->city->getProductLimit())
        );
        $pageData['product_limit'] = $this->city->getProductLimit();
        $pageData['slider_block_status'] = $this->city->getSliderBlockStatus();
        $pageData['slider'] = $this->city->getSlider();
        $pageData['send_gift_block_status'] = $this->city->getSendGiftBlockStatus();
        $pageData['send_gift_block_content'] = $this->renderShortCodes(
            $this->city->getSendGiftBlockContent()
        );
        $pageData['free_shipping_block_status'] = $this->city->getFreeShippingBlockStatus();
        $pageData['free_shipping_block_content'] = $this->renderShortCodes(
            $this->city->getFreeShippingBlockContent()
        );
        $pageData['same_day_delivery_block_status'] = $this->city->getSameDayDeliveryBlockStatus();
        $pageData['same_day_delivery_block_content'] = $this->renderShortCodes(
            $this->city->getSameDayDeliveryBlockContent()
        );
        $pageData['next_day_delivery_block_status'] = $this->city->getNextDayDeliveryBlockStatus();
        $pageData['next_day_delivery_block_content'] = $this->renderShortCodes(
            $this->city->getNextDayDeliveryBlockContent()
        );
        $pageData['any_day_delivery_block_status'] = $this->city->getAnyDayDeliveryBlockStatus();
        $pageData['any_day_delivery_block_content'] = $this->renderShortCodes(
            $this->city->getAnyDayDeliveryBlockContent()
        );
        $pageData['popular_product_block_status'] = $this->city->getPopularProductBlockStatus();
        $pageData['popular_product_limit'] = $this->city->getPopularProductLimit();
        $pageData['review_product_block_status'] = $this->city->getReviewProductBlockStatus();
        $pageData['review_product_limit'] = $this->city->getReviewProductLimit();
        $pageData['category_city_block_status'] = $this->city->getCategoryCityBlockStatus();
        $pageData['category_city_page_limit'] = $this->city->getCategoryCityPageLimit();
        $pageData['related_city_block_status'] = $this->city->getRelatedCityBlockStatus();
        $pageData['related_city_limit'] = $this->city->getRelatedCityLimit();
        $pageData['faq_block_status'] = $this->city->getFaqBlockStatus();
        //$pageData['faq'] = $this->renderShortCodes($this->city->getFaq());

        return $pageData;
    }

    private function renderShortCodes($data)
    {
        return $this->shortCodeRenderer->render(
            $this->city,
            $data,
            $this->storeId
        );
    }

    private function getPageStoreIds()
    {
        $storeIds = $this->city->getPageStoreId();
        if (!is_array($storeIds)) {
            $storeIds = $this->serializer->unserialize($storeIds);
        }
        if (in_array("0", $storeIds)) {
            $allStoreIds = [];
            $allStores = $this->storeRepository->getList();
            foreach ($allStores as $store) {
                $allStoreIds[] = $store->getId();
            }
            $storeIds = array_merge($storeIds, $allStoreIds);
            $storeIds = array_unique($storeIds);
        }
        return $storeIds;
    }

    private function createOrUpdateCityPage($pageData)
    {
        $cityPageCollection = $this->cityPageCollectionFactory->create();
        $cityPageCollection->addFieldToFilter('city_id', ['eq' => $pageData['city_id']]);
        $cityPageCollection->addFieldToFilter('store_id', ['eq' => $pageData['store_id']]);
        if ($cityPageCollection->getSize()) {
            $cityPage = $cityPageCollection->getFirstItem();
            $pageData['id'] = $cityPage->getId();
        } else {
            $cityPage = $this->cityPageFactory->create();
        }
        $cityPage->setData($pageData);
        $cityPage->setCityCode($pageData['city_code']);
        $cityPage->save();
        
        $this->eventManager->dispatch(
            'giftgroup_geopage_new_city_page_generated', 
            ['city_page' => $cityPage]
        );

        return true;   
    }
    
    private function updateCityPageGenerateStatus() {
        $this->city->setData('is_page_generated', true);
        $this->city->save();
    }

    private function clearAll() {
        $this->city = null;
        $this->storeId = null;
    }
}
