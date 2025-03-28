<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\CityPage;

class MassStatus extends AbstractMassAction
{
    protected function doAction($collection)
    {
        $status = $this->getRequest()->getParam('status', 0);
        foreach ($collection as $item) {
            $item->setIsActive($status);
            $item->save();
        }
        return true;
    }
    
    protected function getSuccessMessage($collectionSize)
    {
        $status = $this->getRequest()->getParam('status', 0);
        $status = $status == 1 ? 'enabled' : 'disabled';
        return __('A total of %1 records have been %2', $collectionSize, $status);
    }
}
