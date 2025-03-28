<?php

namespace GiftGroup\GeoPage\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use GiftGroup\GeoPage\Model\DataProvider\CityPage;
use GiftGroup\GeoPage\Model\DataProvider\StatePage;

class City implements ArgumentInterface
{
    private $cityPageDataProvider;

    private $statePageDataProvider;

    public function __construct(
        CityPage $cityPageDataProvider,
        StatePage $statePageDataProvider
    ) {
        $this->cityPageDataProvider = $cityPageDataProvider;
        $this->statePageDataProvider = $statePageDataProvider;
    }

    public function getAllCities()
    {
        $data = [];
        $cityPages = $this->cityPageDataProvider->getAllCities();
        if ($cityPages->getSize()) {
            foreach ($cityPages as $cityPage) {
                $data[$cityPage->getData('state_id')][] = $cityPage;
            }
        }

        return $data;
    }
    
    public function getStatePage($stateId)
    {
        return $this->statePageDataProvider->getStatePageByStateId($stateId);
    }
}
