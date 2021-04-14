<?php


namespace Sagar\Custom\Model\Config\Source;

class Options extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [
            ['label' => __('Sale'), 'value'=>'0'],
            ['label' => __('Rent'), 'value'=>'1']
        ];

        return $this->_options;
    }
}
