<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\CategoryPage;

use GiftGroup\GeoPage\Model\CityCategoryPageFactory;
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
     * @var CityCategoryPageFactory
     */
    private CityCategoryPageFactory $categoryPageFactory;

    /**
     * Delete construct function
     *
     * @param Context $context
     * @param CityCategoryPageFactory $categoryPageFactory
     * @return void
     */
    public function __construct(
        Context            $context,
        CityCategoryPageFactory $categoryPageFactory
    ) {
        $this->categoryPageFactory = $categoryPageFactory;
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
                $categoryPageModel = $this->categoryPageFactory->create();
                $categoryPageModel->load($id);
                $categoryPageModel->delete();
                $this->messageManager->addSuccessMessage(
                    __('You deleted the category page.')
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
            __('We can\'t find a category page to delete.')
        );
        return $resultRedirect->setPath('*/*/');
    }
}
