<?php

namespace GiftGroup\GeoPage\Ui\State\Component\Form;

use GiftGroup\GeoPage\Model\ResourceModel\StatePage\Collection;
use GiftGroup\GeoPage\Model\ResourceModel\StatePage\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;

/**
 * Class to provide data to the state page form
 */
class StatePageDataProvider extends ModifierPoolDataProvider
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
     * @param CollectionFactory $statePageCollectionFactory
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
        CollectionFactory $statePageCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        $this->collection = $statePageCollectionFactory->create();
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
        foreach ($items as $statePage) {
            $this->loadedData[$statePage->getId()] = $statePage->getData();
            $this->loadedData[$statePage->getId()]['block3_category'] = $statePage->getBlock3Category(); 
        }
        $data = $this->dataPersistor->get('state_page');
        if (!empty($data)) {
            $statePage = $this->collection->getNewEmptyItem();
            $statePage->setData($data);
            $this->loadedData[$statePage->getId()] = $statePage->getData();
            $this->loadedData[$statePage->getId()]['block3_category'] = $statePage->getBlock3Category();
            $this->dataPersistor->clear('state_page');
        }

        return $this->loadedData;
    }
}
