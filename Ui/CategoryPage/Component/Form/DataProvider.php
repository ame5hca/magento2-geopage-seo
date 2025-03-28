<?php

namespace GiftGroup\GeoPage\Ui\CategoryPage\Component\Form;

use GiftGroup\GeoPage\Model\ResourceModel\CityCategoryPage\Collection;
use GiftGroup\GeoPage\Model\ResourceModel\CityCategoryPage\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;

/**
 * Class to provide data to the category page form
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
     * @param CollectionFactory $categoryPageCollectionFactory
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
        CollectionFactory $categoryPageCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        $this->collection = $categoryPageCollectionFactory->create();
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
        foreach ($items as $categoryPage) {
            $this->loadedData[$categoryPage->getId()] = $categoryPage->getData();
            /**
             * Re written this line of code for converting the categories data
             * into array format from the serialized one. getCategories() in model
             * will do the unserialize functionality.
             */
            $this->loadedData[$categoryPage->getId()]['block3_category'] = $categoryPage->getBlock3Category(); 
        }

        $data = $this->dataPersistor->get('category_page');
        if (!empty($data)) {
            $categoryPage = $this->collection->getNewEmptyItem();
            $categoryPage->setData($data);
            $this->loadedData[$categoryPage->getId()] = $categoryPage->getData();
            $this->loadedData[$categoryPage->getId()]['block3_category'] = $categoryPage->getBlock3Category(); 
            $this->dataPersistor->clear('category_page');
        }

        return $this->loadedData;
    }
}
