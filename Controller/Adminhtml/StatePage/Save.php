<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\StatePage;

use GiftGroup\GeoPage\Model\StatePageFactory;
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
 * Class responsible for saving the state
 */
class Save extends Action implements HttpPostActionInterface
{
    /**
     * @var DataPersistorInterface
     */
    protected DataPersistorInterface $dataPersistor;

    /**
     * @var StatePageFactory
     */
    protected StatePageFactory $statePageFactory;

    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    protected $productsGenerator;

    protected $logger;

    /**
     * Save construct function
     *
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param StatePageFactory $statePageFactory
     * @param SerializerInterface $serializer
     * @return void
     */
    public function __construct(
        Context                $context,
        DataPersistorInterface $dataPersistor,
        StatePageFactory     $statePageFactory,
        SerializerInterface $serializer,
        ProductsGenerator $productsGenerator,
        Logger $logger
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->statePageFactory = $statePageFactory;
        $this->serializer = $serializer;
        $this->productsGenerator = $productsGenerator;
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
            if (empty($data['state_id']) || empty($data['store_id'])) {
                $this->messageManager->addErrorMessage(__('Missing required fields.'));
                return $resultRedirect->setPath('*/*/');
            }
            $id = $this->getRequest()->getParam('id');
            $statePageModel = $this->statePageFactory->create();
            if ($id) {
                try {
                    $statePageModel->load($id);
                } catch (\Exception $e) {
                    $this->logger->critical('StatePageSaveErrorAdmin-PageModelLoadError : ' . $e->getMessage());
                    $this->messageManager->addErrorMessage(__('This state page no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }
            try {
                if ($id == '') {
                    unset($data['id']);                    
                }
                $data['product_ids'] = $this->productsGenerator->generate(
                    $data['store_id'],
                    (isset($data['product_limit']) ? $data['product_limit'] : null)
                );
                unset($data['form_key']);
                $statePageModel->setData($data);
                $statePageModel->save();

                $this->_eventManager->dispatch(
                    'giftgroup_geopage_state_page_save_after',
                    ['state_page' => $statePageModel]
                );

                $this->messageManager->addSuccessMessage(__('You saved the state page.'));
                $this->dataPersistor->clear('state_page');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $statePageModel->getId()]);;
                }
                return $resultRedirect->setPath('*/*/');
            } catch (AlreadyExistsException $e) {
                $this->messageManager->addErrorMessage(
                    __('There is an existing page with this state code and store.')
                );
            } catch (\Exception $e) {
                $this->logger->critical('StatePageSaveErrorAdmin-General : ' . $e->getMessage());
                $this->messageManager->addErrorMessage(
                    __($e->getMessage())
                );
            }
            $this->dataPersistor->set('state_page', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $statePageModel->getId()]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
