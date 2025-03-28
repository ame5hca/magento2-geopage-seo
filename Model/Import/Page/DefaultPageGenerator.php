<?php

namespace GiftGroup\GeoPage\Model\Import\Page;

use GiftGroup\GeoPage\Model\CityFactory;
use Magento\Framework\Exception\LocalizedException;
use GiftGroup\GeoPage\Model\Page\CityPage\Generator as CityPageGenerator;
use GiftGroup\GeoPage\Model\Page\CityCategoryPage\Generator as CategoryPageGenerator;
use GiftGroup\GeoPage\Model\Import\Helper\AdditionalDataProvider;
use GiftGroup\GeoPage\Model\Import\Helper\DataProcessor;
use Magento\Store\Model\Store;
use Magento\Framework\Serialize\SerializerInterface;

class DefaultPageGenerator
{
    private $cityFactory;

    private $cityPageGenerator;

    private $categoryPageGenerator;

    private $additionalDataProvider;

    private $dataProcessor;

    private $serializer;

    public function __construct(
        CityFactory $cityFactory,
        CityPageGenerator $cityPageGenerator,
        CategoryPageGenerator $categoryPageGenerator,
        AdditionalDataProvider $additionalDataProvider,
        DataProcessor $dataProcessor,
        SerializerInterface $serializer
    ) {
        $this->cityFactory = $cityFactory;
        $this->cityPageGenerator = $cityPageGenerator;
        $this->categoryPageGenerator = $categoryPageGenerator;
        $this->additionalDataProvider = $additionalDataProvider;
        $this->dataProcessor = $dataProcessor;
        $this->serializer = $serializer;
    }

    public function execute($entityData)
    {
        $cityPagerows = $categoryPageRows = [];
        $allCities = $this->additionalDataProvider->getAllCitiesByCodeAndId();
        $allStates = $this->additionalDataProvider->getAllStatesDataByIndexAndId();
        $allStores = $this->additionalDataProvider->getAllStores();
        foreach ($entityData as $entityRow) {
            foreach ($entityRow as $row) {
                $pageStoreIds = $this->serializer->unserialize($row['page_store_id']);
                $tmpRow = $row;
                unset($tmpRow['page_store_id']);
                unset($tmpRow['display_on_homepage']);
                unset($tmpRow['code']);
                unset($tmpRow['country_id']);
                unset($tmpRow['region']);
                $tmpRow['city_name'] = $tmpRow['name'];
                unset($tmpRow['name']);
                $tmpRow['city_id'] = $allCities['by_code'][$row['code']];
                $stateIndex = $row['region'] . '|' . $row['country_id'];
                $tmpRow['state_id'] = $allStates['by_index'][$stateIndex];
                if (in_array(Store::DEFAULT_STORE_ID, $pageStoreIds)) {
                    foreach ($allStores as $store) {
                       if ($store->getId() == Store::DEFAULT_STORE_ID) {
                            continue;
                       }
                       $tmpRow['store_id'] = $store->getId();
                       $cityPagerows[] = $tmpRow;
                    }
                } else {
                    foreach ($pageStoreIds as $storeId) {
                        $tmpRow['store_id'] = $storeId;
                        $cityPagerows[] = $tmpRow;
                    }
                }


                if ($tmpRow['categories']) {
                    $categories = $tmpRow['categories'];                    
                    $categories = $this->serializer->unserialize($categories);
                    unset($tmpRow['city_name']);
                    unset($tmpRow['categories']);
                    if (in_array(Store::DEFAULT_STORE_ID, $pageStoreIds)) {
                        foreach ($categories as $categoryId) {
                            foreach ($allStores as $store) {
                                if ($store->getId() == Store::DEFAULT_STORE_ID) {
                                     continue;
                                }
                                $tmpRow['store_id'] = $store->getId();
                                $tmpRow['category_id'] = $categoryId;
                                $categoryPageRows[] = $tmpRow;
                             }
                        }                        
                    } else {
                        foreach ($categories as $categoryId) {
                            foreach ($pageStoreIds as $storeId) {
                                $tmpRow['store_id'] = $storeId;
                                $tmpRow['category_id'] = $categoryId;
                                $categoryPageRows[] = $tmpRow;
                            }
                        }
                    }
                    
                }

            }
        }
        foreach ($entityData as $entiryRow) {
            foreach ($entiryRow as $row) {
                try {
                    $city = $this->cityFactory->create()->loadByCode($row['code']);
                } catch (\Exception $exception) {
                    throw new LocalizedException(
                        __('No city with the code exist.')
                    );
                }
                if (!empty($row['generate_default_city_page'])) {
                    $this->cityPageGenerator->fromCity($city);
                }
                if (!empty($row['generate_default_city_category_page'])) {
                    $this->categoryPageGenerator->fromCity($city);
                }
            }
        }        
    }
}