<?php

namespace GiftGroup\GeoPage\Model\Registry;

/**
 * Class is responsible for serving the city page data
 * Get and set method of the class will be doing the functionality.
 */
class CityPage
{
    /**
     * @var mixed
     */
    private $cityPageData = null;

    /**
     * Get the city page data
     *
     * @return mixed
     */
    public function getCityPage(): mixed
    {
        return $this->cityPageData;
    }

    /**
     * Set the city page data
     *
     * @param mixed $data
     * @return $this
     */
    public function setCityPage($data): static
    {
        $this->cityPageData = $data;
        return $this;
    }

    /**
     * Clear the data
     *
     * @return $this
     */
    public function clear(): static
    {
        $this->cityPageData = null;
        return $this;
    }
}
