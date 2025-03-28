<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\CategoryPage;

use Exception;
use GiftGroup\GeoPage\Model\ResourceModel\CityCategoryPage\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use GiftGroup\GeoPage\Logger\Logger;

/**
 * Common class for the mass action operations
 */
abstract class AbstractMassAction extends Action implements HttpPostActionInterface
{
    /**
     * @var Filter
     */
    protected Filter $filter;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    protected $logger;

    /**
     * AbstractMassAction construct function
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @return void
     */
    public function __construct(
        Context              $context,
        Filter               $filter,
        CollectionFactory    $collectionFactory,
        Logger $logger
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return Redirect
     * @throws LocalizedException|Exception
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();

            $this->doAction($collection);

            $this->messageManager->addSuccessMessage($this->getSuccessMessage($collectionSize));
        } catch (Exception $ex) {
            $this->logger->critical('CategoryPageMassActionError: ' . $ex->getMessage());
            $this->messageManager->addErrorMessage(
                __($ex->getMessage())
            );
        }
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * In this function add the exact code for the operation.
     */
    abstract protected function doAction($collection);

    /**
     * Here define the success message.
     *
     * @param int $collectionSize
     * @return string
     */
    abstract protected function getSuccessMessage($collectionSize);
}
