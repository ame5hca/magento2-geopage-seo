<?php

namespace GiftGroup\GeoPage\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use GiftGroup\GeoPage\Model\UrlRewriteManager;

class AddUrlRedirect implements ObserverInterface
{
    private $urlRewriteManager;

    public function __construct(
        UrlRewriteManager $urlRewriteManager
    ) {
        $this->urlRewriteManager = $urlRewriteManager;
    }

    public function execute(Observer $observer)
    {
        $cityPage = $observer->getData('city_page');
        $this->urlRewriteManager->addCityPageUrlRewrite($cityPage);
        /**
         * Clear the category collection variable by calling clearCategoryData() is required
         * else previous loaded data will be returned.
         */
        /* $categories = $cityPage->clearCategoryData()->getCategoryCollection();
        if ($categories) {
            foreach ($categories as $category) {
                $this->urlRewriteManager->addCityCategoryUrlRewrite(
                    $cityPage, 
                    ['id' => $category->getId(), 'url_key' => $category->getUrlKey()]
                );
            }
        }   */     
    }
}