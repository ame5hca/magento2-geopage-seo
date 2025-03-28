<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\City;

use GiftGroup\GeoPage\Model\CityFactory;
use GiftGroup\GeoPage\Model\Registry\City as CityRegistry;
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
 * Class responsible for the city edit form
 */
class Edit extends Action implements HttpGetActionInterface
{
    /**
     * @var CityRegistry
     */
    private CityRegistry $cityRegistry;

    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * @var CityFactory
     */
    private CityFactory $cityFactory;

    /**
     * Edit construct function
     *
     * @param Context $context
     * @param CityRegistry $cityRegistry
     * @param PageFactory $resultPageFactory
     * @param CityFactory $cityFactory
     * @return void
     */
    public function __construct(
        Context             $context,
        CityRegistry $cityRegistry,
        PageFactory         $resultPageFactory,
        CityFactory  $cityFactory
    ) {
        $this->cityRegistry = $cityRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->cityFactory = $cityFactory;
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
        $cityModel = $this->cityFactory->create();
        $this->cityRegistry->clear();
        if ($id) {
            try {
                $cityModel->load($id);
                if (!$cityModel->getId()) {
                    throw new LocalizedException(
                        __('This city is not exist.')
                    );
                }
            } catch (\Exception $ex) {
                $this->messageManager->addErrorMessage($ex->getMessage());
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->cityRegistry->setCity($cityModel);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(
            $cityModel->getId() ? __('Edit City - %1', $cityModel->getName()) : __('New City')
        );
        return $resultPage;
    }
}
