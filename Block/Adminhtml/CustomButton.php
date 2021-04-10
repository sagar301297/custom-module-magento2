<?php


namespace Sagar\Custom\Block\Adminhtml;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\AuthorizationInterface;

class CustomButton implements ButtonProviderInterface
{
    /**
     * @var AuthorizationInterface
     */
    protected $authorization;
    /**
     * @var Context
     */
    protected $context;

    public function __construct(
        AuthorizationInterface $authorization,
        Context $context
    ) {
        $this->authorization = $authorization;
        $this->context = $context;
    }

    /**
     * Retrieve button-specified settings
     *
     * @return array
     */
    public function getButtonData()
    {
        if (!$this->authorization->isAllowed('Magento_Cms::save')) {
            return [];
        }

        return [
            'label' => __('Import Data From API'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'primary',
            'sort_order' => 10
        ];
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->context->getUrlBuilder()->getUrl('custom/Apidata', []);
    }
}
