<?php

namespace GiftGroup\GeoPage\Model\Sitemap;

use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Escaper;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use GiftGroup\GeoPage\Model\Config;
use GiftGroup\GeoPage\Model\ResourceModel\CityPage\CollectionFactory as CityPageCollectionFactory;

/**
 * Sitemap model.
 */
class CityPageSitemap extends AbstractSitemap
{
    private $urlRewriteCollectionFactory;

    private $cityPageCollectionFactory;

    public function __construct(
        Escaper $escaper,
        Filesystem $filesystem,
        StoreRepositoryInterface $storeRepository,
        UrlRewriteCollectionFactory $urlRewriteCollectionFactory,
        CityPageCollectionFactory $cityPageCollectionFactory
    ) {
        parent::__construct($escaper, $filesystem, $storeRepository);
        $this->urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
        $this->cityPageCollectionFactory = $cityPageCollectionFactory;
    }

    /**
     * Get current sitemap filename
     *
     * @param int $index
     * @return string
     */
    protected function getCurrentSitemapFilename()
    {
        return 'sitemap_city_pages_' . $this->getStore()->getCode() . '.xml';
    }
    
    protected function getItems($storeId)
    {
        $urls = $items = [];
        $collection = $this->urlRewriteCollectionFactory->create();
        $collection->addFieldToSelect(['request_path', 'target_path']);
        $collection->addFieldToFilter('store_id', ['eq' => $storeId]);
        $collection->addFieldToFilter('target_path', ['like' => Config::CITY_PAGE_BASE_PATH . '%']);
        if ($collection->getSize()) {
            foreach ($collection as $urlRewrite) {
                $urls[$urlRewrite->getData('target_path')] = $urlRewrite->getData('request_path');
            }
        }

        $pageCollection = $this->cityPageCollectionFactory->create();
        $pageCollection->addFieldToSelect(['id', 'updated_at']);
        $pageCollection->addFieldToFilter('is_active', ['eq' => 1]);
        $pageCollection->addFieldToFilter('store_id', ['eq' => $storeId]);
        if ($pageCollection->getSize()) {            
            foreach ($pageCollection as $cityPage) {
                $actionUrl = Config::CITY_PAGE_BASE_PATH . 'id/' . $cityPage->getData('id');
                $items[] = [
                    'url' => (isset($urls[$actionUrl]) ? $urls[$actionUrl] : ''),
                    'updated_at' =>  $cityPage->getData('updated_at')
                ];
            }
        }

        return $items;
    }
}
