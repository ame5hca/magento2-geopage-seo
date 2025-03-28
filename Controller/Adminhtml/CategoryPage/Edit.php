<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\CategoryPage;

use GiftGroup\GeoPage\Model\CityCategoryPageFactory;
use GiftGroup\GeoPage\Model\Registry\CategoryPage as CategoryPageRegistry;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class responsible for the category page edit form
 */
class Edit extends Action implements HttpGetActionInterface
{
    /**
     * @var CategoryPageRegistry
     */
    private CategoryPageRegistry $categoryPageRegistry;

    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * @var CityCategoryPageFactory
     */
    private CityCategoryPageFactory $categoryPageFactory;

    /**
     * Edit construct function
     *
     * @param Context $context
     * @param CategoryPageRegistry $categoryPageRegistry
     * @param PageFactory $resultPageFactory
     * @param CityCategoryPageFactory $categoryPageFactory
     * @return void
     */
    public function __construct(
        Context             $context,
        CategoryPageRegistry $categoryPageRegistry,
        PageFactory         $resultPageFactory,
        CityCategoryPageFactory  $categoryPageFactory
    ) {
        $this->categoryPageRegistry = $categoryPageRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->categoryPageFactory = $categoryPageFactory;
        parent::__construct($context);
    }

    /**
     * Execute function for edit form.
     *
     * @return ResponseInterface|Redirect|ResultInterface|Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $categoryPageModel = $this->categoryPageFactory->create();
        $this->categoryPageRegistry->clear();
        if ($id) {
            try {
                $categoryPageModel->load($id);
                if (!$categoryPageModel->getId()) {
                    throw new LocalizedException(
                        __('This category page is not exist.')
                    );
                }
            } catch (\Exception $ex) {
                $this->messageManager->addErrorMessage($ex->getMessage());
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->categoryPageRegistry->setCategoryPage($categoryPageModel);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(
            $categoryPageModel->getId() ? __('Edit Category Page - %1', $categoryPageModel->getCityName()) : __('New Category Page')
        );
        return $resultPage;
    }
}
