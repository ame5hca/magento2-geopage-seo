<?php

namespace GiftGroup\GeoPage\Model\Registry;

/**
 * Class is responsible for serving the state page data
 * Get and set method of the class will be doing the functionality.
 */
class StatePage
{
    /**
     * @var mixed
     */
    protected $statePage = null;

    /**
     * Get the state page data
     *
     * @return mixed
     */
    public function getStatePage(): mixed
    {
        return $this->statePage;
    }

    /**
     * Set the state page data
     *
     * @param mixed $data
     * @return $this
     */
    public function setStatePage($data): static
    {
        $this->statePage = $data;
        return $this;
    }

    /**
     * Clear the data
     *
     * @return $this
     */
    public function clear(): static
    {
        $this->statePage = null;
        return $this;
    }
}
