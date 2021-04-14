<?php


namespace Sagar\Custom\Controller\Adminhtml\ImportProducts;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;

class Index extends Action
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    /**
     * @var \Sagar\Custom\Model\CustomDataFactory
     */
    protected $customDataFactory;
    /**
     * @var \Sagar\Custom\Service\ImportImageService
     */
    protected $importImageService;

    public function __construct(
        Action\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Sagar\Custom\Model\CustomDataFactory $customDataFactory,
        \Sagar\Custom\Service\ImportImageService $importImageService
    ) {
        parent::__construct($context);
        $this->productFactory = $productFactory;
        $this->customDataFactory = $customDataFactory;
        $this->importImageService = $importImageService;
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
        $getCustomCollection = $this->customDataFactory->create()->getCollection();
        $i = 0;
        try {
            foreach ($getCustomCollection as $collection) {
                $getProductCollection = $this->productFactory->create()->getCollection()
                    ->addFieldToFilter('sku', $collection->getUuid());
                $filterProduct = $getProductCollection->getFirstItem();
                if (empty($filterProduct->getData())) {
                    try {
                        $product = $this->productFactory->create();
                        $product->setSku($collection->getUuid()); // Set your sku here
                        $product->setName($collection->getPropertyTypeTitle().' , '.$collection->getTown()); // Name of Product
                        $product->setPrice($collection->getPrice());
                        $product->setWebsiteIds([1]);
                        $product->setImage($collection->getImageFull());
                        $product->setSmallImage($collection->getImageThumbnail());
                        $imagePath = "https://www.google.co.in/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png"; // path of the image
                        // I am adding Google image for reference because url from API not showing images
                        // $imagePath = $collection->getImageFull()
                        $this->importImageService->execute($product, $imagePath, $visible = true, $imageType = ['image', 'small_image', 'thumbnail']);
                        $product->setDescription($collection->getDescription());
                        $product->setAddress($collection->getAddress());
                        $product->setNumBedrooms($collection->getNumBedrooms());
                        $product->setNumBathrooms($collection->getNumBathrooms());
                        $product->setLongitude($collection->getLongitude());
                        $product->setLatitude($collection->getLatitude());
                        $product->setPropertyTypeDescription($collection->getPropertyTypeDescription());
                        $product->setPropertyTypeTitle($collection->getPropertyTypeTitle());
                        $product->setCustomType($collection->getType() == 'sale' ? 0 : 1);
                        $product->setImportedProduct(1);
                        $product->setPropertyTypeId($collection->getPropertyTypeId());
                        $product->setCounty($collection->getCounty());
                        $product->setCountry($collection->getCountry());
                        $product->setTown($collection->getTown());
                        $product->setPropertyAddress($collection->getPropertyAddress());
                        $product->setAttributeSetId(4); // Attribute set id
                        $product->setStatus(1); // Status on product enabled/ disabled 1/0
                        $product->setWeight(10); // weight of product
                        $product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
                        $product->setTaxClassId(0); // Tax class id
                        $product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
                        $product->setStockData(
                            [
                                'use_config_manage_stock' => 0,
                                'manage_stock' => 1,
                                'is_in_stock' => 1,
                                'qty' => 99
                            ]
                        );

                        $product->save();
                        $i++;
                    } catch (\Exception $e) {
                        $this->messageManager->addError(__($e->getMessage()));
                        $this->_redirect('custom/action/');
                    }
                }
            }
            $this->messageManager->addSuccess(__("Successfully ".$i." Product Imported from Custom Table"));
            $this->_redirect('custom/action/');
        } catch (\Zend\Http\Exception\RuntimeException $runtimeException) {
            $this->messageManager->addError(__($e->getMessage()));
            $this->_redirect('custom/action/');
        }
    }
}
