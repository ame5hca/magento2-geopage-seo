<?php

namespace GiftGroup\GeoPage\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * States resource model class
 */
class States extends AbstractDb
{
    public const TABLE = 'giftgroup_states';
    
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