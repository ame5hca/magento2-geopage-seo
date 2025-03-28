<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\State;

/**
 * Class responsible for handling the mass delete functionality.
 */
class MassDelete extends AbstractMassAction
{
    protected function doAction($collection)
    {
        foreach ($collection as $item) {
            $item->delete();
        }
        return true;
    }
    
    protected function getSuccessMessage($collectionSize)
    {
        return __('A total of %1 records have been deleted', $collectionSize);
    }
}
