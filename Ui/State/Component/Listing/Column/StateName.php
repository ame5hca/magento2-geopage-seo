<?php

namespace GiftGroup\GeoPage\Ui\State\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use GiftGroup\GeoPage\Model\StatesFactory;

class StateName extends Column
{
    protected $statesFactory;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        StatesFactory $statesFactory,
        array $components = [],
        array $data = []
    ) {
        $this->statesFactory = $statesFactory;
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
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        try {
            $state = $this->statesFactory->create()->load($item['state_id']);
            return $state->getData('state_name');
        } catch (\Exception $e) {
            return '';
        }
    }
}
