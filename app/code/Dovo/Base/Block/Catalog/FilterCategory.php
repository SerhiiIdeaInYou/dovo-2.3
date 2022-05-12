<?php
/**
 * Page header block
 */

namespace Dovo\Base\Block\Catalog;

use Dovo\HeaderImage\Helper\Data as HelperData;
use Magento\Framework\View\Element\Template\Context;

class FilterCategory extends \Magento\Framework\View\Element\Template
{
    protected $_categoryHelper;
    protected $_categoryFactory;
    protected $_catalogLayer;
    protected $_frameworkRegistry;
    protected $_currentCategoryKey;
    /**
     * Theme helper
     *
     * @var HelperData
     */
    protected $helperData;
    /**
     * @param Context $context
     * @param HelperData $helperData
     * @param HelperTemplateHtmlHeader $helperTemplateHtmlHeader
     * @param HelperConnectorUltraMegamenu $helperConnectorUltraMegamenu
     * @param array $data
     */
    public function __construct(
        HelperData $helperData,
        Context $context,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Catalog\Model\LayerFactory $catalogModelLayerFactory,
        \Magento\Framework\Registry $frameworkRegistry,
        array $data = []
    ) {
        $this->_categoryHelper = $categoryHelper;
        $this->_categoryFactory = $categoryCollectionFactory;
        $this->helperData = $helperData;
        $this->_frameworkRegistry = $frameworkRegistry;
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

    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        return $this->_categoryHelper->getStoreCategories($sorted , $asCollection, $toLoad);
    }

    public function getDescendants($category, $levels = 2)
    {
        if ((int)$levels < 1) {
            $levels = 1;
        }
        $collection = $this->_categoryFactory->create()
            ->addPathsFilter($category->getPath().'/')
            ->addLevelFilter($category->getLevel() + $levels);
        return $collection;
    }

    public function getCurrentCategoryKey()
    {
        if (!$this->_currentCategoryKey) {
            $category = $this->_frameworkRegistry->registry('current_category');
            if ($category) {
                $this->_currentCategoryKey = $category->getId();
            } else {
                $this->_currentCategoryKey = 2;
            }
        }

        return $this->_currentCategoryKey;

    }

}
