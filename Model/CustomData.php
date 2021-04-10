<?php


namespace Sagar\Custom\Model;

use Magento\Framework\Model\AbstractModel;

class CustomData extends AbstractModel
{
    public function _construct()
    {
        $this->_init(\Sagar\Custom\Model\ResourceModel\CustomData::class);
        parent::_construct();
    }
}
