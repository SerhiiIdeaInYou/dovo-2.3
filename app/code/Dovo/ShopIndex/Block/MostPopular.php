<?php
/**
 * Page header block
 */

namespace Dovo\ShopIndex\Block;

use Dovo\HeaderImage\Helper\Data as HelperData;
use Infortis\Base\Helper\Template\Theme\Html\Header as HelperTemplateHtmlHeader;
use Infortis\Infortis\Helper\Connector\Infortis\UltraMegamenu as HelperConnectorUltraMegamenu;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Page\Title as Title;
use Magento\Setup\Exception;

class MostPopular extends \Magento\Framework\View\Element\Template
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
    protected $_bestSellerCollection;
    protected $_mostviewedCollection;
    protected $_categoryRepository;
    protected $_categoryFactory;
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
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $bestSellerCollectionFactory,
        \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $mostviewedCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->theme = $helperData;
        $this->helperHeader = $helperTemplateHtmlHeader;
        $this->connectorMenu = $helperConnectorUltraMegamenu;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_productRepository = $productRepository;
        $this->_categoryRepository = $categoryRepository;
        $this->_categoryFactory = $categoryFactory;
        $this->_productImageHelper = $productImageHelper;
        $this->_bestSellerCollection = $bestSellerCollectionFactory;
        $this->_mostviewedCollection = $mostviewedCollection;
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

    public function heroEnabled() {
        return false;
    }

    /**
     * Get Hero Product
     *
     * @return Product
     */
    public function getHeroProduct()
    {
        //$product = $this->_productCollectionFactory->create()->load()
        $product = $this->_productRepository->getById(2);
        return $product;
    }

    public function getProductImage($id) {
        $product = $this->_productRepository->getById($id);
        $imageUrl = $this->_productImageHelper
            ->init($product, 'product_page_image_medium')
            ->setImageFile($product->getFile())
            ->getUrl();

        return $imageUrl;
    }
    public function getBestSellers()
    {
        $this->_bestSellerCollection->create()->setModel('Magento\Catalog\Model\Product')
            //->addCategoriesFilter(['eq' => [4]])->addStoreFilter($this->_storeManager->getStore()->getId());
            ->join(['secondTable' => 'ds_catalog_category_product'], 'ds_sales_bestsellers_aggregated_yearly.product_id = secondTable.product_id', ['category_id' => 'secondTable.category_id'])
            ->addFieldToFilter('category_id', ['eq' => 4])
            ->addStoreFilter($this->_storeManager->getStore()->getId());

         return $this->_bestSellerCollection;
    }

    public function getProductsByCatId($id) {
        $categoryIds = explode(',', $id);
        $count = count($categoryIds) > 1;
        if($count) {
            $productCollection = $this->_productCollectionFactory->create();
            $productCollection->addAttributeToSelect('*');
            $category = $productCollection->addCategoriesFilter(array('in' => $categoryIds));
            $category->getSelect()->orderRand();
        } else {
            $category = $this->_categoryFactory->create()->load($id)->getProductCollection()->addAttributeToSelect('*');
        }
        return $category;
    }

    public function getMostViewed()
    {
        $storeId = $this->_storeManager->getStore()->getId();

        $collection = $this->_mostviewedCollection->create()->addAttributeToSelect(
            '*'
        )->addViewsCount()->setStoreId(
            $storeId
        )->addStoreFilter(
            $storeId
        );

        return $collection;
    }
    public  function getCategoryUrl($id)
    {
        try {
            $category = $this->_categoryRepository->get($id);
            return $category->getUrl();
        } catch (Exception $exception){
            return '';
        }
    }
}

