<?php

namespace GiftGroup\GeoPage\Ui\CategoryPage\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use GiftGroup\GeoPage\Model\CityFactory;

class CityName extends Column
{
    protected $cityFactory;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CityFactory $cityFactory,
        array $components = [],
        array $data = []
    ) {
        $this->cityFactory = $cityFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $city = $this->cityFactory->create()->load($item[$fieldName]);
                $item[$fieldName] = $city->getName();
            }
        }

        return $dataSource;
    }
}
