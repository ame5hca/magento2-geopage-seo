<?php

namespace GiftGroup\GeoPage\Block\Adminhtml\CityPage\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Provides the data for button rendering
 */
class BackButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Get the button data.
     *
     * @return array<mixed>
     */
    public function getButtonData(): array
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl(): string
    {
        return $this->getUrl('*/*/');
    }
}
