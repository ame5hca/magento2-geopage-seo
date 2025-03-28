<?php	

namespace GiftGroup\GeoPage\Model\Import;

use Exception;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\ImportExport\Helper\Data as ImportHelper;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Magento\ImportExport\Model\ResourceModel\Import\Data;
use GiftGroup\GeoPage\Model\Config;
use GiftGroup\GeoPage\Model\Import\Helper\DataProcessor;
use GiftGroup\GeoPage\Model\Import\Helper\BulkUrlRewrite;
use GiftGroup\GeoPage\Model\Import\Helper\BulkStateSaveHandler;
use GiftGroup\GeoPage\Model\Import\Page\DefaultPageGenerator;
use GiftGroup\GeoPage\Model\Import\Helper\AdditionalDataProvider;
use Magento\Store\Model\Store;
use GiftGroup\GeoPage\Model\ResourceModel\CityPage;

class City extends AbstractEntity
{
    const ENTITY_CODE = 'city_import';

    const TABLE = 'giftgroup_cities';

    const ENTITY_COLUMN = 'code';

    const URL_REWRITE_TABLE_REQUEST_PATH_COLUMN = 'request_path';

    const URL_REWRITE_TABLE = 'url_rewrite';

    /**
     * If we should check column names
     */
    protected $needColumnCheck = true;

    /**
     * Need to log in import history
     */
    protected $logInHistory = true;

    /**
     * Permanent entity columns.
     */
    protected $_permanentAttributes = [
        'name',
        'code',
        'country_id',
        'region'
    ];

    /**
     * Valid column names
     */
    protected $validColumnNames = [
        'name',
        'code',
        'country_id',
        'region',
        'categories',
        'meta_title',
        'meta_description',
        'meta_robot',
        'is_active',
        'store_id',
        'display_on_homepage',
        'page_store_id',
        'slider_block_status',
        'slider',
        'send_gift_block_status',
        'send_gift_block_content',
        'free_shipping_block_status',
        'free_shipping_block_content',
        'same_day_delivery_block_status',
        'same_day_delivery_block_content',
        'next_day_delivery_block_status',
        'next_day_delivery_block_content',
        'any_day_delivery_block_status',
        'any_day_delivery_block_title',
        'any_day_delivery_block_content',
        'popular_product_block_status',
        'popular_product_limit', 
        'review_product_block_status',
        'review_product_limit',
        'category_city_block_status',
        'category_city_page_limit',
        'related_city_block_status',
        'related_city_limit',
        'faq_block_status',
        'slider_id',
        'block2_status',
        'block2_content_title',
        'block2_content',
        'block3_status',
        'block3_title',
        'block3_category',
        'three_col_block_status',
        'three_col_block_col1',
        'three_col_block_col2',
        'three_col_block_col3',
        'generate_default_city_page',
        'generate_default_city_category_page',
        'generate_state_page'
    ];

    protected $columnsToSkipOnDbSave = [
        'generate_default_city_page',
        'generate_default_city_category_page',
        'generate_state_page'
    ];
    
    protected $columnsToSkipOnPageCreation = [
        'name',
        'code',
        'country_id',
        'region',
        'display_on_homepage',
        'page_store_id'
    ];

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var ResourceConnection
     */
    private $resource;

    private $dataProcessor;

    private $urlRewriteManager;

    private $stateSaveHandler;

    private $defaultPageGenerator;

    private $additionalDataProvider;

    private $finalColumns = [];

    private $allCities = [];

    private $allStates = [];

    private $categoryUrlKeyForId = [];

