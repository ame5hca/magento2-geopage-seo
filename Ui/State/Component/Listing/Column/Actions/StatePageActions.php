<?php

namespace GiftGroup\GeoPage\Ui\State\Component\Listing\Column\Actions;

/**
 * Class handling the actions of the grid view
 */
class StatePageActions extends Actions
{
    /**
     * Get the state edit url
     *
     * @return string
     */
    public function getEditUrl(): string
    {
        return 'geopage/statepage/edit';
    }

    /**
     * Get the state delete url
     *
     * @return string
     */
    public function getDeleteUrl(): string
    {
        return 'geopage/statepage/delete';
    }
}
