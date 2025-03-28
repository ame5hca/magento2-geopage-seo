<?php

namespace GiftGroup\GeoPage\Ui\City\Component\Form;

use GiftGroup\GeoPage\Model\ResourceModel\CityPage\Collection;
use GiftGroup\GeoPage\Model\ResourceModel\CityPage\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;

/**
 * Class to provide data to the city page form
 */
class CityPageDataProvider extends ModifierPoolDataProvider
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
     * @param CollectionFactory $cityPageCollectionFactory
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
        CollectionFactory $cityPageCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        $this->collection = $cityPageCollectionFactory->create();
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
        foreach ($items as $cityPage) {
            $this->loadedData[$cityPage->getId()] = $cityPage->getData();
            /**
             * Re written this line of code for converting the categories data
             * into array format from the serialized one. getCategories() in model
             * will do the unserialize functionality.
             */
            $this->loadedData[$cityPage->getId()]['categories'] = $cityPage->getCategories(); 
            $this->loadedData[$cityPage->getId()]['block3_category'] = $cityPage->getBlock3Category(); 
        }

        $data = $this->dataPersistor->get('city_page');
        if (!empty($data)) {
            $cityPage = $this->collection->getNewEmptyItem();
            $cityPage->setData($data);
            $this->loadedData[$cityPage->getId()] = $cityPage->getData();
            $this->loadedData[$cityPage->getId()]['categories'] = $cityPage->getCategories();
            $this->loadedData[$cityPage->getId()]['block3_category'] = $cityPage->getBlock3Category();
            $this->dataPersistor->clear('city_page');
        }

        return $this->loadedData;
    }
}
