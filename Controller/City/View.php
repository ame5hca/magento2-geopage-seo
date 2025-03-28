<?php

namespace GiftGroup\GeoPage\Controller\City;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use GiftGroup\GeoPage\Model\DataProvider\City;
use GiftGroup\GeoPage\Model\DataProvider\CityPage;
use Magento\Framework\Controller\ResultFactory;
use GiftGroup\GeoPage\Model\Registry\CityPageView;
use GiftGroup\GeoPage\Logger\Logger;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use GiftGroup\GeoPage\Model\Config;

/**
 * Class Index
 */
class View implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

    private $cityDataProvider;

    private $cityPageDataProvider;

    private $resultFactory;

    private $cityPageRegistry;

    private $logger;

    private $messageManager;

    private $urlBuilder;

    /**
     * @param PageFactory $pageFactory
     */
    public function __construct(
        PageFactory $pageFactory,
        City $cityDataProvider,
        ResultFactory $resultFactory,
        CityPageView $cityPageRegistry,
        Logger $logger,
        ManagerInterface $messageManager,
        UrlInterface $urlBuilder,
        CityPage $cityPageDataProvider
    ) {
        $this->pageFactory = $pageFactory;
        $this->cityDataProvider = $cityDataProvider;
        $this->resultFactory = $resultFactory;
        $this->cityPageRegistry = $cityPageRegistry;
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->urlBuilder = $urlBuilder;
        $this->cityPageDataProvider = $cityPageDataProvider;
    }

    public function execute()
    {
        try {
            $cityPage = $this->cityPageDataProvider->getCurrentCityPage();
            if (!$cityPage) {
                throw new LocalizedException(
                    __('Page not found.')
                );
            }
        } catch (\Exception $e) {
            $this->logger->critical('Error-CityViewControllerFE-CityPageLoad : ' . $e->getMessage());
            return $this->redirect404WithMessage();
        }
        try {
            $city = $this->cityDataProvider->getCityById($cityPage->getData('city_id'));
            if (!$city) {
                throw new LocalizedException(
                    __('Page not found. Something went wrong while loading the page.')
                );
            }
        } catch (\Exception $e) {
            $this->logger->critical('Error-CityViewControllerFE-CityLoad : ' . $e->getMessage());
            return $this->redirect404WithMessage();
        }
        if (!$city->getId() || !$city->getIsActive() || $cityPage == null || !$cityPage->getIsActive()) {
            return $this->redirect404WithMessage();
        }
        $cityPageRegistry = $this->cityPageRegistry->clear()->setCity($city)->setCityPage($cityPage);
        $resultPage = $this->pageFactory->create();
        $resultPage = $this->generateBreadcrumbs($resultPage, $city);
        $resultPage->getConfig()->getTitle()->set(
            $this->cityPageDataProvider->getTitle($cityPageRegistry)
        );
        $resultPage->getConfig()->setDescription(
            $this->cityPageDataProvider->getDescription($cityPageRegistry)
        );
        $resultPage->getConfig()->setMetaData(
            'robots', 
            $this->cityPageDataProvider->getMetaRobot($cityPageRegistry)
        );

        return $resultPage;
    }

    private function redirect404WithMessage()
    {
        $this->messageManager->addErrorMessage(
            __('The city page you are requested is not available. Please contact support team.')
        );
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        $resultForward->forward('noroute');
        return $resultForward;
    }

    private function generateBreadcrumbs($resultPage, $city)
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
        $cityBreadcrumbName = $city->getName() . ' ' . 'Gift Baskets';
        $breadcrumbs->addCrumb(
            'city',
            [
                'label' => __($cityBreadcrumbName),
                'title' => __($cityBreadcrumbName)
            ]
        );
        return $resultPage;
    }

    
}
