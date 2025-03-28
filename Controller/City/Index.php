<?php

namespace GiftGroup\GeoPage\Controller\City;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use GiftGroup\GeoPage\Model\Config;

/**
 * Class Index
 */
class Index implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

    private $config;

    /**
     * @param PageFactory $pageFactory
     */
    public function __construct(
        PageFactory $pageFactory,
        Config $config
    ) {
        $this->pageFactory = $pageFactory;
        $this->config = $config;
    }

    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $resultPage->getConfig()->getTitle()->set($this->getTitle());
        $resultPage->getConfig()->setDescription($this->getDescription());

        return $resultPage;
    }

    protected function getTitle()
    {
        return __('Delivery Areas | %1', $this->config->getWebsiteName());
    }
    
    protected function getDescription()
    {
        return __('All cities');
    }
}
