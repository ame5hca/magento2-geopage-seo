<?php

namespace GiftGroup\GeoPage\Ui\City\Component\Form;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;

/**
 * Class to provide data to the city import form
 */
class ImportFormDataProvider extends DataProvider
{
    /**
     * Get the form data
     *
     * @return mixed[]|null
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function getData(): ?array
    {
        return [];
    }
}
