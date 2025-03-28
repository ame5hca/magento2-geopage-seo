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
use GiftGroup\GeoPage\Model\Import\Helper\AdditionalDataProvider;
use GiftGroup\GeoPage\Model\Import\Page\DefaultPageGenerator;
use GiftGroup\GeoPage\Model\ResourceModel\States;
use GiftGroup\GeoPage\Model\ResourceModel\City;
use Magento\Store\Model\Store;
use GiftGroup\GeoPage\Model\Page\ProductsGenerator;
use Magento\Framework\Serialize\SerializerInterface;

class StatePage extends AbstractEntity
{
    const ENTITY_CODE = 'state_page_import';

    const TABLE = 'giftgroup_state_page';

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
        'country_id',
        'store_id'
    ];

    /**
     * Valid column names
     */
    protected $validColumnNames = [
        'name',
        'country_id',
        'meta_title',
        'meta_description',
        'meta_robot',
        'is_active',
        'store_id',
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
        'three_col_block_col3'
    ];

    protected $columnsToSkipOnDbSave = [
        'name',
        'country_id'
    ];
    
    protected $dynamicColumns = [
        'state_id',
        'product_ids'
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

    private $stateData = [];

    private $finalColumns = [];

    private $cityData = [];

    private $cityCodeForId = [];

    private $stateCodeForId = [];

    private $categoryUrlKeyForId = [];

    protected $productsGenerator;

    protected $serializer;

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
        AdditionalDataProvider $additionalDataProvider,
        ProductsGenerator $productsGenerator,
        SerializerInterface $serializer
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
        $this->productsGenerator = $productsGenerator;
        $this->serializer = $serializer;
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
        $stateName = $rowData['name'] ?? '';
        $countryId = $rowData['country_id'] ?? '';

        if (!$stateName) {
            $this->addRowError('NameIsRequired', $rowNum);
        }
        if (!$countryId) {
            $this->addRowError('CountryIsRequired', $rowNum);
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
            'NameIsRequired',
            __('Name is required.')
        );
        $this->addMessageTemplate(
            'CountryIsRequired',
            __('Country is required.')
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
        $this->initStateData();
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
        /* $rows = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                $this->validateRow($rowData, $rowNum);

                if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    $categoryId = $rowData[static::ENTITY_COLUMN];
                    $rows[] = $categoryId;
                }

                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                }
            }
        }

        if ($rows) {
            return $this->deleteEntityFinish(array_unique($rows));
        }

        return false; */
        return true;
    }

    /**
     * Save and replace entities
     *
     * @return void
     */
    private function saveAndReplaceEntity()
    {
        $behavior = $this->getBehavior();
        //$rows =  [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityList = [];

            foreach ($bunch as $rowNum => $row) {
                if (!$this->validateRow($row, $rowNum)) {
                    continue;
                }

                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);

                    continue;
                }
                $columnValues = [];
                foreach ($this->getAvailableColumns() as $columnKey) {   
                    if (in_array($columnKey, $this->columnsToSkipOnDbSave)) {
                        continue;
                    }                
                    $columnValues[$columnKey] = $this->getProcessedValue($row, $columnKey);
                }
                $stateIndex = $row['name'] . '|' . $row['country_id'];
                if (isset($this->stateData[$stateIndex])) {                   
                    $columnValues['state_id'] = $this->stateData[$stateIndex]['id'];
                }                

                //$entityList[$rowId][] = $columnValues;
                $entityList[] = $columnValues;
                $this->countItemsCreated += (int) !isset($row[static::ENTITY_COLUMN]);
                $this->countItemsUpdated += (int) isset($row[static::ENTITY_COLUMN]);
            }

            if (Import::BEHAVIOR_REPLACE === $behavior) {
                /* if ($rows && $this->deleteEntityFinish(array_unique($rows))) {
                    $this->saveEntityFinish($entityList);                   
                } */
            } elseif (Import::BEHAVIOR_APPEND === $behavior) {
                $this->saveEntityFinish($entityList);
            }
        }
        $this->createUrlRewrites();
    }

    private function getProcessedValue($row, $columnName)
    {
        $finalValue = $row[$columnName];
        switch ($columnName) {
            case 'name':
                $finalValue = $this->dataProcessor->formatCityName($finalValue);
                break;
            case 'country_id':
                $finalValue = $this->dataProcessor->removeSpaces($finalValue);
                break;
            case 'block3_category':
                $finalValue = $this->dataProcessor->getSerializedValue($finalValue);
                break;
            case 'store_id':
                $finalValue = $this->dataProcessor->convertToInt($finalValue);
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

            foreach ($entityData as $row) {
                $storeRows = $this->getStatePagesForStores($row);
                foreach ($storeRows as $storeRow) {
                    $rows[] = $storeRow;
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
                 * and city category page, the base url pattern is same ie, city/stateData/citycode/gift-basket/..
                 * So a pattern matching condition are prepared for each deleted city to with single pattern of
                 * a city, both the city page and categpry pages will be deleted.
                 */
                /* foreach ($cityCodes as $cityCode) {
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
                ); */

                return true;
            } catch (Exception $e) {
                return false;
            }
        }

        return false;
    }
    
    private function createUrlRewrites()
    {
        $rows = [];
        $statePages = $this->getStatePagesToCreateUrlRewrites();
        foreach ($statePages as $statePage) {
            $targetPath = sprintf(Config::STATE_PAGE_TARGET_URL, $statePage['id']);
            $requestPath = sprintf(
                Config::STATE_PAGE_URL, 
                $this->stateCodeForId[$statePage['state_id']]
            );
            $rows[] = [
                'entity_type' => '', 
                'entity_id' => 0, 
                'request_path' => $requestPath, 
                'target_path' => $targetPath, 
                'redirect_type' => 0, 
                'store_id' => $statePage['store_id']
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
        $this->finalColumns = array_merge($this->finalColumns, $this->dynamicColumns);
    }

    private function initStateData()
    {
        $this->stateData = $this->stateCodeForId = [];
        $states = $this->additionalDataProvider->getAllStates();
        foreach ($states as $state) {
            $stateIndex = $state['state_name'] . '|' . $state['country_code'];
            $this->stateData[$stateIndex] = ['state_code' => $state['state_code'], 'id' => $state['id']];
            $this->stateCodeForId[$state['id']] = $state['state_code'];
        }
    }
    
    private function getStatePagesToCreateUrlRewrites()
    {
        return $this->additionalDataProvider->getLastAddedStatePages();
    }
    
    private function getStatePagesForStores($row)
    {
        $storeSpecificStateRows = [];
        if ($row['store_id'] == Store::DEFAULT_STORE_ID) {
            $allStores = $this->additionalDataProvider->getAllStores();
            foreach($allStores as $storeId) {
                $row['store_id'] = $storeId;
                $products = $this->productsGenerator->generate(
                    $storeId, 
                    Config::CITY_PAGE_DEFAULT_PRODUCT_LIST_LIMIT
                );
                $row['product_ids'] = $this->serializer->serialize($products);
                $storeSpecificStateRows[] = $row;
            }
        } else {
            $products = $this->productsGenerator->generate(
                $row['store_id'], 
                Config::CITY_PAGE_DEFAULT_PRODUCT_LIST_LIMIT
            );
            $row['product_ids'] = $this->serializer->serialize($products);
            $storeSpecificStateRows[] = $row;
        }

        return $storeSpecificStateRows;
    }

    private function initAllStoresData()
    {
        $allStores = $this->additionalDataProvider->getAllStoresCode();
        $this->dataProcessor->setAllStores($allStores);
    }
}
