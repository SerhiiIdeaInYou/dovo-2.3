<?php
/**
 * @category Mageants FreeGift
 * @package Mageants_FreeGift
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\FreeGift\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class addToCartObserver implements ObserverInterface
{
	/**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_productRepository;
    
	/**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;
    
	/**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
	/**
     * @var \Mageants\FreeGift\Helper\Data
     */
    protected $_freeGiftHelper;   

	/**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;
    
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;
    
    /**
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Mageants\FreeGift\Helper\Data $freeGiftHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mageants\FreeGift\Helper\Data $freeGiftHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Serialize\Serializer\Json $serializer
    ) {
        $this->_productRepository = $productRepository;
        $this->_cart = $cart;    
        $this->_storeManager = $storeManager;  
		$this->_freeGiftHelper = $freeGiftHelper;
		$this->_messageManager = $messageManager;
        $this->serializer = $serializer;
	}
	
	public function execute(\Magento\Framework\Event\Observer $observer) 
	{
		$isActive = $this->_freeGiftHelper->getFreeGiftConfig('mageants_freegift/general/active');
		if($isActive) {
			$validation = $this->_freeGiftHelper->getCartBasedValidRuleOnAddtoCart();
			
			$event = $observer->getEvent();
			$product = $event->getData('product');
			$request = $event->getData('request');
			$params = $request->getParams();

			$storeId = $this->_storeManager->getStore()->getId();

			$selectedFreeGiftSkus = '';
			$freeGiftSuperAttrs = '';
			$selectedFreeGiftQty = '';	
			$selectedFreeGiftSkusArray = '';		
			if(isset($params['selected_free_gifts']))
			{
				$selectedFreeGiftSkus = $params['selected_free_gifts'];
			}
			if(isset($params['freegift_super_attribute']))
			{
				$freeGiftSuperAttrs = $params['freegift_super_attribute'];
			}
			if(isset($params['selected_free_gifts_qty']))
			{
				$selectedFreeGiftQty = $params['selected_free_gifts_qty'];
			}

			/* check whether free gift already added or not (start)*/
					$allItems = $this->_cart->getItems();
					if(is_array($selectedFreeGiftSkus)){
						$selectedGifts = $selectedFreeGiftSkus;
					}else{
						$selectedGifts = explode(',', $selectedFreeGiftSkus);
					}
					$addedSkus=array();
					foreach ($allItems as $addedItems) {
						if($addedItems->getIsFreeItem() && in_array($addedItems->getSku(), $selectedGifts)){
							$addedSkus[$addedItems->getSku()] = true;
						}
					}
			/* check whether free gift already added or not (end)*/
			$getLastItem = $this->_cart->getItems()->addFieldToFilter('product_id',$product->getId())->setOrder('item_id','DESC')->getLastItem();
				
			$parentItemId = $getLastItem->getParentItemId();

			if($parentItemId)
			{
				$lastItemId = $parentItemId;
			} else {
				$lastItemId = $getLastItem->getItemId();
			}		
				
			if(array_key_exists('free_gift_sku', $validation)){
				foreach ($validation['free_gift_sku'] as $skus) {
					if($skus != '')
					{
						foreach(explode(',',$skus) as $sku)
						{
							/* check whether free gift already added or not (start)*/
							if(array_key_exists($sku, $addedSkus)){
								continue;
							}
							/* check whether free gift already added or not (end)*/
							if(array_key_exists('selected_free_gifts', $params)){
								if(strpos($params['selected_free_gifts'], $sku) !== false){	
									$freeGiftProduct = $this->_productRepository->get($sku); 
									$loadProduct = $this->_productRepository->getById($freeGiftProduct->getId(), false, $storeId, true);
									
									$additionalOptions = [];
									$additionalOptions[] = array(
										'label' => "Free! ",
										'value' => "Product",
									);
									
									$loadProduct->addCustomOption('additional_options', $this->serializer->serialize($additionalOptions));
									
									$freeGiftParams = array(
										'product' => $freeGiftProduct->getId(),
										'qty' => $selectedFreeGiftQty
									);

									if(isset($freeGiftSuperAttrs[$sku]))
									{
										$freeGiftParams = array(
											'product' => $loadProduct->getId(),
											'qty' => $selectedFreeGiftQty,
											'super_attribute' => $freeGiftSuperAttrs[$sku]
										);
									}
									
									$this->_cart->addProduct($loadProduct,$freeGiftParams); 

									$lastFreeItem = $this->_cart->getItems()->getLastItem();
									$lastFreeItem->setParentProductId($lastItemId);
									$lastFreeItem->setIsFreeItem(1);
									$lastFreeItem->setPrice(0);
									$lastFreeItem->setBasePrice(0);
									$lastFreeItem->setCustomPrice(0);
									$lastFreeItem->setOriginalCustomPrice(0);
									$lastFreeItem->setPriceInclTax(0);
									$lastFreeItem->setBasePriceInclTax(0);
									$lastFreeItem->getProduct()->setIsSuperMode(true);
									$lastFreeItem->save();

								}	
							}
						}
						$this->_cart->save();
						$this->_freeGiftHelper->updateConfigFreeGiftItem();
					}	
				}
			}			
			
			if(in_array(true, $validation['valid_qty_subtotal']))
			{		
				if($selectedFreeGiftSkus != '')
				{
					$selectedFreeGiftSkusArray = explode(',',$selectedFreeGiftSkus);
				}

				if(is_array($selectedFreeGiftSkusArray))
				{					
					$validatedSku = implode('', $validation['skus']);
						
					foreach($selectedFreeGiftSkusArray as $sku)
					{
						/* check whether free gift already added or not (start)*/
						if(array_key_exists($sku, $addedSkus)){
							continue;
						}
						/* check whether free gift already added or not (end)*/
						if(strpos($validatedSku, $sku)!==false)
						{							
							$freeGiftProduct = $this->_productRepository->get($sku); 
							$loadProduct = $this->_productRepository->getById($freeGiftProduct->getId(), false, $storeId, true);
							
							$additionalOptions = [];
							$additionalOptions[] = array(
								'label' => "Free! ",
								'value' => "Product",
							);
							
							$loadProduct->addCustomOption('additional_options', $this->serializer->serialize($additionalOptions));
							
							$freeGiftParams = array(
								'product' => $freeGiftProduct->getId(),
								'qty' => $selectedFreeGiftQty
							);

							if(isset($freeGiftSuperAttrs[$sku]))
							{
								$freeGiftParams = array(
									'product' => $loadProduct->getId(),
									'qty' => $selectedFreeGiftQty,
									'super_attribute' => $freeGiftSuperAttrs[$sku]
								);
							}
							
							$this->_cart->addProduct($loadProduct,$freeGiftParams); 

							$lastFreeItem = $this->_cart->getItems()->getLastItem();
							$lastFreeItem->setParentProductId($lastItemId);
							$lastFreeItem->setIsFreeItem(1);
							$lastFreeItem->setPrice(0);
							$lastFreeItem->setBasePrice(0);
							$lastFreeItem->setCustomPrice(0);
							$lastFreeItem->setOriginalCustomPrice(0);
							$lastFreeItem->setPriceInclTax(0);
							$lastFreeItem->setBasePriceInclTax(0);
							$lastFreeItem->getProduct()->setIsSuperMode(true);
							$lastFreeItem->save();
						}	
					}		
					$this->_cart->save();
				}

				$this->_freeGiftHelper->updateConfigFreeGiftItem();
			}			

		}
	}
}
