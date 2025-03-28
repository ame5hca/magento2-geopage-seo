<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\CityPage;

use GiftGroup\GeoPage\Model\CityPageFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class responsible for deleting the city page
 */
class Delete extends Action implements HttpPostActionInterface
{
    /**
     * @var CityPageFactory
     */
    private CityPageFactory $cityPageFactory;

    /**
     * Delete construct function
     *
     * @param Context $context
     * @param CityPageFactory $cityPageFactory
     * @return void
     */
    public function __construct(
        Context            $context,
        CityPageFactory $cityPageFactory
    ) {
        $this->cityPageFactory = $cityPageFactory;
        parent::__construct($context);
    }

    /**
     * Execute function for delete.
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $cityPageModel = $this->cityPageFactory->create();
                $cityPageModel->load($id);
                $cityPageModel->delete();
                $this->messageManager->addSuccessMessage(
                    __('You deleted the city page.')
                );
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    $e->getMessage()
                );
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(
            __('We can\'t find a city page to delete.')
        );
        return $resultRedirect->setPath('*/*/');
    }
}
