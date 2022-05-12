<?php

namespace Jajuma\Shariff\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Serialize\SerializerInterface;

class Shariff extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\View\Page\Title
     */
    private $pageTitle;

    /**
     * @var \Jajuma\Shariff\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Catalog\Block\Product\ImageBuilder
     */
    private $imageBuilder;

    /**
     * @var \Magento\Theme\Block\Html\Header\Logo
     */
    private $logo;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $repository;

    /**
     * Shariff constructor.
     * @param Template\Context $context
     * @param \Magento\Framework\View\Page\Title $pageTitle
     * @param \Jajuma\Shariff\Helper\Data $helper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder
     * @param \Magento\Theme\Block\Html\Header\Logo $logo
     * @param SerializerInterface $serializer
     * @param \Magento\Framework\View\Asset\Repository $repository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\View\Page\Title $pageTitle,
        \Jajuma\Shariff\Helper\Data $helper,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        SerializerInterface $serializer,
        \Magento\Framework\View\Asset\Repository $repository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->pageTitle = $pageTitle;
        $this->helper = $helper;
        $this->serializer = $serializer;
        $this->registry = $registry;
        $this->imageBuilder = $imageBuilder;
        $this->logo = $logo;
        $this->repository = $repository;
    }

    /**
     * @param string $asset
     * @return string
     */
    public function getAssetUrl($asset)
    {
        return $this->repository->createAsset($asset)->getUrl();
    }

    public function getTitle()
    {
        return $this->pageTitle->getShort();
    }

    public function getCurrentUrl()
    {
        return $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
    }

    public function showIconPosition()
    {
        $action = $this->getRequest()->getFullActionName();
        $helper = $this->helper;
        if ($action === 'cms_index_index') {
            return $helper->getConfig(\Jajuma\Shariff\Helper\Data::POSITION_HOMEPAGE_PATH);
        }

        if ($action === 'cms_page_view') {
            return $helper->getConfig(\Jajuma\Shariff\Helper\Data::POSITION_CMS_PATH);
        }

        if ($action === 'catalog_product_view') {
            return $helper->getConfig(\Jajuma\Shariff\Helper\Data::POSITION_PRODUCT_PATH);
        }

        if ($action === 'catalog_category_view') {
            return $helper->getConfig(\Jajuma\Shariff\Helper\Data::POSITION_CATEGORY_PATH);
        }

        return false;
    }

    /**
     * @function sort sharing services by sort number in array
     * @return mixed
     */
    public function toSortSharingServices()
    {
        $servicesList = $this->helper->getConfig(\Jajuma\Shariff\Helper\Data::SERVICES_LIST_PATH);
        $servicesListArray = $this->serializer->unserialize($servicesList);
        usort($servicesListArray, function ($a, $b) {
            return $a['sort_number'] - $b['sort_number'];
        });

        return $servicesListArray;
    }

    public function getMediaImage()
    {
        $action = $this->getRequest()->getFullActionName();
        if ($action === 'catalog_product_view') {
            $product = $this->registry->registry('product');
            return $this->escapeUrl($this->getImage($product, 'product_base_image')->getImageUrl());
        } else {
            return $this->logo->getLogoSrc();
        }
    }

    public function shariffHelper()
    {
        return $this->helper;
    }

    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }
}
