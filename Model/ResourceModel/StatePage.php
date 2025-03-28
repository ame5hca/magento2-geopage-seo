<?php

namespace GiftGroup\GeoPage\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * StatePage resource model class
 */
class StatePage extends AbstractDb
{
    public const TABLE = 'giftgroup_state_page';

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