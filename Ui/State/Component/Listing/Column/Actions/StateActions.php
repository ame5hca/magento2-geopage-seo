<?php

namespace GiftGroup\GeoPage\Ui\State\Component\Listing\Column\Actions;

/**
 * Class handling the actions of the grid view
 */
class StateActions extends Actions
{
    /**
     * Get the state edit url
     *
     * @return string
     */
    public function getEditUrl(): string
    {
        return 'geopage/state/edit';
    }

    /**
     * Get the state delete url
     *
     * @return string
     */
    public function getDeleteUrl(): string
    {
        return 'geopage/state/delete';
    }
}
