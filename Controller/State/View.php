<?php

namespace GiftGroup\GeoPage\Controller\State;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use GiftGroup\GeoPage\Model\DataProvider\City;
use Magento\Framework\Controller\ResultFactory;
use GiftGroup\GeoPage\Model\Registry\CityPageView;
use GiftGroup\GeoPage\Model\Registry\StatePageView as StatePageRegistry;
use GiftGroup\GeoPage\Logger\Logger;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use GiftGroup\GeoPage\Model\Config;
use GiftGroup\GeoPage\Model\DataProvider\StatePage;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\RequestInterface;

class View implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

    private $cityDataProvider;

    private $statePageDataProvider;

    private $resultFactory;

    private $cityPageRegistry;

    private $statePageRegistry;

    private $logger;

    private $messageManager;

    private $urlBuilder;

    private $storeManager;

    private $request;

    public function __construct(
        PageFactory $pageFactory,
        City $cityDataProvider,
        ResultFactory $resultFactory,
        CityPageView $cityPageRegistry,
        Logger $logger,
        ManagerInterface $messageManager,
        UrlInterface $urlBuilder,
        StatePage $statePageDataProvider,
        StoreManagerInterface $storeManager,
        RequestInterface $request,
        StatePageRegistry $statePageRegistry
    ) {
        $this->pageFactory = $pageFactory;
        $this->cityDataProvider = $cityDataProvider;
        $this->resultFactory = $resultFactory;
        $this->cityPageRegistry = $cityPageRegistry;
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->urlBuilder = $urlBuilder;
        $this->statePageDataProvider = $statePageDataProvider;
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->statePageRegistry = $statePageRegistry;
    }

    public function execute()
    {        
        try {
            $statePage = $this->statePageDataProvider->getCurrentStatePage();
            if (!$statePage) {
                throw new LocalizedException(
                    __('State page not found.')
                );
            }
        } catch (\Exception $e) {
            $this->logger->critical('Error-StatePageViewControllerFE-StatePageLoad : ' . $e->getMessage());
            return $this->redirect404WithMessage();
        }
        if (!$statePage->getId() || !$statePage->getIsActive()) {
            return $this->redirect404WithMessage();
        }
        $statePageData = $this->statePageRegistry->clear()->setStatePage($statePage);
        $resultPage = $this->pageFactory->create();
        $resultPage = $this->generateBreadcrumbs($resultPage, $statePageData);
        $resultPage->getConfig()->getTitle()->set(
            $this->statePageDataProvider->getTitle($statePageData)
        );
        $resultPage->getConfig()->setDescription(
            $this->statePageDataProvider->getDescription($statePageData)
        );
        $resultPage->getConfig()->setMetaData(
            'robots', $this->statePageDataProvider->getMetaRobot($statePageData)
        );

        return $resultPage;
    }

    private function redirect404WithMessage()
    {
        $this->messageManager->addErrorMessage(
            __('The page you are requested is not available. Please contact support team.')
        );
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        $resultForward->forward('noroute');
        return $resultForward;
    }

    private function generateBreadcrumbs($resultPage, $statePageData)
    {
        $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs->addCrumb(
            'home',
            [
                'label' => __('Home'),
                'title' => __('Home'),
                'link' => $this->urlBuilder->getUrl()
            ]
        );
        $breadcrumbs->addCrumb(
            'areas',
            [
                'label' => __('Areas'),
                'title' => __('Areas'),
                'link' => $this->urlBuilder->getBaseUrl() . Config::CITY_HUB_PAGE_URL
            ]
        );
        $breadcrumbs->addCrumb(
            'category',
            [
                'label' => __($statePageData->getStatePage()->getData('state_name')),
                'title' => __($statePageData->getStatePage()->getData('state_name'))
            ]
        );
        return $resultPage;
    }    
}
