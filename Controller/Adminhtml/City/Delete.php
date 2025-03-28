<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\City;

use GiftGroup\GeoPage\Model\CityFactory;
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
     * @var CityFactory
     */
    private CityFactory $cityFactory;

    /**
     * Delete construct function
     *
     * @param Context $context
     * @param CityFactory $cityFactory
     * @return void
     */
    public function __construct(
        Context            $context,
        CityFactory $cityFactory
    ) {
        $this->cityFactory = $cityFactory;
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
                $cityModel = $this->cityFactory->create();
                $cityModel->load($id);
                $cityModel->delete();
                $this->messageManager->addSuccessMessage(
                    __('You deleted the city.')
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
            __('We can\'t find a city to delete.')
        );
        return $resultRedirect->setPath('*/*/');
    }
}
