<?php

namespace GiftGroup\GeoPage\Ui\State\Component\Listing\Column\Actions;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class handling the actions of the grid view
 */
abstract class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    protected UrlInterface $urlBuilder;

    /**
     * Actions construct function
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array<mixed> $components
     * @param array<mixed> $data
     * @return void
     */
    public function __construct(
        ContextInterface   $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface       $urlBuilder,
        array              $components = [],
        array              $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Add the actions to the data source of grid.
     *
     * @param array<mixed> $dataSource
     * @return array<mixed>
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['id'])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                $this->getEditUrl(),
                                [
                                    'id' => $item['id']
                                ]
                            ),
                            'label' => __('Edit')
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                $this->getDeleteUrl(),
                                [
                                    'id' => $item['id']
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete Item'),
                                'message' => __('Are you sure you want to delete the item?')
                            ],
                            'post' => true
                        ],
                    ];
                }
            }
        }

        return $dataSource;
    }

    /**
     * Get the edit action url
     *
     * @return mixed
     */
    abstract public function getEditUrl();

    /**
     * Get the delete action url
     *
     * @return mixed
     */
    abstract public function getDeleteUrl();
}
