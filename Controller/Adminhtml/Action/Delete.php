<?php


namespace Sagar\Custom\Controller\Adminhtml\Action;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\View\Result\PageFactory;
use Sagar\Custom\Model\CustomData;

class Delete extends Action
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;
    /**
     * @var RedirectFactory
     */
    protected $redirectFactory;
    /**
     * @var CustomData
     */
    protected $customData;

    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory,
        RedirectFactory $redirectFactory,
        CustomData $customData
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        $this->redirectFactory = $redirectFactory;
        $this->customData = $customData;
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
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model = $this->customData;
            $model->load($id);
            try {
                $model->delete();
                $this->messageManager->addSuccessMessage(__('Data Deleted'));
                return $resultRedirect->setPath('*/*/index');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                return $resultRedirect->setPath('*/*/edit', ['id'=>$id]);
            }
        }
    }
}
