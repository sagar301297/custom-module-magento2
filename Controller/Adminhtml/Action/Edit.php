<?php


namespace Sagar\Custom\Controller\Adminhtml\Action;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var \Sagar\Custom\Model\CustomData
     */
    protected $model;

    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory,
        \Magento\Framework\Registry $registry,
        \Sagar\Custom\Model\CustomData $model
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        $this->registry = $registry;
        $this->model = $model;
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->model;

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Data is no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->registry->register("sagar_custom_data", $model);
        $resultPage = $this->pageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Data %1', $model->getId()));
        return $resultPage;
    }
}
