<?php

namespace GiftGroup\GeoPage\Model\Page;

use Magento\Store\Api\StoreRepositoryInterface;
use GiftGroup\GeoPage\Model\DataProvider\CityLocation;
use GiftGroup\GeoPage\Model\Config;

class ShortCodeRenderer
{
    private $storeRepository;

    private $cityLocationDataProvider;

    private $categoryShortCodeRender;

    private $stateShortCodeRender;

    private $config;

    public function __construct(
        StoreRepositoryInterface $storeRepository,
        CityLocation $cityLocationDataProvider,
        Category\ShortCodeRenderer $categoryShortCodeRender,
        State\ShortCodeRenderer $stateShortCodeRender,
        Config $config
    ) {
        $this->storeRepository = $storeRepository;
        $this->cityLocationDataProvider = $cityLocationDataProvider;
        $this->categoryShortCodeRender = $categoryShortCodeRender;
        $this->stateShortCodeRender = $stateShortCodeRender;
        $this->config = $config;
    }

    public function render($city, $data, $storeId)
    {
        if (is_null($data)) {
            return $data;
        }
        if (str_contains($data, '{city}')) {
            $data = str_replace('{city}', $city->getName(), $data);
        }             
        if (str_contains($data, '{country}')) {
            $data = $this->renderCountryCode($city->getCountryId(), $data);
        }
        if (str_contains($data, '{region}')) {
            $data = $this->renderRegion($city->getRegion(), $data);
        }
        if (str_contains($data, '{website}') && $storeId != 0) {
            //$storeName = $this->storeRepository->getById($storeId)->getWebsite()->getName();
            $storeName = $this->config->getWebsiteName();
            $data = str_replace('{website}', $storeName, $data);
        }
        if (str_contains($data, '{delivery}')) {
            $data = $this->renderDeliveryText($city, $data);
        }
        $data = $this->categoryShortCodeRender->render($data);
        $data = $this->stateShortCodeRender->render($data);
        return $data;        
    }

    public function renderCountryCode($countryCode, $data)
    {
        return str_replace('{country}', $this->cityLocationDataProvider->getCountryName($countryCode), $data);
    }
    
    public function renderRegion($region, $data)
    {
        return str_replace('{region}', $this->cityLocationDataProvider->getRegionName($region), $data);
    }
    
    public function renderDeliveryText($city, $data)
    {
        $deliveryText = "Same-Day";
        if ($city->getData('next_day_delivery_block_status')) {
            $deliveryText = "Next-Day";
        } else if ($city->getData('any_day_delivery_block_status')) {
            $deliveryText = "Any-Day";
        }
        return str_replace('{delivery}', $deliveryText, $data);
    }
}