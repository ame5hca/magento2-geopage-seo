<?php

namespace GiftGroup\GeoPage\Controller\Adminhtml\CategoryPage;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use GiftGroup\GeoPage\Model\Sitemap\CategoryPageSitemap;
use GiftGroup\GeoPage\Logger\Logger;

/**
 * Class responsible for generating the sitemaps
 */
class GenerateSitemap extends Action implements HttpPostActionInterface
{
    private $sitemap;

    private $logger;

    public function __construct(
        Context            $context,
        CategoryPageSitemap $sitemap,
        Logger $logger
    ) {
        $this->sitemap = $sitemap;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Execute function for delete.
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->sitemap->generateXml();
            $this->messageManager->addSuccessMessage(
                __('Successfully generated the sitemaps.')
            );
            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->logger->critical('SitemapGenerateError : ' . $e->getMessage());
        }
        $this->messageManager->addErrorMessage(
            __('Something went wrong while generating the sitemap.')
        );
        return $resultRedirect->setPath('*/*/');
    }
}
