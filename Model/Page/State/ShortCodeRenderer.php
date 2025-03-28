<?php

namespace GiftGroup\GeoPage\Model\Page\State;

use GiftGroup\GeoPage\Model\Registry\StatePageView;
use GiftGroup\GeoPage\Model\DataProvider\CityLocation;
use Magento\Store\Api\StoreRepositoryInterface;
use GiftGroup\GeoPage\Model\Config;

class ShortCodeRenderer
{
    private $statePageRegistry;

    private $locationDataProvider;

    private $storeRepository;

    private $config;

    public function __construct(
        StatePageView $statePageRegistry,
        CityLocation $locationDataProvider,
        StoreRepositoryInterface $storeRepository,
        Config $config
    ) {
        $this->statePageRegistry = $statePageRegistry;
        $this->locationDataProvider = $locationDataProvider;
        $this->storeRepository = $storeRepository;
        $this->config = $config;
    }

    public function render($data)
    {
        if (is_null($data)) {
            return $data;
        }
        if (str_contains($data, '{state}')) {            
            $stateName = $this->getStateName();
            $data = str_replace('{state}', $stateName, $data);
        }
        return $data;        
    }
    
    public function renderForPageView($data)
    {
        if (is_null($data)) {
            return $data;
        }
        $statePage = $this->statePageRegistry->getStatePage();
        $data = $this->render($data);
        $data = str_replace('{city}', $this->getStateName(), $data);            
        $data = str_replace('{category}', '', $data);
        $data = str_replace('{region},', '', $data);
        $data = str_replace('{region}', '', $data);
        if (str_contains($data, '{country}')) {
            $data = str_replace(
                '{country}', 
                $this->locationDataProvider->getCountryName($statePage->getData('country_code')), 
                $data
            );
        }
        /* if (str_contains($data, '{region}')) {
            $data = str_replace('{region}', $this->getStateName(), $data);
        } */
        if (str_contains($data, '{website}')) {
            /* $storeName = $this->storeRepository->getById(
                $statePage->getStoreId()
            )->getWebsite()->getName(); */
            $storeName = $this->config->getWebsiteName();
            $data = str_replace('{website}', $storeName, $data);
        }
        if (str_contains($data, '{delivery}')) {
            $data = $this->renderDeliveryText($statePage, $data);
        }
        return $data;        
    }
    
    private function getStateName()
    {
        $statePage = $this->statePageRegistry->getStatePage();
        return $statePage ? $statePage->getData('state_name') : '';
    }

    private function renderDeliveryText($statePage, $data)
    {
        $deliveryText = "Same-Day";
        if ($statePage->getData('next_day_delivery_block_status')) {
            $deliveryText = "Next-Day";
        } else if ($statePage->getData('any_day_delivery_block_status')) {
            $deliveryText = "Any-Day";
        }
        return str_replace('{delivery}', $deliveryText, $data);
    }
}