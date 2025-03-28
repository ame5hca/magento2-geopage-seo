<?php

namespace GiftGroup\GeoPage\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use GiftGroup\GeoPage\Model\Config;

class Head implements ArgumentInterface
{
    private $storeManager;

    private $config;

    public function __construct(
        StoreManagerInterface $storeManager,
        Config $config
    ) {
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    public function getCurrentStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }
    
    public function getExternalLinksData($storeId)
    {
        return $this->config->getExternalLinksData($storeId);
    }
    
    public function getAdditionalStyles($storeId)
    {
        return $this->config->getAdditionalStyles($storeId);
    }
}
