<?php
/**
 * Page header block
 */

namespace Dovo\Base\Block\Product;

use Infortis\Base\Helper\Data as HelperData;
use Infortis\Base\Helper\Template\Catalog\Product\View as HelperTemplateProductView;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;

class Properties extends \Magento\Framework\View\Element\Template
{
    /**
     * Theme helper
     *
     * @var HelperData
     */
    protected $theme;

    /**
     * Product view helper
     *
     * @var HelperTemplateProductView
     */
    protected $helperProductView;

    /**
     * @var Registry
     */
    protected $registry;

    protected $_categoryCollectionFactory;
    protected $_storeManager;
    /**
     *
     * @param Context $context
     * @param HelperData $helperData
     * @param HelperTemplateProductView $helperTemplateProductView
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        HelperTemplateProductView $helperTemplateProductView,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Registry $registry,
        array $data = []
    ) {
        $this->theme = $helperData;
        $this->helperProductView = $helperTemplateProductView;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->registry = $registry;

        parent::__construct($context, $data);
    }

    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * Get Store code
     *
     * @return string
     */
    public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
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
     * @return HelperTemplateProductView
     */
    public function getHelperProductView()
    {
        return $this->helperProductView;
    }

    /**
     * Retrieve currently viewed product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->registry->registry('product');
    }

    public function getProductCategory() {
        $product = $this->getProduct();
        $categoryIds = $product->getCategoryIds();

        $categories = $this->getCategoryCollection()
            ->addAttributeToFilter('entity_id', $categoryIds);

        foreach($categories as $category) {
            if(!$category->getChildren()) {
                return $category->getName();
            }
        }
        return '';
    }

    /**
     * Get category collection
     *
     * @param bool $isActive
     * @param bool|int $level
     * @param bool|string $sortBy
     * @param bool|int $pageSize
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection or array
     */
    private function getCategoryCollection($isActive = true, $level = false, $sortBy = false, $pageSize = false)
    {
        $collection = $this->_categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');

        // select only active categories
        if ($isActive) {
            $collection->addIsActiveFilter();
        }

        // select categories of certain level
        if ($level) {
            $collection->addLevelFilter($level);
        }

        // sort categories by some value
        if ($sortBy) {
            $collection->addOrderField($sortBy);
        }

        // select certain number of categories
        if ($pageSize) {
            $collection->setPageSize($pageSize);
        }

        return $collection;
    }


}
