<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\CityPage;

use GiftGroup\GeoPage\Model\CityPageFactory;
use GiftGroup\GeoPage\Model\Registry\CityPage as CityPageRegistry;
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
 * Class responsible for the city page edit form
 */
class Edit extends Action implements HttpGetActionInterface
{
    /**
     * @var CityPageRegistry
     */
    private CityPageRegistry $cityPageRegistry;

    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * @var CityPageFactory
     */
    private CityPageFactory $cityPageFactory;

    /**
     * Edit construct function
     *
     * @param Context $context
     * @param CityPageRegistry $cityPageRegistry
     * @param PageFactory $resultPageFactory
     * @param CityPageFactory $cityPageFactory
     * @return void
     */
    public function __construct(
        Context             $context,
        CityPageRegistry $cityPageRegistry,
        PageFactory         $resultPageFactory,
        CityPageFactory  $cityPageFactory
    ) {
        $this->cityPageRegistry = $cityPageRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->cityPageFactory = $cityPageFactory;
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
        $cityPageModel = $this->cityPageFactory->create();
        $this->cityPageRegistry->clear();
        if ($id) {
            try {
                $cityPageModel->load($id);
                if (!$cityPageModel->getId()) {
                    throw new LocalizedException(
                        __('This city page is not exist.')
                    );
                }
            } catch (\Exception $ex) {
                $this->messageManager->addErrorMessage($ex->getMessage());
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->cityPageRegistry->setCityPage($cityPageModel);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(
            $cityPageModel->getId() ? __('Edit City Page - %1', $cityPageModel->getCityName()) : __('New City Page')
        );
        return $resultPage;
    }
}
