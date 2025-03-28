<?php

namespace GiftGroup\GeoPage\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * CityCategoryPage resource model class
 */
class CityCategoryPage extends AbstractDb
{
    public const TABLE = 'giftgroup_city_category_page';

    /**
     * Construct function
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE, 'id');
    }

    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getCityId()) {
            $select->join(
                ['city' => $this->getTable('giftgroup_cities')],
                $this->getMainTable() . '.city_id = city.id',
                ['code' => 'city_code']
            );
        }
        if ($object->getStateId()) {
            $select->join(
                ['state' => $this->getTable('giftgroup_states')],
                $this->getMainTable() . '.state_id = state.id',
                ['state_code']
            );
        }

        return $select;
    }
}