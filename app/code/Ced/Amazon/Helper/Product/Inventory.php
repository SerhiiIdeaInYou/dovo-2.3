<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Amazon
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright © 2018 CedCommerce. All rights reserved.
 * @license     EULA http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Amazon\Helper\Product;

class Inventory implements \Ced\Integrator\Helper\Product\InventoryInterface
{
    /** @var \Magento\Framework\Api\Search\SearchCriteriaBuilderFactory $search */
    public $search;

    /** @var \Magento\Framework\Api\FilterFactory */
    public $filter;

    /** @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory */
    public $products;

    /** @var \Ced\Amazon\Api\ProfileRepositoryInterface */
    public $profile;

    /**
     * @var \Ced\Amazon\Api\QueueRepositoryInterface
     */
    public $queue;

    /** @var \Ced\Amazon\Api\Data\Queue\DataInterfaceFactory */
    public $queueDataFactory;

    /** @var \Ced\Amazon\Api\AccountRepositoryInterface */
    public $account;

    /** @var \Ced\Amazon\Api\FeedRepositoryInterface */
    public $feed;

    /** @var \Ced\Amazon\Helper\Config */
    public $config;

    /** @var \Amazon\Sdk\EnvelopeFactory */
    public $envelope;

    /** @var \Amazon\Sdk\Product\InventoryFactory */
    public $inventory;

    /** @var \Magento\CatalogInventory\Api\StockRegistryInterface */
    public $stockRegistry;

    /** @var \Magento\CatalogInventory\Api\StockStateInterface */
    public $stock;

    /** @var \Ced\Amazon\Api\StrategyRepositoryInterface */
    public $strategyRepository;

    /** @var \Magento\Catalog\Model\Product */
    public $productData;

    /** @var \Magento\InventorySalesApi\Api\GetProductSalableQtyInterface */
    public $getProductSalableQty;

    /** @var \Magento\InventorySalesApi\Model\GetAssignedStockIdForWebsiteInterface */
    public $getAssignedStockIdForWebsite;

    public function __construct(
        \Magento\Framework\Api\Search\SearchCriteriaBuilderFactory $search,
        \Magento\Framework\Api\FilterFactory $filter,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productsFactory,
        \Magento\Catalog\Model\Product $productData,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Ced\Amazon\Api\AccountRepositoryInterface $account,
        \Ced\Amazon\Api\ProfileRepositoryInterface $profile,
        \Ced\Amazon\Api\QueueRepositoryInterface $queue,
        \Ced\Amazon\Api\FeedRepositoryInterface $feed,
        \Ced\Amazon\Api\StrategyRepositoryInterface $strategyRepository,
        \Ced\Amazon\Api\Data\Queue\DataInterfaceFactory $queueDataFactory,
        \Ced\Amazon\Helper\Config $config,
        \Amazon\Sdk\EnvelopeFactory $envelopeFactory,
        \Amazon\Sdk\Product\InventoryFactory $inventory,
        \Magento\InventorySalesApi\Api\GetProductSalableQtyInterface $getProductSalableQty,
        \Magento\InventorySalesApi\Model\GetAssignedStockIdForWebsiteInterface $getAssignedStockIdForWebsite
    ) {
        $this->search = $search;
        $this->filter = $filter;

        $this->productData = $productData;
        $this->products = $productsFactory;
        $this->stock = $stockState;
        $this->stockRegistry = $stockRegistry;

        $this->strategyRepository = $strategyRepository;
        $this->account = $account;
        $this->profile = $profile;
        $this->feed = $feed;
        $this->queue = $queue;
        $this->queueDataFactory = $queueDataFactory;
        $this->config = $config;

        $this->envelope = $envelopeFactory;
        $this->inventory = $inventory;

        $this->getProductSalableQty = $getProductSalableQty;
        $this->getAssignedStockIdForWebsite = $getAssignedStockIdForWebsite;
    }

