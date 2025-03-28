<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\City;

use GiftGroup\GeoPage\Model\StatesFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class responsible for deleting the city
 */
class Delete extends Action implements HttpPostActionInterface
{
    /**
     * @var StatesFactory
     */
    private StatesFactory $statesFactory;

    /**
     * Delete construct function
     *
     * @param Context $context
     * @param StatesFactory $statesFactory
     * @return void
     */
    public function __construct(
        Context            $context,
        StatesFactory $statesFactory
    ) {
        $this->statesFactory = $statesFactory;
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
                $stateModel = $this->statesFactory->create();
                $stateModel->load($id);
                $stateModel->delete();
                $this->messageManager->addSuccessMessage(
                    __('You deleted the state.')
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
            __('We can\'t find a state to delete.')
        );
        return $resultRedirect->setPath('*/*/');
    }
}
