<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\StatePage;

use GiftGroup\GeoPage\Model\StatePageFactory;
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
     * @var StatePageFactory
     */
    private StatePageFactory $statePageFactory;

    /**
     * Delete construct function
     *
     * @param Context $context
     * @param StatePageFactory $statePageFactory
     * @return void
     */
    public function __construct(
        Context            $context,
        StatePageFactory $statePageFactory
    ) {
        $this->statePageFactory = $statePageFactory;
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
                $statePageModel = $this->statePageFactory->create();
                $statePageModel->load($id);
                $statePageModel->delete();
                $this->messageManager->addSuccessMessage(
                    __('You deleted the state page.')
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
            __('We can\'t find a state page to delete.')
        );
        return $resultRedirect->setPath('*/*/');
    }
}