    /**
     * @TODO update inventory for 'uploaded' products only
     * Update the values for provided ids
     * @param array $ids
     * @param bool $throttle
     * @param string $priority
     * @return boolean
     * @throws \Exception
     */
    public function update(
        array $ids = [],
        $throttle = true,
        $priority = \Ced\Amazon\Model\Source\Queue\Priorty::MEDIUM
    ) {
        $status = false;
        if (isset($ids) && !empty($ids)) {
            $profileIds = $this->profile->getProfileIdsByProductIds($ids);
            if (!empty($profileIds)) {
                /** @var \Magento\Framework\Api\Filter $idsFilter */
                $idsFilter = $this->filter->create();
                $idsFilter->setField(\Ced\Amazon\Model\Profile::COLUMN_ID)
                    ->setConditionType('in')
                    ->setValue($profileIds);

                /** @var \Magento\Framework\Api\Filter $statusFilter */
                $statusFilter = $this->filter->create();
                $statusFilter->setField(\Ced\Amazon\Model\Profile::COLUMN_STATUS)
                    ->setConditionType('eq')
                    ->setValue(\Ced\Amazon\Model\Source\Profile\Status::ENABLED);

                /** @var \Magento\Framework\Api\Search\SearchCriteriaBuilder $criteria */
                $criteria = $this->search->create();
                $criteria->addFilter($statusFilter);
                $criteria->addFilter($idsFilter);
                /** @var \Ced\Amazon\Api\Data\ProfileSearchResultsInterface $profiles */
                $profiles = $this->profile->getList($criteria->create());
                /** @var \Ced\Amazon\Api\Data\AccountSearchResultsInterface $accounts */
                $accounts = $profiles->getAccounts();

                /** @var array $stores */
                $stores = $profiles->getProfileByStoreIdWise();

                /** @var \Ced\Amazon\Api\Data\AccountInterface $account */
                foreach ($accounts->getItems() as $accountId => $account) {
                    foreach ($stores as $storeId => $profiles) {
                        $envelope = null;
                        /** @var \Ced\Amazon\Api\Data\ProfileInterface $profile */
                        foreach ($profiles as $profileId => $profile) {
                            if ($profile->getAccountId() != $accountId) {
                                continue;
                            }
                            $productIds = $this->profile->getAssociatedProductIds($profileId, $storeId, $ids);
                            $specifics = [
                                'ids' => $productIds,
                                'account_id' => $accountId,
                                'marketplace' => $profile->getMarketplace(),
                                'profile_id' => $profileId,
                                'store_id' => $storeId,
                                'type' => \Amazon\Sdk\Api\Feed::PRODUCT_INVENTORY,
                            ];

                            if (!empty($productIds)) {
                                if ($throttle == true) {
                                    // queue
                                    /** @var \Ced\Amazon\Api\Data\Queue\DataInterface $queueData */
                                    $queueData = $this->queueDataFactory->create();
                                    $queueData->setAccountId($accountId);
                                    $queueData->setMarketplace($profile->getMarketplace());
                                    $queueData->setSpecifics($specifics);
                                    $queueData->setPriorty($priority);
                                    $queueData->setType(\Amazon\Sdk\Api\Feed::PRODUCT_INVENTORY);
                                    $this->queue->push($queueData);
                                } else {
                                    //TODO: add all data to unique_id in session & process via multiple ajax requests.

                                    // prepare & send: divide in chunks and process in multiple requests
                                    $envelope = $this->prepare($specifics, $envelope);
                                    $this->feed->send($envelope, $specifics);
                                }
                                $status = true;
                            }
                        }
                    }
                }
            }
        }

        return $status;
    }

