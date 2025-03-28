<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\State;

use GiftGroup\GeoPage\Model\StatesFactory;
use GiftGroup\GeoPage\Model\Registry\State as StateRegistry;
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
     * @var StateRegistry
     */
    private StateRegistry $stateRegistry;

    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * @var StatesFactory
     */
    private StatesFactory $statesFactory;

    /**
     * Edit construct function
     *
     * @param Context $context
     * @param StateRegistry $stateRegistry
     * @param PageFactory $resultPageFactory
     * @param StatesFactory $statesFactory
     * @return void
     */
    public function __construct(
        Context             $context,
        StateRegistry $stateRegistry,
        PageFactory         $resultPageFactory,
        StatesFactory  $statesFactory
    ) {
        $this->stateRegistry = $stateRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->statesFactory = $statesFactory;
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
        $stateModel = $this->statesFactory->create();
        $this->stateRegistry->clear();
        if ($id) {
            try {
                $stateModel->load($id);
                if (!$stateModel->getId()) {
                    throw new LocalizedException(
                        __('This state is not exist.')
                    );
                }
            } catch (\Exception $ex) {
                $this->messageManager->addErrorMessage($ex->getMessage());
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->stateRegistry->setState($stateModel);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(
            $stateModel->getId() ? __('Edit State - %1', $stateModel->getName()) : __('New State')
        );
        return $resultPage;
    }
}
