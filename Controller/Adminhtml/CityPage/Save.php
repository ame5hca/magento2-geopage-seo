<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\CityPage;

use GiftGroup\GeoPage\Model\CityPageFactory;
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
     * @var CityPageFactory
     */
    protected CityPageFactory $cityPageFactory;
    
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
     * @param CityPageFactory $cityPageFactory
     * @param SerializerInterface $serializer
     * @return void
     */
    public function __construct(
        Context                $context,
        DataPersistorInterface $dataPersistor,
        CityPageFactory     $cityPageFactory,
        SerializerInterface $serializer,
        ProductsGenerator $productsGenerator,
        CityFactory $cityFactory,
        Logger $logger
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->cityPageFactory = $cityPageFactory;
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
        $data['city_code'] = '';
        if ($data) {
            if (empty($data['city_id']) || empty($data['store_id'])) {
                $this->messageManager->addErrorMessage(__('Missing required fields.'));
                return $resultRedirect->setPath('*/*/');
            }
            $id = $this->getRequest()->getParam('id');
            $cityPageModel = $this->cityPageFactory->create();
            if ($id) {
                try {
                    $cityPageModel->load($id);
                } catch (\Exception $e) {
                    $this->logger->critical('CityPageSaveErrorAdmin-PageModelLoadError : ' . $e->getMessage());
                    $this->messageManager->addErrorMessage(__('This city page no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }
            try {
                try {
                    $cityModel = $this->cityFactory->create()->load($data['city_id']);                    
                    $data['city_code'] = $cityModel->getCode();
                } catch (\Exception $e) {
                    $this->logger->critical('CityPageSaveErrorAdmin-CityModelLoadError : ' . $e->getMessage());
                    $this->messageManager->addErrorMessage(__('Selected city not available.Please see log.'));
                    return $resultRedirect->setPath('*/*/');
                }      
                if ($id == '') {
                    unset($data['id']);
                    $data['city_name'] = $cityModel->getName();                    
                }
                $data['product_ids'] = $this->productsGenerator->generate(
                    $data['store_id'],
                    (isset($data['product_limit']) ? $data['product_limit'] : null)
                );
                unset($data['form_key']);
                $cityPageModel->setData($data);
                $cityPageModel->setCityCode($data['city_code']);
                $cityPageModel->save();

                $this->_eventManager->dispatch(
                    'giftgroup_geopage_city_page_save_after', 
                    ['city_page' => $cityPageModel]
                );

                $this->messageManager->addSuccessMessage(__('You saved the city page.'));
                $this->dataPersistor->clear('city_page');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $cityPageModel->getId()]);;
                }
                return $resultRedirect->setPath('*/*/');
            } catch (AlreadyExistsException $e) {
                $this->messageManager->addErrorMessage(
                    __('There is an existing page with this city and store.')
                );
            } catch (\Exception $e) {
                $this->logger->critical('CityPageSaveErrorAdmin-General : ' . $e->getMessage());
                $this->messageManager->addErrorMessage(
                    __($e->getMessage())
                );
            }
            $this->dataPersistor->set('city_page', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $cityPageModel->getId()]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
