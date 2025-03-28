<?php

namespace GiftGroup\GeoPage\Ui\City\Component\Listing\Column\Actions;

/**
 * Class handling the actions of the grid view
 */
class CategoryActions extends Actions
{
    /**
     * Get the city page edit url
     *
     * @return string
     */
    public function getEditUrl(): string
    {
        return 'geopage/categorypage/edit';
    }

    /**
     * Get the city page delete url
     *
     * @return string
     */
    public function getDeleteUrl(): string
    {
        return 'geopage/categorypage/delete';
    }
}
