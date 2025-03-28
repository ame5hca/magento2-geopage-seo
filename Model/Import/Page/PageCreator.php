<?php

namespace GiftGroup\GeoPage\Model\Import\Page;

use Magento\Framework\App\ResourceConnection;

class PageCreator
{
    private $resource;

    private $connection;

    public function __construct(
        ResourceConnection $resource
    ) {
        $this->connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
    }

    public function createCityPages($entityRows)
    {
        if ($entityRows) {
            $this->connection->insertOnDuplicate(
                $this->connection->getTableName(), 
                $rows, 
                $this->getFinalColumns()
            );

            return true;
        }
    }
    
    public function createCategoryPages($entityRows)
    {
        
    }
    
    public function createStatePages($entityRows)
    {
        
    }
}