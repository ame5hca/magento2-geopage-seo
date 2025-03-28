<?php

namespace GiftGroup\GeoPage\Block\Adminhtml\CategoryPage;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Model\UrlInterface;

/**
 * Provide data to render the generate sitemap button
 */
class GenerateSitemap implements ButtonProviderInterface
{
    protected $urlBuilder;

    /**
     * GenerateSitemap construct function
     *
     * @param UrlInterface $urlBuilder
     * @return void
     */
    public function __construct(
        UrlInterface     $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        return [
            'label' => __('Generate Sitemap'),
            'class' => 'secondary',
            'on_click' => 'deleteConfirm(\'' . $this->getGeneralInformation() . '\', \'' . $this->getUrl() . '\', {"data": {}})',
            'sort_order' => 20,
        ];
    }

    /**
     * URL to generate sitemap action
     *
     * @return string
     */
    private function getUrl(): string
    {
        return $this->urlBuilder->getUrl('*/*/generatesitemap');
    }

    private function getGeneralInformation()
    {
        return __('This will generate sitemap for all the stores and it will be saved in pub/sitemaps/ folder. So you can get the sitemap as {siteurl}/sitemaps/sitemap_category_pages_{storecode}.xml');
    }
}
