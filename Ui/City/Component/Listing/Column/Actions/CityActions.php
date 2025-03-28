<?php

namespace GiftGroup\GeoPage\Ui\City\Component\Listing\Column\Actions;

/**
 * Class handling the actions of the grid view
 */
class CityActions extends Actions
{
    /**
     * Get the city edit url
     *
     * @return string
     */
    public function getEditUrl(): string
    {
        return 'geopage/city/edit';
    }

    /**
     * Get the city delete url
     *
     * @return string
     */
    public function getDeleteUrl(): string
    {
        return 'geopage/city/delete';
    }
}
