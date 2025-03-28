<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\City;

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
     * @var CityFactory
     */
    protected CityFactory $cityFactory;
    
    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * Save construct function
     *
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param CityFactory $cityFactory
     * @param SerializerInterface $serializer
     * @return void
     */
    public function __construct(
        Context                $context,
        DataPersistorInterface $dataPersistor,
        CityFactory     $cityFactory,
        SerializerInterface $serializer
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->cityFactory = $cityFactory;
        $this->serializer = $serializer;
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
            if (empty($data['name']) || empty($data['code'])) {
                $this->messageManager->addErrorMessage(__('Missing required fields.'));
                return $resultRedirect->setPath('*/*/');
            }
            $id = $this->getRequest()->getParam('id');
            $cityModel = $this->cityFactory->create();
            if ($id) {
                try {
                    $cityModel->load($id);
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('This city no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }
            try {
                if ($id == '') {
                    unset($data['id']);
                }
                unset($data['form_key']);
                $data['code'] = preg_replace('/\s+/', '-', $data['code']);
                if (!empty($data['categories'])) {
                    $data['categories'] = $this->serializer->serialize($data['categories']);
                }
                $cityModel->setData($data);
                $cityModel->save();

                $this->_eventManager->dispatch('giftgroup_geopage_city_save_after', ['city' => $cityModel]);

                $this->messageManager->addSuccessMessage(__('You saved the city.'));
                $this->dataPersistor->clear('city');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $cityModel->getId()]);;
                }
                return $resultRedirect->setPath('*/*/');
            } catch (AlreadyExistsException $e) {
                $this->messageManager->addErrorMessage(
                    __('City with the same code already exist. Please check.')
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __($e->getMessage())
                );
            }
            $this->dataPersistor->set('city', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $cityModel->getId()]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
