<?php

namespace GiftGroup\GeoPage\Model\Page\Category;

use GiftGroup\GeoPage\Model\Registry\CategoryPageView;

class ShortCodeRenderer
{
    private $categoryPageRegistry;

    public function __construct(
        CategoryPageView $categoryPageRegistry
    ) {
        $this->categoryPageRegistry = $categoryPageRegistry;
    }

    public function render($data)
    {
        if (is_null($data)) {
            return $data;
        }
        if (str_contains($data, '{category}')) {
            $categoryPage = $this->categoryPageRegistry->getCategoryPage();
            $categoryName = $categoryPage ? $categoryPage->getCategoryName() : '';
            if ($this->categoryPageRegistry->getIsCatNameCapital()) {
                $categoryName = ucwords($categoryName);
            }
            $data = str_replace('{category}', $categoryName, $data);
        }
        return $data;        
    }
}