    /**
     * Courses constructor.
     *
     * @param JsonHelper $jsonHelper
     * @param ImportHelper $importExportData
     * @param Data $importData
     * @param ResourceConnection $resource
     * @param Helper $resourceHelper
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     */
    public function __construct(
        JsonHelper $jsonHelper,
        ImportHelper $importExportData,
        Data $importData,
        ResourceConnection $resource,
        Helper $resourceHelper,
        ProcessingErrorAggregatorInterface $errorAggregator,
        DataProcessor $dataProcessor,
        BulkUrlRewrite $urlRewriteManager,
        BulkStateSaveHandler $stateSaveHandler,
        DefaultPageGenerator $defaultPageGenerator,
        AdditionalDataProvider $additionalDataProvider
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->resource = $resource;
        $this->connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
        $this->dataProcessor = $dataProcessor;
        $this->urlRewriteManager = $urlRewriteManager;
        $this->stateSaveHandler = $stateSaveHandler;
        $this->defaultPageGenerator = $defaultPageGenerator;
        $this->additionalDataProvider = $additionalDataProvider;
        $this->initMessageTemplates();
    }

    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return static::ENTITY_CODE;
    }

    /**
     * Get available columns
     *
     * @return array
     */
    public function getValidColumnNames(): array
    {
        return $this->validColumnNames;
    }

    /**
     * Row validation
     *
     * @param array $rowData
     * @param int $rowNum
     *
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum): bool
    {
        $cityName = $rowData['name'] ?? '';
        $cityCode = $rowData['code'] ?? '';
        $cityCode = $this->dataProcessor->formatCode($cityCode);
        $countryId = $rowData['country_id'] ?? '';
        $region = $rowData['region'] ?? '';

        if (!$cityName) {
            $this->addRowError('CityNameIsRequired', $rowNum);
        }
        if (!$cityCode) {
            $this->addRowError('CityCodeIsRequired', $rowNum);
        }
        if (!$countryId) {
            $this->addRowError('CountryIsRequired', $rowNum);
        }
        if (!$region) {
            $this->addRowError('RegionIsRequired', $rowNum);
        }

        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }

        $this->_validatedRows[$rowNum] = true;

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * Init Error Messages
     */
    private function initMessageTemplates()
    {
        $this->addMessageTemplate(
            'CityNameIsRequired',
            __('City name cannot be empty.')
        );
        $this->addMessageTemplate(
            'CityCodeIsRequired',
            __('City code cannot be empty and should be unique.')
        );
        $this->addMessageTemplate(
            'CountryIsRequired',
            __('Country is required.')
        );
        $this->addMessageTemplate(
            'RegionIsRequired',
            __('Region is required.')
        );
    }

    /**
     * Import data
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function _importData(): bool
    {
        $this->initCategoryUrlKeyData();
        $this->initFinalColumnsForDbSave();
        $this->initAllStoresData();
        switch ($this->getBehavior()) {
            case Import::BEHAVIOR_DELETE:
                $this->deleteEntity();
                break;
            case Import::BEHAVIOR_REPLACE:
                $this->saveAndReplaceEntity();
                break;
            case Import::BEHAVIOR_APPEND:
                $this->saveAndReplaceEntity();
                break;
        }

        return true;
    }

    /**
     * Delete entities
     *
     * @return bool
     */
    private function deleteEntity(): bool
    {
        $rows = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                $this->validateRow($rowData, $rowNum);

                if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    $code = $rowData[static::ENTITY_COLUMN];
                    $rows[] = $code;
                }

                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                }
            }
        }

        if ($rows) {
            return $this->deleteEntityFinish(array_unique($rows));
        }

        return false;
    }

    /**
     * Save and replace entities
     *
     * @return void
     */
    private function saveAndReplaceEntity()
    {
        $behavior = $this->getBehavior();
        $rows =  $regions = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityList = $entityListForPageGeneration = $regions = [];

            foreach ($bunch as $rowNum => $row) {
                if (!$this->validateRow($row, $rowNum)) {
                    continue;
                }

                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);

                    continue;
                }

                $rowId = $this->getProcessedValue($row, static::ENTITY_COLUMN);
                $rows[] = $rowId;

                $columnValues = [];
                foreach ($this->getAvailableColumns() as $columnKey) {
                    if (in_array($columnKey, $this->columnsToSkipOnDbSave)) {
                        continue;
                    } 
                    $columnValues[$columnKey] = $this->getProcessedValue($row, $columnKey);
                }

                $regionArrayIndex = $row['region'] . '|' . $row['country_id'];
                if (!isset($regions[$regionArrayIndex])) {
                    $regions[$regionArrayIndex] = [
                        'state_name' => $row['region'], 
                        'country_code' => $row['country_id'], 
                        'magento_region_id' => $columnValues['region']
                    ];
                }

                $entityList[$rowId][] = $columnValues;
                $columnValues['region'] = $row['region'];
                $columnValues['generate_default_city_page'] = $row['generate_default_city_page'];
                $columnValues['generate_default_city_category_page'] = $row['generate_default_city_category_page'];
                $entityListForPageGeneration[$rowId][] = $columnValues;
                $this->countItemsCreated += (int) !isset($row[static::ENTITY_COLUMN]);
                $this->countItemsUpdated += (int) isset($row[static::ENTITY_COLUMN]);
            }

            if (Import::BEHAVIOR_REPLACE === $behavior) {
                if ($rows && $this->deleteEntityFinish(array_unique($rows))) {
                    $this->saveEntityFinish($entityList);
                }
            } elseif (Import::BEHAVIOR_APPEND === $behavior) {
                $this->saveEntityFinish($entityList);
            }
            $this->stateSaveHandler->save($regions);
            //$this->defaultPageGenerator->execute($entityListForPageGeneration);
            $this->generateDefaultPages($entityListForPageGeneration);
        }
    }

    private function getProcessedValue($row, $columnName)
    {
        $finalValue = $row[$columnName];
        switch ($columnName) {
            case 'name':
                $finalValue = $this->dataProcessor->removeRegionNameFromCityName(
                    $row['region'],
                    $finalValue
                );
                $finalValue = $this->dataProcessor->formatCityName($finalValue);
                break;
            case 'region':
                $finalValue = $this->dataProcessor->region($row);
                break;
            case 'code':
                $finalValue = $this->dataProcessor->formatCode($finalValue);
                break;
            case 'categories':
            case 'block3_category':
                $finalValue = $this->dataProcessor->getSerializedValue($finalValue);
                break;
            case 'page_store_id':
                if (empty($finalValue)) {
                    $finalValue = '["0"]';
                } else {
                    $finalValue = $this->dataProcessor->getSerializedValue($finalValue);
                }
                break;
            case 'store_id':
                $finalValue = $this->dataProcessor->convertToInt($finalValue);
                break;
            case static::ENTITY_COLUMN:
                $finalValue = $this->dataProcessor->getCityCode($finalValue);
                break;
            case 'meta_title':
            case 'meta_description':
            case 'meta_robot':
            case 'send_gift_block_content':
            case 'free_shipping_block_content':
            case 'same_day_delivery_block_content':
            case 'next_day_delivery_block_content':
            case 'any_day_delivery_block_title':
            case 'any_day_delivery_block_content':
            case 'popular_product_limit':
            case 'review_product_limit':
            case 'category_city_page_limit':
            case 'related_city_limit':
            case 'three_col_block_col1':
            case 'three_col_block_col2':
            case 'three_col_block_col3':
            case 'block2_content_title':
            case 'block2_content':
            case 'block3_title':
                if (empty($finalValue)) {
                    $finalValue = $this->dataProcessor->getDefaultValue(
                        $columnName,
                        $row['store_id']
                    );
                }                
                break;
            case 'slider':
                if (is_numeric($finalValue)) {
                    $finalValue = $this->dataProcessor->getSliderBlockDefaultContent(
                        $finalValue
                    );
                }
                break;
        }
        return $finalValue;
    }

    /**
     * Save entities
     *
     * @param array $entityData
     *
     * @return bool
     */
    private function saveEntityFinish(array $entityData): bool
    {
        if ($entityData) {
            $tableName = $this->connection->getTableName(static::TABLE);
            $rows = [];

            foreach ($entityData as $entityRows) {
                foreach ($entityRows as $row) {
                    $rows[] = $row;
                }
            }

            if ($rows) {
                $this->connection->insertOnDuplicate($tableName, $rows, $this->getFinalColumns());

                return true;
            }

            return false;
        }
    }

    /**
     * Delete entities
     *
     * @param array $entityIds
     *
     * @return bool
     */
    private function deleteEntityFinish(array $cityCodes): bool
    {
        if ($cityCodes) {
            try {
                $this->countItemsDeleted += $this->connection->delete(
                    $this->connection->getTableName(static::TABLE),
                    $this->connection->quoteInto(static::ENTITY_COLUMN . ' IN (?)', $cityCodes)
                );
                $urlRewritesReqPaths = [];
                /**
                 * After a city is deleted, then the related url rewrites of the city page,
                 * category page need to be deleted. So for a city, the url rewrite of the city page
                 * and category page can be identified by the pattern checking of the url. For the city page
                 * and city category page, the base url pattern is same ie, city/statecode/citycode/gift-basket/..
                 * So a pattern matching condition are prepared for each deleted city to with single pattern of
                 * a city, both the city page and categpry pages will be deleted.
                 */
                foreach ($cityCodes as $cityCode) {
                    $urlRewritesReqPaths[] = sprintf(
                        Config::PATTERN_FOR_URLREWRITE_OF_CITY_AND_CATEGORY, 
                        '%', 
                        $cityCode,
                        '%'
                    );
                }
                $this->connection->delete(
                    $this->connection->getTableName(static::URL_REWRITE_TABLE),
                    $this->connection->quoteInto(
                        static::URL_REWRITE_TABLE_REQUEST_PATH_COLUMN . ' IN (?)', 
                        $urlRewritesReqPaths
                    )
                );

                return true;
            } catch (Exception $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Get available columns
     *
     * @return array
     */
    private function getAvailableColumns(): array
    {
        return $this->validColumnNames;
    }

    private function getFinalColumns(): array
    {
        return $this->finalColumns;
    }
    
    private function initFinalColumnsForDbSave()
    {
        $this->finalColumns = [];
        $this->finalColumns = array_diff($this->getAvailableColumns(), $this->columnsToSkipOnDbSave);
    }

    private function getFinalColumnsForPage(): array
    {
        $finalColumns = $this->getFinalColumns();
        $finalColumns = array_diff($finalColumns, $this->columnsToSkipOnPageCreation);

        return $finalColumns;
    }
    
    private function getFinalColumnsForCityPage(): array
    {
        $finalColumns = $this->getFinalColumnsForPage();
        $finalColumns[] = 'city_id';
        $finalColumns[] = 'state_id';
        $finalColumns[] = 'city_name';
        $finalColumns[] = 'product_ids';

        return $finalColumns;
    }
    
    private function getFinalColumnsForCategoryPage(): array
    {
        $finalColumns = $this->getFinalColumnsForPage();
        $finalColumns[] = 'city_id';
        $finalColumns[] = 'state_id';
        $finalColumns[] = 'category_id';
        $finalColumns[] = 'product_ids';
        if(($key = array_search("categories", $finalColumns)) !== false) {
            unset($finalColumns[$key]);
        }

        return $finalColumns;
    }

    private function generateDefaultPages($entityData)
    {
        $cityPagerows = $categoryPageRows = [];
        $this->allCities = $this->additionalDataProvider->getAllCitiesByCodeAndId();
        $this->allStates = $this->additionalDataProvider->getAllStatesDataByIndexAndId();
        $allStores = $this->additionalDataProvider->getAllStores();
        foreach ($entityData as $entityRow) {
            foreach ($entityRow as $row) {
                $pageStoreIds = $this->dataProcessor->unserialize($row['page_store_id']);
                $tmpRow = $row;
                unset($tmpRow['page_store_id']);
                unset($tmpRow['display_on_homepage']);
                unset($tmpRow['code']);
                unset($tmpRow['country_id']);
                unset($tmpRow['region']);
                unset($tmpRow['generate_default_city_page']);
                unset($tmpRow['generate_default_city_category_page']);
                $tmpRow['city_name'] = $tmpRow['name'];
                unset($tmpRow['name']);
                $tmpRow['city_id'] = $this->allCities['by_code'][$row['code']];
                $stateIndex = $row['region'] . '|' . $row['country_id'];
                $tmpRow['state_id'] = $this->allStates['by_index'][$stateIndex]['id'];

                if ($row['generate_default_city_page']) {
                    if (in_array(Store::DEFAULT_STORE_ID, $pageStoreIds)) {
                        foreach ($allStores as $storeId) {
                           if ($storeId == Store::DEFAULT_STORE_ID) {
                                continue;
                           }
                           $tmpRow['store_id'] = $storeId;
                           $tmpRow['product_ids'] = $this->dataProcessor->getSerializedArray(
                                $this->additionalDataProvider->getProducts($storeId)
                            );
                           $cityPagerows[] = $tmpRow;
                        }
                    } else {
                        foreach ($pageStoreIds as $storeId) {
                            $tmpRow['store_id'] = $storeId;
                            $tmpRow['product_ids'] = $this->dataProcessor->getSerializedArray(
                                $this->additionalDataProvider->getProducts($storeId)
                            );
                            $cityPagerows[] = $tmpRow;
                        }
                    }
                }               


                if ($row['generate_default_city_category_page'] && $tmpRow['categories']) {
                    $categories = $tmpRow['categories'];                    
                    $categories = $this->dataProcessor->unserialize($categories);
                    unset($tmpRow['city_name']);
                    unset($tmpRow['categories']);
                    if (in_array(Store::DEFAULT_STORE_ID, $pageStoreIds)) {
                        foreach ($categories as $categoryId) {
                            $categoryId = $this->dataProcessor->removeSpaces($categoryId);
                            if (empty($categoryId)) {
                                continue;
                            }
                            foreach ($allStores as $storeId) {
                                if ($storeId == Store::DEFAULT_STORE_ID) {
                                     continue;
                                }
                                $tmpRow['store_id'] = $storeId;
                                $tmpRow['category_id'] = (int)$categoryId;
                                $tmpRow['product_ids'] = $this->dataProcessor->getSerializedArray(
                                    $this->additionalDataProvider->getProducts($storeId,$categoryId)
                                );
                                $categoryPageRows[] = $tmpRow;
                             }
                        }                        
                    } else {
                        foreach ($categories as $categoryId) {
                            $categoryId = $this->dataProcessor->removeSpaces($categoryId);
                            if (empty($categoryId)) {
                                continue;
                            }
                            foreach ($pageStoreIds as $storeId) {
                                $tmpRow['store_id'] = $storeId;
                                $tmpRow['category_id'] = (int)$categoryId;
                                $tmpRow['product_ids'] = $this->dataProcessor->getSerializedArray(
                                    $this->additionalDataProvider->getProducts($storeId,$categoryId)
                                );
                                $categoryPageRows[] = $tmpRow;
                            }
                        }
                    }
                    
                }
            }
        }

        if ($cityPagerows) {
            $this->connection->insertOnDuplicate(
                $this->connection->getTableName(CityPage::TABLE), 
                $cityPagerows, 
                $this->getFinalColumnsForCityPage()
            );
            $this->createUrlRewritesForCityPages();
        }
        
        if ($categoryPageRows) {
            $this->connection->insertOnDuplicate(
                $this->connection->getTableName(CategoryPage::TABLE), 
                $categoryPageRows, 
                $this->getFinalColumnsForCategoryPage()
            );
            $this->createUrlRewritesForCategoryPages();
        }
    }

    private function createUrlRewritesForCityPages()
    {
        $rows = [];
        $cityPages = $this->additionalDataProvider->getLastAddedCityPages();
        foreach ($cityPages as $cityPage) {
            $targetPath = sprintf(Config::CITY_PAGE_TARGET_URL, $cityPage['id']);
            $requestPath = sprintf(
                Config::CITY_PAGE_URL, 
                $this->allStates['by_id'][$cityPage['state_id']], 
                $this->allCities['by_id'][$cityPage['city_id']]
            );
            $rows[] = [
                'entity_type' => '', 
                'entity_id' => 0, 
                'request_path' => $requestPath, 
                'target_path' => $targetPath, 
                'redirect_type' => 0, 
                'store_id' => $cityPage['store_id']
            ];
        }
        if ($rows) {
            $this->connection->insertOnDuplicate(
                $this->connection->getTableName(static::URL_REWRITE_TABLE),
                $rows, 
                ['entity_type', 'entity_id', 'request_path', 'target_path', 'redirect_type', 'store_id']
            );           
        }
        return true;
    }
    
    private function createUrlRewritesForCategoryPages()
    {
        $rows = [];
        $categoryPages = $this->additionalDataProvider->getLastAddedCategoryPages();
        foreach ($categoryPages as $categoryPage) {
            $targetPath = sprintf(Config::CITY_CATEGORY_PAGE_TARGET_URL, $categoryPage['id']);
            $requestPath = sprintf(
                Config::CITY_CATEGORY_PAGE_URL, 
                $this->allStates['by_id'][$categoryPage['state_id']], 
                $this->allCities['by_id'][$categoryPage['city_id']],
                $this->categoryUrlKeyForId[$categoryPage['category_id']]
            );
            $rows[] = [
                'entity_type' => '', 
                'entity_id' => 0, 
                'request_path' => $requestPath, 
                'target_path' => $targetPath, 
                'redirect_type' => 0, 
                'store_id' => $categoryPage['store_id']
            ];
        }
        if ($rows) {
            $this->connection->insertOnDuplicate(
                $this->connection->getTableName(static::URL_REWRITE_TABLE),
                $rows, 
                ['entity_type', 'entity_id', 'request_path', 'target_path', 'redirect_type', 'store_id']
            );           
        }
        return true;
    }

    private function initCategoryUrlKeyData()
    {
        $this->categoryUrlKeyForId = [];
        $categoryCollection = $this->additionalDataProvider->getCategoryCollection();
        if ($categoryCollection) {
            foreach ($categoryCollection as $category) {
                $this->categoryUrlKeyForId[$category->getId()] = $category->getUrlKey();
            }
        }
    }

    private function initAllStoresData()
    {
        $allStores = $this->additionalDataProvider->getAllStoresCode();
        $this->dataProcessor->setAllStores($allStores);
    }
}
