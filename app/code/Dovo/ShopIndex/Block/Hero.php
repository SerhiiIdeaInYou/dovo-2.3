<?php
/**
 * Page header block
 */

namespace Dovo\ShopIndex\Block;

use Infortis\Base\Helper\Data as HelperData;
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
    protected $theme;

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
    ) {
        $this->theme = $helperData;
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
    public function getHelperTheme()
    {
        return $this->theme;
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
    public function getHeroProduct()
    {
        //$product = $this->_productCollectionFactory->create()->load()
        $product = $this->_productRepository->getById(704);
        return $product;
    }

    public function getProductImage() {
        $product = $this->getHeroProduct();
        $imageUrl = $this->_productImageHelper
            ->init($product, 'product_page_image_large')
            ->setImageFile($product->getFile())
            ->getUrl();

        return $imageUrl;
    }

    public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }



}
