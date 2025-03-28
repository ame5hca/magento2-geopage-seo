<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\CategoryPage;

use GiftGroup\GeoPage\Model\CityCategoryPageFactory;
use GiftGroup\GeoPage\Model\CityFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use GiftGroup\GeoPage\Model\Page\ProductsGenerator;
use GiftGroup\GeoPage\Logger\Logger;

/**
 * Class responsible for saving the city
 */
class Save extends Action implements HttpPostActionInterface
{
    /**
     * @var DataPersistorInterface
     */
    protected DataPersistorInterface $dataPersistor;

    /**
     * @var CityCategoryPageFactory
     */
    protected CityCategoryPageFactory $categoryPageFactory;
    
    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    protected $productsGenerator;

    protected $cityFactory;

    protected $logger;

    /**
     * Save construct function
     *
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param CityCategoryPageFactory $categoryPageFactory
     * @param SerializerInterface $serializer
     * @return void
     */
    public function __construct(
        Context                $context,
        DataPersistorInterface $dataPersistor,
        CityCategoryPageFactory     $categoryPageFactory,
        SerializerInterface $serializer,
        ProductsGenerator $productsGenerator,
        CityFactory $cityFactory,
        Logger $logger
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->categoryPageFactory = $categoryPageFactory;
        $this->serializer = $serializer;
        $this->productsGenerator = $productsGenerator;
        $this->cityFactory = $cityFactory;
        $this->logger = $logger;
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
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            if (empty($data['city_id']) || !isset($data['store_id']) || empty($data['category_id'])) {
                $this->messageManager->addErrorMessage(__('Missing required fields.'));
                return $resultRedirect->setPath('*/*/');
            }
            $id = $this->getRequest()->getParam('id');
            $categoryPageModel = $this->categoryPageFactory->create();
            if ($id) {
                try {
                    $categoryPageModel->load($id);
                } catch (\Exception $e) {
                    $this->logger->critical('CategoryPageSaveErrorAdmin-PageModelLoadError : ' . $e->getMessage());
                    $this->messageManager->addErrorMessage(__('This category page no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }
            try {
                try {
                    $cityModel = $this->cityFactory->create()->load($data['city_id']);  
                    $state = $cityModel->getState(); 
                    if (!$state) {
                        $this->logger->critical('CategoryPageSaveErrorAdmin - city state not found');
                        $this->messageManager->addErrorMessage(__('Unable to find the state linked.'));
                        return $resultRedirect->setPath('*/*/');
                    }                 
                    $data['city_code'] = $cityModel->getCode();
                    $data['state_id'] = $state->getData('id');
                    $data['state_code'] = $state->getData('state_code');
                } catch (\Exception $e) {
                    $this->logger->critical('CategoryPageSaveErrorAdmin-CityModelLoadError : ' . $e->getMessage());
                    $this->messageManager->addErrorMessage(__('Selected city not available.Please see log.'));
                    return $resultRedirect->setPath('*/*/');
                }      
                if ($id == '') {
                    unset($data['id']);                    
                }
                $data['product_ids'] = $this->productsGenerator->generate(
                    $data['store_id'],
                    (isset($data['product_limit']) ? $data['product_limit'] : null),
                    $categoryPageModel->getData('category_id')
                );
                unset($data['form_key']);
                $categoryPageModel->setData($data);
                $categoryPageModel->save();

                $this->_eventManager->dispatch(
                    'giftgroup_geopage_category_page_save_after', 
                    ['category_page' => $categoryPageModel]
                );

                $this->messageManager->addSuccessMessage(__('You saved the category page.'));
                $this->dataPersistor->clear('category_page');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $categoryPageModel->getId()]);;
                }
                return $resultRedirect->setPath('*/*/');
            } catch (AlreadyExistsException $e) {
                $this->messageManager->addErrorMessage(
                    __('There is an existing page with this category and store.')
                );
            } catch (\Exception $e) {
                $this->logger->critical('CategoryPageSaveErrorAdmin-General : ' . $e->getMessage());
                $this->messageManager->addErrorMessage(
                    __($e->getMessage())
                );
            }
            $this->dataPersistor->set('category_page', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $categoryPageModel->getId()]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
