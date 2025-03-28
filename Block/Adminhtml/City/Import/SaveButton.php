<?php

namespace GiftGroup\GeoPage\Block\Adminhtml\City\Import;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Provide the data to render the import save button
 */
class SaveButton implements ButtonProviderInterface
{
    /**
     * Get button data
     *
     * @return array<mixed>
     */
    public function getButtonData(): array
    {
        return [
            'label' => __('Import'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
