<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\City;

use GiftGroup\GeoPage\Model\ResourceModel\City\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use GiftGroup\GeoPage\Model\Page\Generator;
use GiftGroup\GeoPage\Logger\Logger;

/**
 * Class responsible for handling the mass city page generate functionality.
 */
class MassGeneratePage extends AbstractMassAction
{
    private $cityPageGenerator;

    public function __construct(
        Context              $context,
        Filter               $filter,
        CollectionFactory    $collectionFactory,
        Logger $logger,
        Generator $cityPageGenerator
    ) {
        parent::__construct($context, $filter, $collectionFactory, $logger);
        $this->cityPageGenerator = $cityPageGenerator;
    }

    protected function doAction($collection)
    {
        foreach ($collection as $city) {
            $this->cityPageGenerator->build($city);
        }
        return true;
    }
    
    protected function getSuccessMessage($collectionSize)
    {
        return __('A total of %1 city pages have been generated', $collectionSize);
    }
}