    /**
     * Prepare Inventory for Amazon
     * @param array $specifics
     * @param array|null $envelope
     * @return \Amazon\Sdk\Envelope|null
     * @throws \Exception
     */
    public function prepare(array $specifics = [], $envelope = null)
    {
        if (isset($specifics) && !empty($specifics)) {
            $ids = $specifics['ids'];

            /** @var \Ced\Amazon\Api\Data\AccountInterface $account */
            $account = $this->account->getById($specifics['account_id']);

            /** @var \Ced\Amazon\Api\Data\ProfileInterface $profile */
            $profile = $this->profile->getById($specifics['profile_id']);

            if (!isset($envelope)) {
                /** @var \Amazon\Sdk\Envelope $envelope */
                $envelope = $this->envelope->create(
                    [
                        'merchantIdentifier' => $account->getConfig($profile->getMarketplaceIds())->getSellerId(),
                        'messageType' => \Amazon\Sdk\Base::MESSAGE_TYPE_INVENTORY
                    ]
                );
            }

            $storeId = $profile->getStore()->getId();

            $attributes = $profile->getProfileAttributes();

            if (isset($attributes['SKU']) && isset($attributes['SKU']['magento_attribute_code']) && $attributes['SKU']['magento_attribute_code']) {
                $profileSkuAttribute  = $attributes['SKU']['magento_attribute_code'];
            } else {
                $profileSkuAttribute = 'sku';
            }


            $attributeList = $this->config->getInventoryAttributeList();
            $attributeList = array_merge(
                $attributeList,
                ['sku', 'entity_id', 'type_id', 'extension_attributes']
            );
//            $attributeSku = $this->config->getInventoryAlternateSku();
//            if ($attributeSku) {
//                $attributeSku = explode(",", $attributeSku);
//            }
            $attributeSku=null;
            if (empty($attributeSku)) {
//                $attributeSkuList = ["sku"];
                $attributeSkuList = [$profileSkuAttribute];
            } else {
//                $attributeSkuList = $attributeSku;
                $attributeSkuList = array_merge($attributeSku, [$profileSkuAttribute]);
            }
            $attributeSkuList = array_unique($attributeSkuList);

            /** @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $products */
            $products = $this->products->create()
                ->setStoreId($storeId)
                ->addAttributeToSelect($attributeList)
                ->addAttributeToFilter('entity_id', ['in' => $ids]);
            /** @var \Magento\Catalog\Model\Product $product */
            foreach ($products as $product) {
                // case 1 : for configurable products
                if ($product->getTypeId() == 'configurable') {
                    $parentId = $product->getId();
                    $productType = $product->getTypeInstance();

                    /** @codingStandardsIgnoreStart */
                    $childIds = $productType->getChildrenIds($parentId);
                    /** @codingStandardsIgnoreEnd */
                    /** @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $products */
                    $childs = $this->products->create()
                        ->setStoreId($storeId)
                        ->addAttributeToSelect($attributeList)
                        ->addAttributeToFilter('entity_id', ['in' => $childIds[0]]);
                    /** @var \Magento\Catalog\Model\Product $child */
                    foreach ($childs as $child) {
                        foreach ($attributeSkuList as $key => $attr) {
                            $productAttr = $this->productData->load($child->getId());
                            if ($attr == 'default_value') {
                                $attr = 'sku';
                            }
                            if ($productAttr->getData($attr)) {
                                $atData = $productAttr->getData($attr);
                                $atData = explode('||', $atData);
                                foreach ($atData as $atKey => $attrData) {
                                    $inventory = $this->calculate($child, $profile, $attrData, $key.$atKey);
                                    $envelope->addInventory($inventory);
                                }
                            }
                        }
                    }
                } elseif ($product->getTypeId() == 'simple') {
                    // case 2 : for simple products

                    foreach ($attributeSkuList as $key => $attr) {
                        $productAttr = $this->productData->load($product->getId());
                        if ($attr == 'default_value') {
                            $attr = 'sku';
                        }
                        if ($productAttr->getData($attr)) {
                            $atData = $productAttr->getData($attr);
                            $atData = explode('||', $atData);
                            foreach ($atData as $atKey => $attrData) {
                                $inventory = $this->calculate($product, $profile, $attrData, $key.$atKey);
                                $envelope->addInventory($inventory);
                            }
                        }
                    }
                } elseif ($product->getTypeId() == 'bundle') {
                    // case 2 : for bundle products
                    foreach ($attributeSkuList as $key => $attr) {
                        $productAttr = $this->productData->load($product->getId());
                        if ($attr == 'default_value') {
                            $attr = 'sku';
                        }
                        if ($productAttr->getData($attr)) {
                            $atData = $productAttr->getData($attr);
                            $atData = explode('||', $atData);
                            foreach ($atData as $atKey => $attrData) {
                                $inventory = $this->calculate($product, $profile, $attrData, $key.$atKey);
                                $envelope->addInventory($inventory);
                            }
                        }
                    }
                }
            }
        }

        return $envelope;
    }

