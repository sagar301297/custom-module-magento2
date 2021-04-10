<?php


namespace Sagar\Custom\Model\ResourceModel\CustomData;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Sagar\Custom\Model\CustomData;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(CustomData::class, \Sagar\Custom\Model\ResourceModel\CustomData::class);
    }
}
