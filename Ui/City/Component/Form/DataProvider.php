<?php

namespace GiftGroup\GeoPage\Ui\City\Component\Form;

use GiftGroup\GeoPage\Model\ResourceModel\City\Collection;
use GiftGroup\GeoPage\Model\ResourceModel\City\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;

/**
 * Class to provide data to the city form
 */
class DataProvider extends ModifierPoolDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected DataPersistorInterface $dataPersistor;

    /**
     * @var array<mixed>|null
     */
    protected $loadedData;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $cityCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array<mixed> $meta
     * @param array<mixed> $data
     * @param PoolInterface|null $pool
     * @return void
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $cityCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        $this->collection = $cityCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
    }

    /**
     * Get the form data
     *
     * @return mixed[]|null
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function getData(): ?array
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $city) {
            $this->loadedData[$city->getId()] = $city->getData();
            /**
             * Re written this line of code for converting the categories data
             * into array format from the serialized one. getCategories() in model
             * will do the unserialize functionality.
             */
            $this->loadedData[$city->getId()]['categories'] = $city->getCategories(); 
            $this->loadedData[$city->getId()]['page_store_id'] = $city->getPageStoreId(); 
            $this->loadedData[$city->getId()]['block3_category'] = $city->getBlock3Category(); 
        }

        $data = $this->dataPersistor->get('city');
        if (!empty($data)) {
            $city = $this->collection->getNewEmptyItem();
            $city->setData($data);
            $this->loadedData[$city->getId()] = $city->getData();
            $this->loadedData[$city->getId()]['categories'] = $city->getCategories();
            $this->loadedData[$city->getId()]['page_store_id'] = $city->getPageStoreId(); 
            $this->loadedData[$city->getId()]['block3_category'] = $city->getBlock3Category(); 
            $this->dataPersistor->clear('city');
        }

        return $this->loadedData;
    }
}
