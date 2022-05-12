<?php
/**
 * Page header block
 */

namespace Dovo\Base\Block\Home;

use Dovo\HeaderImage\Helper\Data as HelperData;
use Infortis\Base\Helper\Template\Theme\Html\Header as HelperTemplateHtmlHeader;
use Infortis\Infortis\Helper\Connector\Infortis\UltraMegamenu as HelperConnectorUltraMegamenu;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Page\Title as Title;

class Hero extends \Magento\Framework\View\Element\Template
{
    /**
     * Theme helper
     *
     * @var HelperData
     */
    protected $helperData;

    /**
     * Header block helper
     *
     * @var HelperTemplateHtmlHeader
     */
    protected $helperHeader;

    /**
     * Connector helper for menu module
     *
     * @var HelperConnectorUltraMegamenu
     */
    protected $connectorMenu;
    protected $_productCollectionFactory;
    protected $_productRepository;
    protected $_productImageHelper;
    protected $_storeManager;

    /**
     * @param Context $context
     * @param HelperData $helperData
     * @param HelperTemplateHtmlHeader $helperTemplateHtmlHeader
     * @param HelperConnectorUltraMegamenu $helperConnectorUltraMegamenu
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        HelperTemplateHtmlHeader $helperTemplateHtmlHeader,
        HelperConnectorUltraMegamenu $helperConnectorUltraMegamenu,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Helper\Image $productImageHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    )
    {
        $this->helperData = $helperData;
        $this->helperHeader = $helperTemplateHtmlHeader;
        $this->connectorMenu = $helperConnectorUltraMegamenu;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_productRepository = $productRepository;
        $this->_productImageHelper = $productImageHelper;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Get helper
     *
     * @return HelperData
     */
    public function getHelperData()
    {
        return $this->helperData;
    }

    /**
     * Get helper
     *
     * @return HelperTemplateHtmlHeader
     */
    public function getHelperHeader()
    {
        return $this->helperHeader;
    }

    /**
     * Get helper
     *
     * @return HelperConnectorUltraMegamenu
     */
    public function getHelperConnectorMenu()
    {
        return $this->connectorMenu;
    }

    /**
     * Get Hero Product
     *
     * @return Product
     */
    public function getHeroProduct($id)
    {
        try {
            $product = $this->_productRepository->getById($id);
            return $product;
        } catch (\Exception $exception) {
            $coll = $this->_productCollectionFactory->create()->addAttributeToSelect('*')->addStoreFilter($this->_storeManager->getStore());
            if ((int)$this->getData('id') === 1) {
                $product = $coll->getFirstItem();
            } else {
                $product = $coll->getLastItem();
            }
            return $product;
        }
    }

    /**
     * Get Hero Product
     *
     * @return Product
     */
    public function getHeroProductUrl()
    {
        try {

            $productId = $this->helperData->getCfg(('general/hero' . $this->getData('id')));
            $product = $this->_productRepository->getById($productId);
            return $product;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function getProductImage($id)
    {
        $product = $this->_productRepository->getById($id);
        $imageUrl = $this->_productImageHelper
            ->init($product, 'product_page_image_large')
            ->setImageFile($product->getFile())
            ->getUrl();

        return $imageUrl;
    }


}
