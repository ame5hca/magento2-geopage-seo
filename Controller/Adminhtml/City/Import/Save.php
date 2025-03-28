<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\City\Import;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;

/**
 * Class responsible for city importing
 */
class Save extends Action implements HttpPostActionInterface
{

    /**
     * Execute function to display grid page
     *
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $fileInfo = $this->getRequest()->getFiles();
        return $fileInfo;
    }
}
