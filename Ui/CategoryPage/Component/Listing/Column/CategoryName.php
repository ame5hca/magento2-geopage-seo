<?php

namespace GiftGroup\GeoPage\Ui\CategoryPage\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Catalog\Model\ResourceModel\Category as CategoryResource;

class CategoryName extends Column
{
    protected $cityFactory;

    protected $categoryResource;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CategoryResource $categoryResource,
        array $components = [],
        array $data = []
    ) {
        $this->categoryResource = $categoryResource;
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
                $value = $this->getCategoryNameFromId(
                    $item[$fieldName],
                    $item['store_id']
                );
                $item[$fieldName] = $value;
            }
        }

        return $dataSource;
    }

    private function getCategoryNameFromId($categoryId, $storeId)
    {
        return $this->categoryResource->getAttributeRawValue(
            $categoryId,
            'name',
            $storeId
        );
    }
}
