<?php

namespace GiftGroup\GeoPage\Model\Sitemap;

use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Escaper;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use GiftGroup\GeoPage\Model\Config;
use GiftGroup\GeoPage\Model\ResourceModel\StatePage\CollectionFactory as StatePageCollectionFactory;

/**
 * Sitemap model.
 */
class StatePageSitemap extends AbstractSitemap
{
    private $urlRewriteCollectionFactory;

    private $statePageCollectionFactory;

    public function __construct(
        Escaper $escaper,
        Filesystem $filesystem,
        StoreRepositoryInterface $storeRepository,
        UrlRewriteCollectionFactory $urlRewriteCollectionFactory,
        StatePageCollectionFactory $statePageCollectionFactory
    ) {
        parent::__construct($escaper, $filesystem, $storeRepository);
        $this->urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
        $this->statePageCollectionFactory = $statePageCollectionFactory;
    }

    /**
     * Get current sitemap filename
     *
     * @param int $index
     * @return string
     */
    protected function getCurrentSitemapFilename()
    {
        return 'sitemap_state_pages_' . $this->getStore()->getCode() . '.xml';
    }
    
    protected function getItems($storeId)
    {
        $urls = $items = [];
        $collection = $this->urlRewriteCollectionFactory->create();
        $collection->addFieldToSelect(['request_path', 'target_path']);
        $collection->addFieldToFilter('store_id', ['eq' => $storeId]);
        $collection->addFieldToFilter('target_path', ['like' => Config::STATE_PAGE_BASE_PATH . '%']);
        if ($collection->getSize()) {
            foreach ($collection as $urlRewrite) {
                $urls[$urlRewrite->getData('target_path')] = $urlRewrite->getData('request_path');
            }
        }

        $pageCollection = $this->statePageCollectionFactory->create();
        $pageCollection->addFieldToSelect(['id', 'updated_at']);
        $pageCollection->addFieldToFilter('is_active', ['eq' => 1]);
        $pageCollection->addFieldToFilter('store_id', ['eq' => $storeId]);
        if ($pageCollection->getSize()) {            
            foreach ($pageCollection as $statePage) {
                $actionUrl = Config::STATE_PAGE_BASE_PATH . 'id/' . $statePage->getData('id');
                $items[] = [
                    'url' => (isset($urls[$actionUrl]) ? $urls[$actionUrl] : ''),
                    'updated_at' =>  $statePage->getData('updated_at')
                ];
            }
        }

        return $items;
    }
}
