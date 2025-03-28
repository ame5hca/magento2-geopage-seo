<?php

namespace GiftGroup\GeoPage\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use GiftGroup\GeoPage\Model\UrlRewriteManager;

class AddStatePageUrlRedirect implements ObserverInterface
{
    private $urlRewriteManager;

    public function __construct(
        UrlRewriteManager $urlRewriteManager
    ) {
        $this->urlRewriteManager = $urlRewriteManager;
    }

    public function execute(Observer $observer)
    {
        $statePage = $observer->getData('state_page');
        $this->urlRewriteManager->addStatePageUrlRewrite($statePage);
    }
}