<?php

namespace GiftGroup\GeoPage\Model\Registry;

/**
 * Class is responsible for serving the city & page data
 * Get and set method of the class will be doing the functionality.
 */
class CityPageView
{
    /**
     * @var mixed
     */
    private $cityData = ['city' => null, 'category' => null];

    /**
     * Get the city data
     *
     * @return mixed
     */
    public function getCity(): mixed
    {
        return $this->cityData['city'];
    }

    /**
     * Set the city data
     *
     * @param mixed $data
     * @return $this
     */
    public function setCity($data): static
    {
        $this->cityData['city'] = $data;
        return $this;
    }

    /**
     * Get the city page data
     *
     * @return mixed
     */
    public function getCityPage(): mixed
    {
        return $this->cityData['city_page'];
    }

    /**
     * Set the city page data
     *
     * @param mixed $data
     * @return $this
     */
    public function setCityPage($data): static
    {
        $this->cityData['city_page'] = $data;
        return $this;
    }

    /**
     * Get the city category data
     *
     * @return mixed
     */
    public function getCategory(): mixed
    {
        return $this->cityData['category'];
    }

    /**
     * Set the city category data
     *
     * @param mixed $data
     * @return $this
     */
    public function setCategory($data): static
    {
        $this->cityData['category'] = $data;
        return $this;
    }

    /**
     * Clear the data
     *
     * @return $this
     */
    public function clear(): static
    {
        $this->cityData['city'] = null;
        $this->cityData['category'] = null;
        return $this;
    }
}
