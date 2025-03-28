<?php

namespace GiftGroup\GeoPage\Model\Sitemap;

use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Escaper;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use GiftGroup\GeoPage\Model\Config;
use GiftGroup\GeoPage\Model\ResourceModel\CityCategoryPage\CollectionFactory as CategoryPageCollectionFactory;

/**
 * Sitemap model.
 */
class CategoryPageSitemap extends AbstractSitemap
{
    private $urlRewriteCollectionFactory;

    private $categoryPageCollectionFactory;

    public function __construct(
        Escaper $escaper,
        Filesystem $filesystem,
        StoreRepositoryInterface $storeRepository,
        UrlRewriteCollectionFactory $urlRewriteCollectionFactory,
        CategoryPageCollectionFactory $categoryPageCollectionFactory
    ) {
        parent::__construct($escaper, $filesystem, $storeRepository);
        $this->urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
        $this->categoryPageCollectionFactory = $categoryPageCollectionFactory;
    }

    /**
     * Get current sitemap filename
     *
     * @param int $index
     * @return string
     */
    protected function getCurrentSitemapFilename()
    {
        return 'sitemap_category_pages_' . $this->getStore()->getCode() . '.xml';
    }
    
    protected function getItems($storeId)
    {
        $urls = $items = [];
        $collection = $this->urlRewriteCollectionFactory->create();
        $collection->addFieldToSelect(['request_path', 'target_path']);
        $collection->addFieldToFilter('store_id', ['eq' => $storeId]);
        $collection->addFieldToFilter('target_path', ['like' => Config::CITY_CATEGORY_PAGE_BASE_PATH . '%']);
        if ($collection->getSize()) {
            foreach ($collection as $urlRewrite) {
                $urls[$urlRewrite->getData('target_path')] = $urlRewrite->getData('request_path');
            }
        }

        $pageCollection = $this->categoryPageCollectionFactory->create();
        $pageCollection->addFieldToSelect(['id', 'updated_at']);
        $pageCollection->addFieldToFilter('is_active', ['eq' => 1]);
        $pageCollection->addFieldToFilter('store_id', ['eq' => $storeId]);
        if ($pageCollection->getSize()) {            
            foreach ($pageCollection as $catPage) {
                $actionUrl = Config::CITY_CATEGORY_PAGE_BASE_PATH . 'id/' . $catPage->getData('id');
                $items[] = [
                    'url' => (isset($urls[$actionUrl]) ? $urls[$actionUrl] : ''),
                    'updated_at' =>  $catPage->getData('updated_at')
                ];
            }
        }

        return $items;
    }
}
