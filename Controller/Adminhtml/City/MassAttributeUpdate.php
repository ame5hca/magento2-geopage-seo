<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\City;

class MassAttributeUpdate extends AbstractMassAction
{
    protected function doAction($collection)
    {
        $homepageShowStatus = $this->getRequest()->getParam('show_in_homepage', 0);
        foreach ($collection as $item) {
            $item->setDisplayOnHomepage($homepageShowStatus);
            $item->save();
        }
        return true;
    }
    
    protected function getSuccessMessage($collectionSize)
    {
        return __('A total of %1 records have been updated', $collectionSize);
    }
}
