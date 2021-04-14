<?php

namespace Sagar\Custom\Controller\Adminhtml\Apidata;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;

class Index extends Action
{
    /**
     * @var \Zend\Http\Client
     */
    protected $zendClient;
    /**
     * @var \Sagar\Custom\Model\CustomDataFactory
     */
    protected $customDataFactory;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        Action\Context $context,
        \Zend\Http\Client $zendClient,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Sagar\Custom\Model\CustomDataFactory $customDataFactory
    ) {
        parent::__construct($context);
        $this->zendClient = $zendClient;
        $this->customDataFactory = $customDataFactory;
        $this->scopeConfig = $scopeConfig;
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
        try {
            $curl = curl_init();
            $apiKey = $this->scopeConfig->getValue('sagardata/customdata/api_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $pageNumber = $this->scopeConfig->getValue('sagardata/customdata/page_number', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $pageSize = $this->scopeConfig->getValue('sagardata/customdata/page_size', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $url = 'https://trial.craig.mtcserver15.com/api/properties?api_key='.$apiKey.'&page[number]='.$pageNumber.'&page[size]='.$pageSize;
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'APIKEY: 111111111111111111111',
                'Content-Type: application/json',
            ]);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            // EXECUTE:
            $result = curl_exec($curl);
            $responses = json_decode($result, true);
            if ($responses['data']) {
                foreach ($responses['data'] as $respons) {
                    $model = $this->customDataFactory->create();
                    $getCollection = $model->getCollection()->addFieldToFilter('uuid', $respons['uuid']);
                    $customfirstitem = $getCollection->getFirstItem();
                    if (!empty($customfirstitem->getData())) {
                        $respons['property_type_title'] = $respons['property_type']['title'];
                        $respons['property_type_description'] = $respons['property_type']['description'];
                        $respons['property_type_created_at'] = $respons['property_type']['created_at'];
                        $respons['property_type_updated_at'] = $respons['property_type']['updated_at'];
                        $model->load($customfirstitem->getId());
                        $model->setData($respons);
                        $model->setId($customfirstitem->getId());
                        $model->save();
                    } else {
                        $respons['property_type_title'] = $respons['property_type']['title'];
                        $respons['property_type_description'] = $respons['property_type']['description'];
                        $respons['property_type_created_at'] = $respons['property_type']['created_at'];
                        $respons['property_type_updated_at'] = $respons['property_type']['updated_at'];
                        $model->setData($respons);
                        $model->save();
                    }
                }
                $this->messageManager->addSuccess(__("Successfully imported Custom Data"));
                $this->_redirect('custom/action/');
            } else {
                $this->messageManager->addError(__("No Data Found"));
                $this->_redirect('custom/action/');
            }
        } catch (\Zend\Http\Exception\RuntimeException $runtimeException) {
            echo $runtimeException->getMessage();
        }
    }
}
