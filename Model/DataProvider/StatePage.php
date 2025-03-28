<?php

namespace GiftGroup\GeoPage\Model\DataProvider;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\RequestInterface;
use GiftGroup\GeoPage\Model\StatePageFactory;
use GiftGroup\GeoPage\Model\ResourceModel\StatePage\CollectionFactory as StatePageCollectionFactory;
use GiftGroup\GeoPage\Model\StatesFactory;
use GiftGroup\GeoPage\Model\Config;
use GiftGroup\GeoPage\Model\Page\State\ShortCodeRenderer;

class StatePage
{
    private $storeManager;

    private $request;

    private $statePageFactory;

    private $locationDataProvider;

    private $statePageCollectionFactory;

    private $statesFactory;

    private $config;

    private $shortCodeRenderer;

    private $statePage = null;

    private $storeCode = null;

    public function __construct(
        StoreManagerInterface $storeManager,
        RequestInterface $request,
        StatePageFactory $statePageFactory,
        CityLocation $locationDataProvider,
        StatePageCollectionFactory $statePageCollectionFactory,
        StatesFactory $statesFactory,
        Config $config,
        ShortCodeRenderer $shortCodeRenderer
    ) {
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->statePageFactory = $statePageFactory;
        $this->locationDataProvider = $locationDataProvider;
        $this->statePageCollectionFactory = $statePageCollectionFactory;
        $this->statesFactory = $statesFactory;
        $this->config = $config;
        $this->shortCodeRenderer = $shortCodeRenderer;
    }

    public function getCurrentStatePage()
    {
        $statePageId = $this->request->getParam('id', null);
        if (!$statePageId) {
            return null;
        }
        return $this->getStatePageById($statePageId);
    }

    public function getStatePageById($pageId)
    {
        if (!$this->statePage || ($pageId != $this->statePage->getId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $this->statePage = $this->statePageFactory->create()->load($pageId);
            if ($storeId != $this->statePage->getData('store_id')) {
                $this->statePage = null;
            }
        }
        return $this->statePage;
    }
    
    public function getStatePageByStateId($stateId)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $collection = $this->statePageCollectionFactory->create();
        $collection->selectMandatoryFields();
        $collection->addFieldToFilter('state_id', ['eq' => $stateId]);
        $collection->addFieldToFilter('store_id', ['eq' => $storeId]);
        $collection->addStateCodeToResult();
        if ($collection->getSize()) {
            return $collection->getFirstItem();
        }
        return null;
    }

    public function getTitle($statePageRegistry)
    {
        /* return __(
            '%1 Gift Baskets Delivery | Send Gift Basket Delivery To %1,%2 | %3', 
            $statePageRegistry->getStatePage()->getData('state_name'),
            $this->locationDataProvider->getCountryName(
                $statePageRegistry->getStatePage()->getData('country_code')
            ),
            $this->config->getWebsiteName()); */
            return $this->shortCodeRenderer->renderForPageView(
                $statePageRegistry->getStatePage()->getData('meta_title')
            );
    }
    
    public function getDescription($statePageRegistry)
    {
        /* return __(
            'Buy Gift Basket Online - Free Shipping To %1,%2. Same-Day Delivery in %1 | Over 3200 Custom Designs ⏩ Order Now!',
            $statePageRegistry->getStatePage()->getData('state_name'),
            $this->locationDataProvider->getCountryName(
                $statePageRegistry->getStatePage()->getData('country_code')
            )
        ); */
        return $this->shortCodeRenderer->renderForPageView(
            $statePageRegistry->getStatePage()->getData('meta_description')
        );
    }
    
    public function getMetaRobot($statePageRegistry)
    {
        $metRobot = $statePageRegistry->getStatePage()->getMetaRobot();
        if ($metRobot == 'INDEX,FOLLOW') {
            $sorting = $this->request->getParam('product_list_order', null);
            if ($sorting) {
                return 'NOINDEX,FOLLOW';
            }
        }
        return $metRobot;
    }

    public function getRelatedStates($statePageRegistry, $limit)
    {
        $stateId = $statePageRegistry->getStatePage()->getData('state_id');
        $storeId = $statePageRegistry->getStatePage()->getData('state_id');
        try {
            $state = $this->statesFactory->create()->load(
                $stateId
            );
        } catch (\Exception $e) {
            return null;
        }
        $storeId = $this->storeManager->getStore()->getId();
        $collection = $this->statePageCollectionFactory->create();
        $collection->selectMandatoryFields();
        $collection->addFieldToFilter('state_id', ['neq' => $stateId]);
        $collection->addFieldToFilter('store_id', ['eq' => $storeId]);
        $collection->addCountryFilter($state->getData('country_code'));
        if ($limit) {
            $collection->setPageSize($limit);
            $collection->setCurPage(1);
        }
        $collection->getSelect()->orderRand();
        
        return $collection;
    }

    public function getStoreCurrencyCode()
    {
        return $this->storeManager->getStore()->getCurrentCurrencyCode();
    }

    public function getCurrentStoreCode()
    {
        if (!$this->storeCode) {
            $this->storeCode = $this->storeManager->getStore()->getCode();
        }
        return $this->storeCode;
    }
}