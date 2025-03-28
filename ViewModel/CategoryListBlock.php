<?php

namespace GiftGroup\GeoPage\ViewModel;

class CategoryListBlock extends Block3
{
    public function showCategoryPageList()
    {
        return $this->cityPageDataProvider->showCategoryPageList();
    }
    
    public function getCityCategories()
    {
        return $this->cityPageDataProvider->getCityCategories();
    }

    public function getCategoryPageBlockTitle()
    {
        return __('Popular Collections in %1', $this->getCity()->getName());
    }
    
    public function getImage($categoryId)
    {
        $collection = $this->productsDataProvider->getItems([$categoryId], 1);
        if ($collection->getSize()) {
            $product = $collection->getFirstItem();
            return $this->getProductImage($product);
        }
        return null;
    }
}