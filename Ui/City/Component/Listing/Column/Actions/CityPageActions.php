<?php

namespace GiftGroup\GeoPage\Ui\City\Component\Listing\Column\Actions;

/**
 * Class handling the actions of the grid view
 */
class CityPageActions extends Actions
{
    /**
     * Get the city page edit url
     *
     * @return string
     */
    public function getEditUrl(): string
    {
        return 'geopage/citypage/edit';
    }

    /**
     * Get the city page delete url
     *
     * @return string
     */
    public function getDeleteUrl(): string
    {
        return 'geopage/citypage/delete';
    }
}
