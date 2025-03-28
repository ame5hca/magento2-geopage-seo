<?php

namespace GiftGroup\GeoPage\Controller\CityCategory;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use GiftGroup\GeoPage\Model\DataProvider\City;
use Magento\Framework\Controller\ResultFactory;
use GiftGroup\GeoPage\Model\Registry\CityPageView;
use GiftGroup\GeoPage\Model\Registry\CategoryPageView;
use GiftGroup\GeoPage\Logger\Logger;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use GiftGroup\GeoPage\Model\Config;
use GiftGroup\GeoPage\Model\DataProvider\CategoryPage;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\RequestInterface;

class View implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

    private $cityDataProvider;

    private $categoryDataProvider;

    private $resultFactory;

    private $cityPageRegistry;

    private $categoryPageRegistry;

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
        CategoryPage $categoryDataProvider,
        StoreManagerInterface $storeManager,
        RequestInterface $request,
        CategoryPageView $categoryPageRegistry
    ) {
        $this->pageFactory = $pageFactory;
        $this->cityDataProvider = $cityDataProvider;
        $this->resultFactory = $resultFactory;
        $this->cityPageRegistry = $cityPageRegistry;
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->urlBuilder = $urlBuilder;
        $this->categoryDataProvider = $categoryDataProvider;
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->categoryPageRegistry = $categoryPageRegistry;
    }

    public function execute()
    {        
        try {
            $category = $this->categoryDataProvider->getCurrentCategoryPage();
            if (!$category) {
                throw new LocalizedException(
                    __('Category data is empty.')
                );
            }
        } catch (\Exception $e) {
            $this->logger->critical('Error-CityCategoryViewControllerFE-CategoryLoad : ' . $e->getMessage());
            return $this->redirect404WithMessage();
        }
        try {
            $city = $this->cityDataProvider->getCityById($category->getData('city_id'));
            if (!$city) {
                throw new LocalizedException(
                    __('City data is empty.')
                );
            }
        } catch (\Exception $e) {
            $this->logger->critical('Error-CityCategoryViewControllerFE-CityLoad : ' . $e->getMessage());
            return $this->redirect404WithMessage();
        }
        if (!$city->getId() || !$city->getIsActive() || $category == null || !$category->getData('is_active')) {
            return $this->redirect404WithMessage();
        }
        //$cityPageData = $this->cityPageRegistry->clear()->setCity($city)->setCategory($category);
        $cityPageData = $this->categoryPageRegistry->clear()->setCity($city)->setCategoryPage($category);
        $resultPage = $this->pageFactory->create();
        $resultPage = $this->generateBreadcrumbs($resultPage, $cityPageData);
        $resultPage->getConfig()->getTitle()->set(
            $this->categoryDataProvider->getTitle($cityPageData)
        );
        $resultPage->getConfig()->setDescription(
            $this->categoryDataProvider->getDescription($cityPageData)
        );
        $resultPage->getConfig()->setMetaData(
            'robots', $this->categoryDataProvider->getMetaRobot($cityPageData)
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

    private function generateBreadcrumbs($resultPage, $cityPageData)
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
        $city = $cityPageData->getCity();
        $cityBreadcrumbName = $city->getName() . ' ' . 'Gift Baskets';
        $breadcrumbs->addCrumb(
            'city',
            [
                'label' => __($cityBreadcrumbName),
                'title' => __($cityBreadcrumbName),
                'link' => $this->urlBuilder->getBaseUrl() . $city->getUrl()
            ]
        );
        $categoryPage = $cityPageData->getCategoryPage();
        $breadcrumbs->addCrumb(
            'category',
            [
                'label' => __($categoryPage->getCategoryName()),
                'title' => __($categoryPage->getCategoryName())
            ]
        );
        return $resultPage;
    }    
}
