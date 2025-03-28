<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\StatePage;

use GiftGroup\GeoPage\Model\StatePageFactory;
use GiftGroup\GeoPage\Model\Registry\StatePage as StatePageRegistry;
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
 * Class responsible for the state page edit form
 */
class Edit extends Action implements HttpGetActionInterface
{
    /**
     * @var StatePageRegistry
     */
    private StatePageRegistry $statePageRegistry;

    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * @var StatePageFactory
     */
    private StatePageFactory $statePageFactory;

    /**
     * Edit construct function
     *
     * @param Context $context
     * @param StatePageRegistry $statePageRegistry
     * @param PageFactory $resultPageFactory
     * @param StatePageFactory $categoryPageFactory
     * @return void
     */
    public function __construct(
        Context             $context,
        StatePageRegistry $statePageRegistry,
        PageFactory         $resultPageFactory,
        StatePageFactory  $statePageFactory
    ) {
        $this->statePageRegistry = $statePageRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->statePageFactory = $statePageFactory;
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
        $statePageModel = $this->statePageFactory->create();
        $this->statePageRegistry->clear();
        if ($id) {
            try {
                $statePageModel->load($id);
                if (!$statePageModel->getId()) {
                    throw new LocalizedException(
                        __('This state page is not exist.')
                    );
                }
            } catch (\Exception $ex) {
                $this->messageManager->addErrorMessage($ex->getMessage());
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->statePageRegistry->setStatePage($statePageModel);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(
            $statePageModel->getId() ? __('Edit State Page - %1', $statePageModel->getCityName()) : __('New State Page')
        );
        return $resultPage;
    }
}
