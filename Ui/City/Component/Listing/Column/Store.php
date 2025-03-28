<?php

namespace GiftGroup\GeoPage\Ui\City\Component\Listing\Column;

use Magento\Store\Ui\Component\Listing\Column\Store as MagentoStoreUiStore;

/**
 * Class Store
 */
class Store extends MagentoStoreUiStore
{
    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $content = '';
        if ($item['store_id'] == 0) {
            return __('All Store Views');
        }
        $origStores = [$item['store_id']];
        $data = $this->systemStore->getStoresStructure(false, $origStores);

        foreach ($data as $website) {
            $content .= $website['label'] . "<br/>";
            foreach ($website['children'] as $group) {
                $content .= str_repeat('&nbsp;', 3) . $this->escaper->escapeHtml($group['label']) . "<br/>";
                foreach ($group['children'] as $store) {
                    $content .= str_repeat('&nbsp;', 6) . $this->escaper->escapeHtml($store['label']) . "<br/>";
                }
            }
        }

        return $content;
    }
}
