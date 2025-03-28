<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\City;

use GiftGroup\GeoPage\Model\CityFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use GiftGroup\GeoPage\Logger\Logger;
use GiftGroup\GeoPage\Model\Page\Generator as CityPageGenerator;

/**
 * Class responsible for generating city page
 */
class GeneratePage extends Action implements HttpPostActionInterface
{
    /**
     * @var CityFactory
     */
    protected CityFactory $cityFactory;
    
    protected $cityPageGenerator;

    protected $logger;

    /**
     * Save construct function
     *
     * @param Context $context
     * @param CityFactory $cityFactory
     * @return void
     */
    public function __construct(
        Context                $context,
        CityFactory     $cityFactory,
        Logger $logger,
        CityPageGenerator $cityPageGenerator
    ) {
        $this->cityFactory = $cityFactory;
        $this->logger = $logger;
        $this->cityPageGenerator = $cityPageGenerator;
        parent::__construct($context);
    }

    /**
     * Execute function
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $cityId = $this->getRequest()->getPost('city_id', null);
        if (!$cityId) {
            $this->messageManager->addErrorMessage(__('City id is missing'));
            return $resultRedirect->setPath('*/*/');
        }
        try {
            $city = $this->cityFactory->create()->load($cityId);
            $this->cityPageGenerator->build($city);
            $this->messageManager->addSuccessMessage(
                __('%1 page generated successfully.', $city->getName())
            );  
        } catch (\Exception $e) {
            $this->logger->critical('CityPageGenerateError: ' . $e->getMessage());
            $this->messageManager->addErrorMessage(
                __('Something went wrong while generating the page. Please see the log.')
            );            
        }
        return $resultRedirect->setPath('*/*/edit', ['id' => $cityId]);
    }
}
