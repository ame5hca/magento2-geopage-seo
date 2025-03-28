<?php

namespace GiftGroup\GeoPage\Block\Adminhtml\City\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Backend\Model\UrlInterface;
use GiftGroup\GeoPage\Model\Registry\City as CityRegistry;

/**
 * Provide data to render the generate [age button
 */
class GeneratePage extends GenericButton implements ButtonProviderInterface
{
    private $cityRegistry;

    /**
     * GeneratePage construct function
     *
     * @param RequestInterface $request
     * @param UrlInterface $urlBuilder
     * @param CityRegistry $cityRegistry
     * @return void
     */
    public function __construct(
        RequestInterface $request,
        UrlInterface     $urlBuilder,
        CityRegistry $cityRegistry
    ) {
        parent::__construct($request, $urlBuilder);
        $this->cityRegistry = $cityRegistry;
    }

    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getId()) {
            $texts = $this->getTexts();
            $data = [
                'label' => $texts['label'],
                'class' => 'save secondary',
                'on_click' => 'deleteConfirm(\'' . $texts['confirm_message'] . '\', \'' . $this->getPageGenerateUrl() . '\', {"data": {"city_id": ' . $this->getId() . '}})',
                'sort_order' => 50,
            ];
        }
        return $data;
    }

    /**
     * URL to send generate city page
     *
     * @return string
     */
    public function getPageGenerateUrl(): string
    {
        return $this->getUrl('*/*/generatepage');
    }

    public function getTexts(): array
    {
        $texts = [
            'label' => __('Generate Page'),
            'confirm_message' => __('Based on the settings and contents under the "City Page Generation" tab, new pages will be generated for the city against each store view selected. So please recheck and verify the data configured under the tab.')
        ];
        $city = $this->cityRegistry->getCity();
        if ($city->getIsPageGenerated()) {
            $texts['label'] = __('Re-Generate Page');
            $texts['confirm_message'] = __('Based on the settings and contents under the "City Page Generation" tab, city pages will be re-generated Or new pages will be generated for the new store view selected. So please recheck and verify the data configured under the tab.');
        }
        return $texts;
    }
}
