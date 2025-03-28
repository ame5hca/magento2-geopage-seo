<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\State;

use GiftGroup\GeoPage\Model\StatesFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use GiftGroup\GeoPage\Model\DataProvider\CityLocation;

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
     * @var StatesFactory
     */
    protected StatesFactory $stateFactory;
    
    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    protected $locationDataProvider;

    /**
     * Save construct function
     *
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param StatesFactory $stateFactory
     * @param SerializerInterface $serializer
     * @return void
     */
    public function __construct(
        Context                $context,
        DataPersistorInterface $dataPersistor,
        StatesFactory     $stateFactory,
        SerializerInterface $serializer,
        CityLocation $locationDataProvider
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->stateFactory = $stateFactory;
        $this->serializer = $serializer;
        $this->locationDataProvider = $locationDataProvider;
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
            if (empty($data['state_name']) || empty($data['state_code']) || empty($data['country_code'])) {
                $this->messageManager->addErrorMessage(__('Missing required fields.'));
                return $resultRedirect->setPath('*/*/');
            }
            $id = $this->getRequest()->getParam('id');
            $stateModel = $this->stateFactory->create();
            if ($id) {
                try {
                    $stateModel->load($id);
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('This state no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }
            try {
                if ($id == '') {
                    unset($data['id']);
                }
                unset($data['form_key']);
                $data['state_code'] = preg_replace('/\s+/', '-', $data['state_code']);
                $data['magento_region_id'] = $this->locationDataProvider->getRegionIdFromName(
                    $data['state_name'],
                    $data['country_code']
                );
                $stateModel->setData($data);
                $stateModel->save();

                $this->_eventManager->dispatch('giftgroup_geopage_state_save_after', ['state' => $stateModel]);

                $this->messageManager->addSuccessMessage(__('You saved the state.'));
                $this->dataPersistor->clear('state');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $stateModel->getId()]);;
                }
                return $resultRedirect->setPath('*/*/');
            } catch (AlreadyExistsException $e) {
                $this->messageManager->addErrorMessage(
                    __('State with the same code already exist. Please check.')
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __($e->getMessage())
                );
            }
            $this->dataPersistor->set('state', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $stateModel->getId()]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
