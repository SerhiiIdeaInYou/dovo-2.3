<?php

namespace Dovo\HeaderImage\Model\Source;

class Categories
{
    protected $_categoryCollectionFactory;
    protected $_categoryHelper;
    protected $optionsList = [];

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Helper\Category $categoryHelper
    )
    {
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_categoryHelper = $categoryHelper;
    }

    /*public function getCategory() {
        $category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());

        return $category->getUrl();
    }*/
    public function getCategoryCollection($isActive = true, $level = 0, $sortBy = false, $pageSize = false)
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

    public function generateList() {
        $categories = $this->getCategoryCollection();
        $optionsList = [];
        $optionsList[] = ['value' => '', 'label' => __(' ')];
        $optionsList[] = ['value' => 'Sag es', 'label' => __('Sag es')];
        foreach($categories as $cat) {
            $optionsList[] = ['value' => $cat->getId(), 'label' => __($cat->getName())];
        }
        return $optionsList;

    }

    public function toOptionArray() {

        return $this->generateList();
    }
}
