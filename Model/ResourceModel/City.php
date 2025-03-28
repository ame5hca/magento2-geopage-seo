<?php

namespace GiftGroup\GeoPage\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * City resource model class
 */
class City extends AbstractDb
{
    public const TABLE = 'giftgroup_cities';

    /**
     * Construct function
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE, 'id');
    }
}