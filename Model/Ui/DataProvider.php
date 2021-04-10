<?php


namespace Sagar\Custom\Model\Ui;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Sagar\Custom\Model\ResourceModel\CustomData\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $reporting, $searchCriteriaBuilder, $request, $filterBuilder, $meta, $data);
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->dataPersistor = $dataPersistor;
    }


    /**
     * @return array
     */
    public function getData()
    {

        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collectionFactory->create()->getItems();
        $m = [];
        foreach ($items as $model) {
            if ($model->getImageFull()!= '' || $model->getImageFull() !=null) {
                $m['image_full'][0]['name']   = $model->getImageFull();
                $m['image_full'][0]['url']    = $model->getImageFull();
            }
            if ($model->getImageThumbnail()!= '' || $model->getImageThumbnail() !=null) {
                $m['image_thumbnail'][0]['name']   = $model->getImageThumbnail();
                $m['image_thumbnail'][0]['url']    = $model->getImageThumbnail();
            }
            $this->loadedData[$model->getId()] = array_merge($model->getData(), $m);
        }
        $data = $this->dataPersistor->get('sagar_custom_data');

        if (!empty($data)) {
            $model = $this->collectionFactory->create()->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('sagar_custom_data');
        }

        return $this->loadedData;
    }
}
