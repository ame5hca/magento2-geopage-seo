<?php

namespace GiftGroup\GeoPage\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use GiftGroup\GeoPage\Model\UrlRewriteManager;

class DeleteCityPageUrlRedirect implements ObserverInterface
{
    private $urlRewriteManager;

    public function __construct(
        UrlRewriteManager $urlRewriteManager
    ) {
        $this->urlRewriteManager = $urlRewriteManager;
    }

    public function execute(Observer $observer)
    {
        $city = $observer->getEvent()->getObject();
        $this->urlRewriteManager->deleteCityUrlRewrite($city);
    }
}