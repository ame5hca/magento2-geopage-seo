<?php

namespace GiftGroup\GeoPage\ViewModel\Category;

class CategoryListBlock extends Block3
{
    public function showCategoryPageList()
    {
        return $this->categoryPageDataProvider->showCategoryPageList();
    }
    
    public function getCityCategories()
    {
        return $this->categoryPageDataProvider->getRelatedCategories();
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