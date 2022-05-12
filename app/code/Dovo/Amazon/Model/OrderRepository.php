<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Dovo\Amazon\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterfaceFactory as SearchResultFactory;
use Magento\Sales\Api\Data\ShippingAssignmentInterface;
use Magento\Sales\Model\Order\ShippingAssignmentBuilder;
use Magento\Sales\Model\ResourceModel\Metadata;
use Magento\Framework\App\ObjectManager;
use Dovo\Amazon\Logger\Logger;

/**
 * Repository class
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderRepository extends \Magento\Sales\Model\OrderRepository
{
    /**
     * @var Metadata
     */
    protected $metadata;

    /**
     * @var SearchResultFactory
     */
    protected $searchResultFactory = null;

    /**
     * @var OrderExtensionFactory
     */
    private $orderExtensionFactory;

    /**
     * @var ShippingAssignmentBuilder
     */
    private $shippingAssignmentBuilder;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var OrderInterface[]
     */
    protected $registry = [];

    protected $_logger;
    /**
     * Constructor
     *
     * @param Metadata $metadata
     * @param SearchResultFactory $searchResultFactory
     * @param CollectionProcessorInterface|null $collectionProcessor
     * @param \Magento\Sales\Api\Data\OrderExtensionFactory|null $orderExtensionFactory
     */
    public function __construct(
        Metadata $metadata,
        SearchResultFactory $searchResultFactory,
        CollectionProcessorInterface $collectionProcessor = null,
        \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory = null,
        Logger $logger
    ) {
        $this->metadata = $metadata;
        $this->searchResultFactory = $searchResultFactory;
        $this->collectionProcessor = $collectionProcessor ?: ObjectManager::getInstance()
            ->get(\Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface::class);
        $this->orderExtensionFactory = $orderExtensionFactory ?: ObjectManager::getInstance()
            ->get(\Magento\Sales\Api\Data\OrderExtensionFactory::class);
        $this->_logger = $logger;

        parent::__construct(
            $metadata,
            $searchResultFactory,
            $collectionProcessor,
            $orderExtensionFactory
        );
    }

    /**
     * load entity
     *
     * @param int $id
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id)
    {
        if (!$id) {
            throw new InputException(__('An ID is needed. Set the ID and try again.'));
        }
        if (!isset($this->registry[$id])) {
            /** @var OrderInterface $entity */
            $entity = $this->metadata->getNewInstance()->load($id);
            if (!$entity->getEntityId()) {
                throw new NoSuchEntityException(
                    __("The entity that was requested doesn't exist. Verify the entity and try again.")
                );
            }
            $this->_logger->info($entity->getIncrementId());
            $this->setShippingAssignments($entity);
            if(strpos($entity->getIncrementId(), 'AMZN-') !== false) {
                $this->_logger->info('Jump into the Maw');
                $this->updateTaxPrices($entity);
            }
            $this->registry[$id] = $entity;
        }
        return $this->registry[$id];
    }

    /** @var OrderInterface $entity */
    protected function updateTaxPrices($entity) {
        $taxTotal = 0.0;
        try {
            $newBillingStreet = array();
            $billingStreet = $entity->getBillingAddress()->getStreet();
            if($billingStreet[0] === 'N/A') {
                $newBillingStreet[] = $billingStreet[1];
                $entity->getBillingAddress()->setStreet($newBillingStreet);
            }

            $newShippingStreet = array();
            $shippingAssignments = $entity->getExtensionAttributes()->getShippingAssignments();
            $shipping = array_shift($shippingAssignments)->getShipping();
            $shippingAddress = $shipping->getAddress();
            $shippingStreet = $shippingAddress->getStreet();
            if($shippingStreet[0] === 'N/A') {
                $newShippingStreet[] = $shippingStreet[1];
                $shippingAddress->setStreet($newShippingStreet);
            }
        } catch (\Throwable $e) {
            $this->_logger->info('Error Log: ' . $e->getMessage());
        }
        $orderItems = $entity->getItems();
        foreach ($orderItems as $item) {
            $qty = (float)$item->getQtyOrdered();
            $this->_logger->info('ID: '. $item->getSku(). 'Base Price: ' . $item->getBasePrice());
            $basePrice = round($item->getBasePrice() / 1.19, 2);
            $this->_logger->info('ID: '. $item->getSku(). 'Base Price After: ' . $basePrice);
            $taxAmount = round($basePrice * 0.19, 2);
           // $rowTotal = $item->getRowTotal() / 1.19;
            $item->setTaxPercent(19);

            $this->_logger->info('Item Row: ID'. $item->getSku(). ' Amt: ' . $taxAmount);
            $taxTotal += $taxAmount * $qty;

            $item->setTaxAmount($taxAmount * $qty);
            $item->setBaseTaxAmount($taxAmount * $qty);
            $item->setTaxInvoiced($taxAmount * $qty);
            $item->setBaseTaxInvoiced($taxAmount * $qty);
            $item->setBasePrice($basePrice);
            $item->setPrice($basePrice);
            $item->setRowTotal($basePrice * $qty);
            $item->setBaseRowTotal($basePrice * $qty);
        }

        $this->_logger->info($taxTotal);
        $shippingAmount = round(($entity->getShippingAmount() / 1.19), 2);
        $shippingTaxAmount = round(($entity->getShippingAmount() / 1.19) * 0.19, 2);
        $entity->setBaseShippingAmount($shippingAmount);
        $entity->setBaseShippingTaxAmount($shippingTaxAmount);
        $entity->setShippingTaxAmount($shippingTaxAmount);
        $entity->setBaseTaxAmount($taxTotal + $shippingTaxAmount);
        $entity->setBaseTaxInvoiced($taxTotal + $shippingTaxAmount);

        //$total = $entity->getGrandTotal();

    }

    /**
     * Find entities by criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Magento\Sales\Api\Data\OrderSearchResultInterface $searchResult */
        $searchResult = $this->searchResultFactory->create();
        $this->collectionProcessor->process($searchCriteria, $searchResult);
        $searchResult->setSearchCriteria($searchCriteria);
        foreach ($searchResult->getItems() as $order) {
            $this->setShippingAssignments($order);
            if(strpos($order->getIncrementId(), 'AMZN-') !== false) {
                $this->_logger->info('Jump into the Maw');
                $this->updateTaxPrices($order);
            }
        }
        return $searchResult;
    }

    /**
     * Register entity to delete
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $entity
     * @return bool
     */
    public function delete(\Magento\Sales\Api\Data\OrderInterface $entity)
    {
        $this->metadata->getMapper()->delete($entity);
        unset($this->registry[$entity->getEntityId()]);
        return true;
    }

    /**
     * Delete entity by Id
     *
     * @param int $id
     * @return bool
     */
    public function deleteById($id)
    {
        $entity = $this->get($id);
        return $this->delete($entity);
    }

    /**
     * Perform persist operations for one entity
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $entity
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function save(\Magento\Sales\Api\Data\OrderInterface $entity)
    {
        /** @var  \Magento\Sales\Api\Data\OrderExtensionInterface $extensionAttributes */
        $extensionAttributes = $entity->getExtensionAttributes();
        if ($entity->getIsNotVirtual() && $extensionAttributes && $extensionAttributes->getShippingAssignments()) {
            $shippingAssignments = $extensionAttributes->getShippingAssignments();
            if (!empty($shippingAssignments)) {
                $shipping = array_shift($shippingAssignments)->getShipping();
                $entity->setShippingAddress($shipping->getAddress());
                $entity->setShippingMethod($shipping->getMethod());
            }
        }
        $this->metadata->getMapper()->save($entity);
        $this->registry[$entity->getEntityId()] = $entity;
        return $this->registry[$entity->getEntityId()];
    }

    /**
     * @param OrderInterface $order
     * @return void
     */
    private function setShippingAssignments(OrderInterface $order)
    {
        /** @var OrderExtensionInterface $extensionAttributes */
        $extensionAttributes = $order->getExtensionAttributes();

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->orderExtensionFactory->create();
        } elseif ($extensionAttributes->getShippingAssignments() !== null) {
            return;
        }
        /** @var ShippingAssignmentInterface $shippingAssignment */
        $shippingAssignments = $this->getShippingAssignmentBuilderDependency();
        $shippingAssignments->setOrderId($order->getEntityId());
        $extensionAttributes->setShippingAssignments($shippingAssignments->create());
        $order->setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get the new ShippingAssignmentBuilder dependency for application code
     *
     * @return ShippingAssignmentBuilder
     * @deprecated 100.0.4
     */
    private function getShippingAssignmentBuilderDependency()
    {
        if (!$this->shippingAssignmentBuilder instanceof ShippingAssignmentBuilder) {
            $this->shippingAssignmentBuilder = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Sales\Model\Order\ShippingAssignmentBuilder::class
            );
        }
        return $this->shippingAssignmentBuilder;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Sales\Api\Data\OrderSearchResultInterface $searchResult
     * @return void
     * @deprecated 101.0.0
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Sales\Api\Data\OrderSearchResultInterface $searchResult
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $conditions[] = [$condition => $filter->getValue()];
            $fields[] = $filter->getField();
        }
        if ($fields) {
            $searchResult->addFieldToFilter($fields, $conditions);
        }
    }
}