    /**
     * Apply Strategies
     * @param \Magento\Catalog\Model\Product $product
     * @param \Ced\Amazon\Api\Data\ProfileInterface $profile
     * @param \Amazon\Sdk\Product\Inventory $inventory
     * @return \Amazon\Sdk\Product\Inventory $inventory
     * @throws \Exception
     */
    public function apply($product, $profile, $inventory)
    {
        $qty = 0;

        $latency = $this->config->getInventoryLatency();

        try {
            $strategyId = $profile->getData(\Ced\Amazon\Model\Profile::COLUMN_STRATEGY_INVENTORY);
            /** @var \Ced\Amazon\Api\Data\Strategy\InventoryInterface $strategy */
            if ($this->config->getStrategyAssignByRule()) {
                // Use GetByRule() for Auto Strategy Assignment
                $strategy = $this->strategyRepository->GetByRule($product);
            } else {
                $strategy = $this->strategyRepository->getById($strategyId);
            }

            $latency = $strategy->getFulfillmentLatency();
            $override = $strategy->getInventoryOverride();
            $threshold = $strategy->getThreshold();
            $thresholdBreakpointValue = $strategy->getThresholdBreakpoint();
            $thresholdLessThanValue = $strategy->getThresholdLess();
            $thresholdGreaterThanValue = $strategy->getThresholdGreater();

            if ($override) {
                /** @var \Magento\CatalogInventory\Api\Data\StockItemInterface $item */
                $item = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
                // If stock override, get the value set as default stock
                $qty = (string)$item->getQty();
            } else {
                // Else get the correct stock from magento
                $qty = (string)$this->stock->getStockQty(
                    $product->getId(),
                    $product->getStore()->getWebsiteId()
                );
            }
            // If qty is negative
            $qty = $qty < 0 ? 0 : $qty;

            if ($threshold && is_numeric($thresholdBreakpointValue) && $thresholdBreakpointValue > 0) {
                if ($qty <= (int)$thresholdBreakpointValue && is_numeric($thresholdLessThanValue) &&
                    $thresholdLessThanValue >= 0) {
                    $qty = (string)$thresholdLessThanValue;
                } elseif ($qty > (int)$thresholdBreakpointValue && is_numeric($thresholdGreaterThanValue) &&
                    $thresholdGreaterThanValue >= 0) {
                    $qty = (string)$thresholdGreaterThanValue;
                }
            }
        } catch (\Exception $e) {
            // Silence
        }

        $inventory->setId($profile->getId() . $product->getId());
        $inventory->setData($product->getSku(), $qty, $latency);

        return $inventory;
    }

    /**
     * Calculate Qty
     * @param \Magento\Catalog\Model\Product $product
     * @param \Ced\Amazon\Api\Data\ProfileInterface $profile
     * @param $attrData
     * @param $key
     * @return \Amazon\Sdk\Product\Inventory
     * @throws \Exception
     */
    public function calculate($product, $profile, $attrData, $key)
    {
        /** @var \Amazon\Sdk\Product\Inventory $inventory */
        $inventory = $this->inventory->create();

        $strategy = $profile->getData(\Ced\Amazon\Model\Profile::COLUMN_STRATEGY);
        $strategyId = $profile->getData(\Ced\Amazon\Model\Profile::COLUMN_STRATEGY_INVENTORY);
        if (!empty($strategy) && (!empty($strategyId) || $this->config->getStrategyAssignByRule())) {
            $inventory = $this->apply($product, $profile, $inventory);
        } else {
            $override = $this->config->getInventoryOverride();
            $threshold = $this->config->getInventoryThresholdStatus();
            $thresholdBreakpointValue = $this->config->getInventoryThresholdValue();
            $thresholdLessThanValue = $this->config->getInventoryThresholdLessThan();
            $thresholdGreaterThanValue = $this->config->getInventoryThresholdGreaterThan();

            $mappings = $this->config->getInventoryAttribute();
            $accountId = $profile->getAccountId();
            if (isset($mappings[$accountId]) && !empty($mappings[$accountId])) {
                // Using Global Inventory Attribute Mapping
                $custom = (int)$product->getData($mappings[$accountId]);

                $qty = $custom ? $custom : 0;
            } else {
                // Using Magento Default Inventory Attribute
                if ($override) {
                    if ($product->getTypeId() == 'bundle') {
                        $qty = $this->getBundleQuantity($product);
                    } else {
                        /** @var \Magento\CatalogInventory\Api\Data\StockItemInterface $item */
                        $item = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());

                        if ($item->getManageStock()) {
                            if ($this->config->getSendSalableQuantity()) {
                                // if MSI is not installed then comment these lines
                                // get value from assigned stock(for chosen website in profile)
                                $stockId = $this->getAssignedStockIdForWebsite->execute($product->getStore()->getWebsite()->getCode());
                                $saleQty = $this->getProductSalableQty->execute($product->getSku(), $stockId);
                                $qty = $saleQty;
                            }
                            else {
                                // if MSI is not installed, then uncomment this
                                $qty = (string)$item->getQty();
                            }
                        } else {
                            //getvalue from config
                            $qty = $this->config->defaultQty();
                        }
                    }
                } else {
                    if ($product->getTypeId() == 'bundle') {
                        $qty = $this->getBundleQuantity($product);
                    } else {
                        // Else get the correct stock from magento
                        $qty = (string)$this->stock->getStockQty(
                            $product->getId(),
                            $product->getStore()->getWebsiteId()
                        );

                        $item = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());


                        if ($item->getManageStock()) {
                            if ($this->config->getSendSalableQuantity()) {
                                // get value from assigned stock(for chosen website in profile)
                                $stockId = $this->getAssignedStockIdForWebsite->execute($product->getStore()->getWebsite()->getCode());
                                $saleQty = $this->getProductSalableQty->execute($product->getSku(), $stockId);
                                $qty = $saleQty;
                            }else {
                                $qty = (string)$item->getQty();
                            }
                        } else {
                            //getvalue from config
                            $qty = $this->config->defaultQty();
                        }
                    }
                }
            }
            // If qty is negative
            $qty = $qty < 0 ? 0 : $qty;

