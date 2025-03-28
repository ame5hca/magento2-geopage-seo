<?php

namespace GiftGroup\GeoPage\Model\Registry;

/**
 * Class is responsible for serving the city data
 * Get and set method of the class will be doing the functionality.
 */
class CategoryPage
{
    /**
     * @var mixed
     */
    private $categoryPage = null;

    /**
     * Get the city data
     *
     * @return mixed
     */
    public function getCategoryPage(): mixed
    {
        return $this->categoryPage;
    }

    /**
     * Set the city data
     *
     * @param mixed $data
     * @return $this
     */
    public function setCategoryPage($data): static
    {
        $this->categoryPage = $data;
        return $this;
    }

    /**
     * Clear the data
     *
     * @return $this
     */
    public function clear(): static
    {
        $this->categoryPage = null;
        return $this;
    }
}
