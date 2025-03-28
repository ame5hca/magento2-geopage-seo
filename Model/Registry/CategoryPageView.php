<?php

namespace GiftGroup\GeoPage\Model\Registry;

/**
 * Class is responsible for serving the city & page data
 * Get and set method of the class will be doing the functionality.
 */
class CategoryPageView
{
    /**
     * @var mixed
     */
    private $data = ['city' => null, 'category_page' => null, 'cat_name_capital' => false];

    /**
     * Get the city data
     *
     * @return mixed
     */
    public function getCity(): mixed
    {
        return $this->data['city'];
    }

    /**
     * Set the city data
     *
     * @param mixed $data
     * @return $this
     */
    public function setCity($city): static
    {
        $this->data['city'] = $city;
        return $this;
    }

    /**
     * Get the city category data
     *
     * @return mixed
     */
    public function getCategoryPage(): mixed
    {
        return $this->data['category_page'];
    }

    /**
     * Set the city category data
     *
     * @param mixed $data
     * @return $this
     */
    public function setCategoryPage($categoryData): static
    {
        $this->data['category_page'] = $categoryData;
        return $this;
    }

    public function getIsCatNameCapital()
    {
        return $this->data['cat_name_capital'];
    }
    
    public function setIsCatNameCapital($value): static
    {
        $this->data['cat_name_capital'] = $value;
        return $this;
    }

    /**
     * Clear the data
     *
     * @return $this
     */
    public function clear(): static
    {
        $this->data['city'] = null;
        $this->data['category_page'] = null;
        $this->data['cat_name_capital'] = false;
        return $this;
    }
}
