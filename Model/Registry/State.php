<?php

namespace GiftGroup\GeoPage\Model\Registry;

/**
 * Class is responsible for serving the state data
 * Get and set method of the class will be doing the functionality.
 */
class State
{
    /**
     * @var mixed
     */
    private $stateData = null;

    /**
     * Get the state data
     *
     * @return mixed
     */
    public function getState(): mixed
    {
        return $this->stateData;
    }

    /**
     * Set the state data
     *
     * @param mixed $data
     * @return $this
     */
    public function setState($data): static
    {
        $this->stateData = $data;
        return $this;
    }

    /**
     * Clear the data
     *
     * @return $this
     */
    public function clear(): static
    {
        $this->stateData = null;
        return $this;
    }
}
