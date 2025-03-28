<?php

namespace GiftGroup\GeoPage\Model\Registry;

/**
 * Class is responsible for serving the city data
 * Get and set method of the class will be doing the functionality.
 */
class City
{
    /**
     * @var mixed
     */
    private $cityData = null;

    /**
     * Get the city data
     *
     * @return mixed
     */
    public function getCity(): mixed
    {
        return $this->cityData;
    }

    /**
     * Set the city data
     *
     * @param mixed $data
     * @return $this
     */
    public function setCity($data): static
    {
        $this->cityData = $data;
        return $this;
    }

    /**
     * Clear the data
     *
     * @return $this
     */
    public function clear(): static
    {
        $this->cityData = null;
        return $this;
    }
}