            if ($threshold && is_numeric($thresholdBreakpointValue) && $thresholdBreakpointValue > 0) {
                if ($qty <= (int)$thresholdBreakpointValue && is_numeric($thresholdLessThanValue) &&
                    $thresholdLessThanValue >= 0) {
                    $qty = (string)$thresholdLessThanValue;
                } elseif ($qty > (int)$thresholdBreakpointValue && is_numeric($thresholdGreaterThanValue) &&
                    $thresholdGreaterThanValue >= 0) {
                    $qty = (string)$thresholdGreaterThanValue;
                }
            }
            $inventory->setId($profile->getId() . $product->getId() . $key);
            if ($this->config->getOverrideFulfillmentLatency() === true || 1) {
                $productInventoryFulfillmentAttributeName = $this->config->getInventoryFulfillmentLatencyAttribute();
                if ($productInventoryFulfillmentAttributeName == "default_value") {
                    $inventory->setData($attrData, $qty, $this->config->getInventoryLatency());
                } else {
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $magentoProduct = $objectManager->get('Magento\Catalog\Api\ProductRepositoryInterface')->getById($product->getId());
                    $productInventoryFulfilmentAttributeValue = $magentoProduct->getData($productInventoryFulfillmentAttributeName);
                    $inventory->setData($attrData, $qty, $productInventoryFulfilmentAttributeValue);
                }
            } else {
                $inventory->setData($attrData, $qty, $this->config->getInventoryLatency());
            }
        }

        return $inventory;
    }

    public function getBundleQuantity($product)
    {
        //load model for bundle product to get all attributes
        $product = $this->productData->load($product->getId());
        $qty = 0;

        foreach ($product->getExtensionAttributes()->getBundleProductOptions() as $option) {
            if ($option->getRequired()) {
                foreach ($option->getProductLinks() as $selection) {
                    $defaultQuantity=$selection->getQty();
                    $entityId = $selection->getEntityId();
                    $bundleOption = $this->productData->load($entityId, 'entity_id');
                    $item = $this->stockRegistry->getStockItem($bundleOption->getId(), $bundleOption->getStore()->getWebsiteId());
                    if ($item->getManageStock()) {
                        if ($this->config->getSendSalableQuantity()) {
                            // if MSI is not installed then comment these lines
                            // get value from assigned stock(for chosen website in profile)
                            $stockId = $this->getAssignedStockIdForWebsite->execute($product->getStore()->getWebsite()->getCode());
                            $saleQty = $this->getProductSalableQty->execute($product->getSku(), $stockId);
                            $quantity = $saleQty;
                        }else{
                        // if MSI is not installed, then uncomment this
                            $quantity = (string)$item->getQty();}
                    } else {
                        //getvalue from config
                        $quantity= $this->config->defaultQty();
                    }

                    // If stock override, get the value set as default stock
                    if ($qty === 0) {
                        $qty = floor($quantity/$defaultQuantity);
                    } else {
                        $qty = min([$qty,floor($quantity/$defaultQuantity)]);
                    }
                }
            }
        }
        return $qty;
    }
}
