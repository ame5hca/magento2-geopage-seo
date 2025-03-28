<?php

namespace GiftGroup\GeoPage\Model\DataProvider;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use GiftGroup\GeoPage\Model\CityCategoryPageFactory;
use GiftGroup\GeoPage\Model\Config;
use GiftGroup\GeoPage\Model\Page\ShortCodeRenderer;

class CategoryPage
{
    private $categoryRepository;

    private $storeManager;

    private $request;

    private $categoryPageFactory;

    private $config;

    private $shortCodeRenderer;

    private $categoryPage = null;

    private $storeCode = null;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        StoreManagerInterface $storeManager,
        RequestInterface $request,
        CityCategoryPageFactory $categoryPageFactory,
        Config $config,
        ShortCodeRenderer $shortCodeRenderer
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->categoryPageFactory = $categoryPageFactory;
        $this->config = $config;
        $this->shortCodeRenderer = $shortCodeRenderer;
    }

    public function getCurrentCategoryPage()
    {
        $categoryPageId = $this->request->getParam('id', null);
        if (!$categoryPageId) {
            return null;
        }
        return $this->getCategoryPageById($categoryPageId);
    }

    public function getCategoryPageById($pageId)
    {
        if (!$this->categoryPage || ($pageId != $this->categoryPage->getId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $this->categoryPage = $this->categoryPageFactory->create()->load($pageId);
            if ($storeId != $this->categoryPage->getData('store_id')) {
                $this->categoryPage = null;
            }
        }
        return $this->categoryPage;
    }

    public function getTitle($categoryRegistry)
    {
        /* return __(
            '%1 %2 Gift Baskets Delivery | Send %2 Gift Basket Delivery To %1, %3 | %4', 
            $categoryRegistry->getCity()->getName(),
            $categoryRegistry->getCategoryPage()->getCategoryName(),
            $categoryRegistry->getCity()->getRegionName(),
            $this->config->getWebsiteName()); */
            $categoryRegistry->setIsCatNameCapital(true);
            $title = $this->shortCodeRenderer->render(
                $categoryRegistry->getCity(),
                $categoryRegistry->getCategoryPage()->getData('meta_title'),
                $categoryRegistry->getCategoryPage()->getData('store_id')
            );
            $categoryRegistry->setIsCatNameCapital(false);
            return $title;
    }
    
    public function getDescription($categoryRegistry)
    {
        /* return __(
            'Buy %1 Gift Basket Online - Free Shipping To %2(%3, %4) Same-Day Delivery in %2 | Over 3200 Custom Designs ⏩ Order Now!',
            $categoryRegistry->getCategoryPage()->getCategoryName(),
            $categoryRegistry->getCity()->getName(),
            $categoryRegistry->getCity()->getRegionName(),
            $categoryRegistry->getCity()->getCountryName()
        ); */
        $categoryRegistry->setIsCatNameCapital(true);
        $description = $this->shortCodeRenderer->render(
            $categoryRegistry->getCity(),
            $categoryRegistry->getCategoryPage()->getData('meta_description'),
            $categoryRegistry->getCategoryPage()->getData('store_id')
        );
        $categoryRegistry->setIsCatNameCapital(false);
        return $description;
    }
    
    public function getMetaRobot($categoryRegistry)
    {
        $metRobot = $categoryRegistry->getCategoryPage()->getMetaRobot();
        if ($metRobot == 'INDEX,FOLLOW') {
            $sorting = $this->request->getParam('product_list_order', null);
            if ($sorting) {
                return 'NOINDEX,FOLLOW';
            }
        }
        return $metRobot;
    }

    public function getCurrentStoreCode()
    {
        if (!$this->storeCode) {
            $this->storeCode = $this->storeManager->getStore()->getCode();
        }
        return $this->storeCode;
    }
}