<?php


namespace Sagar\Custom\Controller\Adminhtml\Action;

use Sagar\Custom\Model\CustomDataFactory;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;

class Save extends Action
{
    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * @var CustomDataFactory
     */
    protected $customData;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        ResultFactory $resultFactory,
        CustomDataFactory $customData
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->resultFactory = $resultFactory;
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
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $data = $this->getRequest()->getPostValue();
        $newData = $this->customData->create()->load($data['custom_entity_id']);
        try {
            if (isset($data['image_full'][0]['url'])) {
                $data['image_full']  = $data['image_full'][0]['url'];
            }
            if (isset($data['image_thumbnail'][0]['url'])) {
                $data['image_thumbnail']  = $data['image_thumbnail'][0]['url'];
            }
            $newData->setData($data);
            $newData->save();
            $this->messageManager->addSuccessMessage(__('Changes Saved Successfully'));
            $this->_getSession()->setFormData(false);
            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('*/*/');
        }
    }
}
