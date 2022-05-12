<?php
/**
 * @category  Apptrian
 * @package   Apptrian_FacebookPixel
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */

namespace Apptrian\FacebookPixel\Block;

class Code extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;
    
    /**
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;
    
    /**
     * @var \Magento\Catalog\Helper\Data
     */
    public $catalogHelper;
    
    /**
     * @var Magento\Framework\App\Http\Context
     */
    public $httpContext;
    
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    public $categoryFactory;
    
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    public $productFactory;
    
    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    public $configurableProductModel;
    
    /**
     * @var \Magento\Bundle\Model\Product\Type
     */
    public $bundleProductModel;
    
    /**
     * @var \Magento\GroupedProduct\Model\Product\Type\Grouped
     */
    public $groupedProductModel;
    
    /**
     * @var \Magento\Checkout\Model\Session
     */
    public $checkoutSession;
    
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;
    
    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    public $addressFactory;
    
    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    public $regionFactory;
    
    /**
     * Tax config model
     *
     * @var \Magento\Tax\Model\Config
     */
    public $taxConfig;
    
    /**
     * Tax display flag
     *
     * @var null|int
     */
    public $taxDisplayFlag = null;
    
    /**
     * Tax catalog flag
     *
     * @var null|int
     */
    public $taxCatalogFlag = null;
    
    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    public $localeFormat;
    
    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    public $localeResolver;
    
    /**
     * Store object
     *
     * @var null|\Magento\Store\Model\Store
     */
    public $store = null;
    
    /**
     * Store ID
     *
     * @var null|int
     */
    public $storeId = null;
    
    /**
     * Base currency code
     *
     * @var null|string
     */
    public $baseCurrencyCode = null;
    
    /**
     * Current currency code
     *
     * @var null|string
     */
    public $currentCurrencyCode = null;
    
    /**
     * Category event name
     *
     * @var null|string
     */
    public $categoryEventName = null;
    
    /**
     * Search event name
     *
     * @var null|string
     */
    public $searchEventName = null;
    
    /**
     * Search event parameter
     *
     * @var null|string
     */
    public $searchParamName = null;
    
    /**
     * Product type
     *
     * @var string
     */
    public $productType = null;
    
    /**
     * Product ID
     *
     * @var string|integer
     */
    public $productId = 0;
    
    /**
     * Product children  (contents array with IDs)
     *
     * @var array
     */
    public $contentsWithIds = [];
    
    /**
     * Bundle product options to product IDs map
     *
     * @var array
     */
    public $bundleProductOptionsData = [];
    
    /**
     * Configurable product options to product IDs map
     *
     * @var array
     */
    public $configurableProductOptionsData = [];
    
    /**
     * Configurable product allowed products array
     *
     * @var array
     */
    public $allowedProducts = [];
    
    /**
     * Data with content_ids instead of contents
     *
     * @var array
     */
    public $dataWithContentIds = [];
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $cP
     * @param \Magento\Bundle\Model\Product\Type $bundleProduct
     * @param \Magento\GroupedProduct\Model\Product\Type\Grouped $groupedProduct
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $cP,
        \Magento\Bundle\Model\Product\Type $bundleProduct,
        \Magento\GroupedProduct\Model\Product\Type\Grouped $groupedProduct,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        array $data = []
    ) {
        $this->scopeConfig              = $context->getScopeConfig();
        $this->storeManager             = $context->getStoreManager();
        $this->coreRegistry             = $coreRegistry;
        $this->catalogHelper            = $catalogHelper;
        $this->httpContext              = $httpContext;
        $this->categoryFactory          = $categoryFactory;
        $this->productFactory           = $productFactory;
        $this->configurableProductModel = $cP;
        $this->bundleProductModel       = $bundleProduct;
        $this->groupedProductModel      = $groupedProduct;
        $this->checkoutSession          = $checkoutSession;
        $this->customerFactory          = $customerFactory;
        $this->addressFactory           = $addressFactory;
        $this->regionFactory            = $regionFactory;
        $this->taxConfig                = $taxConfig;
        $this->localeFormat             = $localeFormat;
        $this->localeResolver           = $localeResolver;
        
        parent::__construct($context, $data);
    }
    
    /**
     * Used in .phtml file and returns array of data.
     *
     * @return array
     */
    public function getFacebookPixelData()
    {
        $data = [];
        
        $idConfig = $this->getConfig(
            'apptrian_facebookpixel/general/pixel_id',
            $this->getStoreId()
        );
        
        $data['id_data']               = explode(',', $idConfig);
        $data['full_action_name']      = $this->getRequest()->getFullActionName();
        $data['page_handles']          = $this->getPageHandles();
        $data['page_handles_category'] = $this->getPageHandles('category');
        $data['page_handles_product']  = $this->getPageHandles('product');
        $data['page_handles_quote']    = $this->getPageHandles('quote');
        $data['page_handles_order']    = $this->getPageHandles('order');
        $data['page_handles_search']   = $this->getPageHandles('search');
    
        return $data;
    }
    
    /**
     * Returns array of handles based on configuration.
     *
     * @param string $type
     * @return array
     */
    public function getPageHandles($type = 'general')
    {
        $handles = [];
        
        $config = $this->getConfig(
            'apptrian_facebookpixel/' . $type . '/page_handles',
            $this->getStoreId()
        );
        
        $handles = explode(',', $config);
        
        return $handles;
    }
    
    /**
     * Returns custmer data needed for advanced matching.
     *
     * @return array
     */
    public function getCustomerData()
    {
        $data         = [];
        $customerData = [];
        $address      = null;
        $addressId    = 0;
        $customer     = null;
        $customerId   = 0;
        
        $isLoggedIn = $this->httpContext->getValue(
            \Magento\Customer\Model\Context::CONTEXT_AUTH
        );
        
        if ($isLoggedIn) {
            $customerId = $this->httpContext->getValue(
                \Apptrian\FacebookPixel\Model\Customer\Context
                    ::CONTEXT_CUSTOMER_ID
            );
        }
        
        if (!$customerId) {
            return null;
        }
        
        $customer = $this->getCustomerById($customerId);
        
        if (null == $customer) {
            return null;
        }
        
        $data['external_id'] = $customerId;
        
        $data['db'] = $this->stripNonNumeric(
            $this->datetimeToDate($customer->getDob())
        );
        $data['em'] = $customer->getEmail();
        $data['fn'] = $customer->getFirstname();
        
        // 1 male, 2 female, 3 not specified
        $ge = $customer->getGender();
        
        if ($ge == 1) {
            $data['ge'] =  'm';
        }
        
        if ($ge == 2) {
            $data['ge'] =  'f';
        }
        
        $data['ln'] = $customer->getLastname();
        
        // Get billing address
        $addressId = $customer->getDefaultBilling();
        
        // If there is no billing address get shipping address
        if (!$addressId) {
            $addressId = $customer->getDefaultShipping();
        }
        
        if ($addressId) {
            $address = $this->getCustomerAddressById($addressId);
            
            $data['ct']      = $address->getCity();
            $data['country'] = $address->getCountry();
            $data['ph']      = $this->stripNonNumeric($address->getTelephone());
            $data['st']      = $this->getRegionCodeOrNameById(
                $address->getRegionId()
            );
            $data['zp']      = $address->getPostcode();
        }
        
        foreach ($data as $key => $value) {
            if ($value) {
                $customerData[$key] = $this->formatData($value);
            }
        }
        
        return $customerData;
    }
    
    /**
     * Returns category data needed for tracking.
     *
     * @return array
     */
    public function getCategoryData()
    {
        $c = $this->getCategory();
        
        if (null == $c) {
            return null;
        }
        
        $data = [];
        
        // Get event name
        $eventName = $this->getCategoryEventName();
        
        if ($eventName) {
            // Custom Parameters
            $attributeValue = '';
            $map = $this->getParameterToAttributeMap('category');
            
            foreach ($map as $parameter => $attribute) {
                $attributeValue = $this->getAttributeValue($c, $attribute);
                
                if ($attributeValue) {
                    $data[$parameter] = $this->filter($attributeValue);
                }
            }
        }
        
        return $data;
    }
    
    /**
     * Returns product data needed for tracking.
     *
     * @return array
     */
    public function getProductData()
    {
        $p = $this->getProduct();
        
        if (null == $p) {
            return null;
        }
        
        $data = [];
        
        // Set product ID property
        $this->productId = $p->getEntityId();
        
        // Get product data with contents
        $data = $this->getProductDataWithContents($p);

        return $data;
    }
    
    /**
     * Returns product data array with contents
     *
     * @param \Magento\Catalog\Model\Product $p
     * @return array
     */
    public function getProductDataWithContents($p)
    {
        $data           = [];
        $contents       = [];
        $contentsSingle = [];
        $i              = 0;
        
        $dataContentIds = [];
        $this->dataWithContentIds = [];
        
        $currencyCode = $this->getCurrentCurrencyCode();
        $value        = $this->formatPrice($this->getProductPrice($p));
        $sku          = $this->filter($p->getSku());
        $productName  = $this->filter($p->getName());
        $productType  = $p->getTypeId();
        
        // Save product type for easy access in template
        $this->productType = $productType;
        
        $contentsSingle[0]['id']         = $sku;
        $contentsSingle[0]['quantity']   = 1;
        $contentsSingle[0]['item_price'] = $value;
        
        $dataContentIds['content_ids'][0] = $sku;
        
        // Custom Parameters
        $attributeValue = '';
        $map = $this->getParameterToAttributeMap();
        
        foreach ($map as $parameter => $attribute) {
            $attributeValue = $this->getAttributeValue($p, $attribute);
            
            if ($attributeValue) {
                $contentsSingle[0][$parameter] = $this->filter($attributeValue);
                $dataContentIds[$parameter] = $this->filter($attributeValue);
            }
        }
        
        // Set content_name if it is not empty
        $contentNameAttrib = $this->getConfig(
            'apptrian_facebookpixel/product/content_name',
            $this->getStoreId()
        );
        if ($contentNameAttrib) {
            $contentName = $this->getAttributeValue($p, $contentNameAttrib);
            if ($contentName) {
                $data['content_name'] = $this->filter($contentName);
                $dataContentIds['content_name'] = $this->filter($contentName);
            }
        }
        
        // Set content_category if it is not empty
        $contentCategoryAttrib = $this->getConfig(
            'apptrian_facebookpixel/product/content_category',
            $this->getStoreId()
        );
        if ($contentCategoryAttrib) {
            $contentCategory = $this->getAttributeValue($p, $contentCategoryAttrib);
            if ($contentCategory) {
                $data['content_category'] = $this->filter($contentCategory);
                $dataContentIds['content_category'] = $this->filter($contentCategory);
            }
        }
        
        // Set google_product_category if it is not empty
        $googleProductCategoryAttrib = $this->getConfig(
            'apptrian_facebookpixel/product/google_product_category',
            $this->getStoreId()
        );
        if ($googleProductCategoryAttrib) {
            $googleProductCategory = $this->getAttributeValue($p, $googleProductCategoryAttrib);
            if ($googleProductCategory) {
                $data['google_product_category'] = $this->filter($googleProductCategory);
                $dataContentIds['google_product_category'] = $this->filter($googleProductCategory);
            }
        }
        
        $data['contents'] = $contentsSingle;
        
        $data['content_type'] = 'product';
        $dataContentIds['content_type'] = 'product';
        
        // Event options
        // 1 = parent
        // 2 = children
        // 3 = both
        $option = (int) $this->getConfig(
            'apptrian_facebookpixel/product/ident_' . $productType,
            $this->getStoreId()
        );
        
        // Check product type and find all variant SKUs
        if ($productType == 'configurable'
            || $productType == 'bundle'
            || $productType == 'grouped'
        ) {
            $children = $this->getProductChildren($p);
            $childId  = 0;
            $childSKu = '';
            
            foreach ($children as $child) {
                // Must load child product to get all data
                $child = $this->getProductById($child->getEntityId());
                
                if ($productType == 'configurable') {
                    $this->allowedProducts[$i] = $child;
                }
                
                $childId  = $this->filter($child->getEntityId());
                $childSku = $this->filter($child->getSku());
                
                // Required parameter id
                $contents[$i]['id'] = $childSku;
                $this->contentsWithIds[$childId]['id'] = $childSku;
                $dataContentIds['content_ids'][$i] = $childSku;
                if ($option == 3) {
                    // Optional parameter item_group_id
                    $contents[$i]['item_group_id'] = $sku;
                    $this->contentsWithIds[$childId]['item_group_id'] = $sku;
                }
                
                // Required parameter quantity
                $contents[$i]['quantity'] = 1;
                $this->contentsWithIds[$childId]['quantity'] = 1;
                
                // Optional parameter item_price
                $contents[$i]['item_price'] = $this->formatPrice($this->getProductPrice($child));
                $this->contentsWithIds[$childId]['item_price'] = $this->formatPrice($this->getProductPrice($child));
                
                // Optional custom parameters
                foreach ($map as $parameter => $attribute) {
                    $attributeValue = $this->getAttributeValue($child, $attribute);
                    
                    if ($attributeValue) {
                        $contents[$i][$parameter] = $this->filter($attributeValue);
                        $this->contentsWithIds[$childId][$parameter] = $this->filter($attributeValue);
                    }
                }
                
                // Do not forget to increment
                $i++;
            }
            
            // Must be done like this because you need contentsWithIds in any case
            if ($option !== 1) {
                // Reset contents
                $data['contents'] = [];
                // Set contents
                $data['contents'] = $contents;
                
                if ($option == 3) {
                    $dataContentIds['item_group_id'] = $sku;
                }
            } else {
                // 0 = product
                // 1 = product_group
                $useProductGroup = (int) $this->getConfig(
                    'apptrian_facebookpixel/product/content_type_' . $productType,
                    $this->getStoreId()
                );
                
                if ($useProductGroup) {
                    $data['content_type'] = 'product_group';
                    $dataContentIds['content_type'] = 'product_group';
                }
                
                // Reset content_ids
                $dataContentIds['content_ids'] = [];
                
                // Set content_ids
                $dataContentIds['content_ids'][0] = $sku;
            }
            
            // If bundle product type set options array
            if ($productType == 'bundle') {
                $this->bundleProductOptionsData = $this->getBundleProductOptionsData($p);
            }
            
            // If configurable product type set options array
            if ($productType == 'configurable') {
                $this->configurableProductOptionsData = $this->getConfigurableProductOptionsData($p);
            }
        } else {
            // simple, downlodable, virtual
            if ($option == 3) {
                $parentSku = $this->getParentProductSku($p->getEntityId());
                
                if ($parentSku) {
                    // Optional parameter item_group_id
                    $data['contents'][0]['item_group_id'] = $parentSku;
                    $dataContentIds['item_group_id']      = $parentSku;
                }
            }
        }
        
        $data['value']              = $value;
        $dataContentIds['value']    = $value;
        $data['currency']           = $currencyCode;
        $dataContentIds['currency'] = $currencyCode;
        
        $useContentIds = (bool) $this->getConfig(
            'apptrian_facebookpixel/product/use_content_ids',
            $this->getStoreId()
        );
        
        if ($useContentIds) {
            $this->dataWithContentIds = $dataContentIds;
        }
        
        return $data;
    }
    
    /**
     * Returns product data array with content_ids
     *
     * @return array
     */
    public function getProductDataWithContentIds()
    {
        return $this->dataWithContentIds;
    }
    
    /**
     * Returns configuration value for detect_selected_sku
     *
     * @return bool
     */
    public function isDetectSelectedSkuEnabled($productType)
    {
        return $this->getConfig(
            'apptrian_facebookpixel/product/detect_selected_sku_'.$productType,
            $this->getStoreId()
        );
    }
    
    /**
     * Returns product type
     *
     * @return array
     */
    public function getProductType()
    {
        return $this->productType;
    }
    
    /**
     * Returns contents with IDs array
     *
     * @return array
     */
    public function getContentsWithIds()
    {
        return $this->contentsWithIds;
    }
    
    /**
     * Returns price decimal sign
     *
     * @return string
     */
    public function getPriceDecimalSymbol()
    {
        $decimalSymbol = '';
        $locale        = $this->localeResolver->getLocale();
        $priceFormat   = $this->localeFormat->getPriceFormat($locale);
        $decimalSymbol = $priceFormat['decimalSymbol'];
        
        return $decimalSymbol;
    }
    
    /**
     * Returns data needed for tracking from order object.
     *
     * @return array|null
     */
    public function getOrderData()
    {
        return $this->getOrderOrQuoteData('order');
    }
    
    /**
     * Returns data needed for tracking from quote object.
     *
     * @return array|null
     */
    public function getQuoteData()
    {
        return $this->getOrderOrQuoteData('quote');
    }
    
    /**
     * Returns data needed for tracking based on config group.
     *
     * @return array|null
     */
    public function getOrderOrQuoteData($group)
    {
        $obj = null;
        
        if ($group == 'order') {
            $obj = $this->checkoutSession->getLastRealOrder();
        }
        
        if ($group == 'quote') {
            $obj = $this->checkoutSession->getQuote();
        }
        
        if (null == $obj) {
            return null;
        }
        
        $objId = $obj->getId();
        
        if (!$objId) {
            return null;
        }
        
        $allItems        = $obj->getAllItems();
        $allVisibleItems = $obj->getAllVisibleItems();

        $data         = [];
        $items        = [];
        $itemId       = '';
        $parentItemId = '';
        $i            = 0;
        $contents     = [];
        $product      = null;
        $productId    = 0;
        $productType  = '';
        $parent       = null;
        $parentId     = 0;
        $storeId      = $this->getStoreId();
        $numItems     = 0;
        $taxFlag      = $this->getDisplayTaxFlag();

        // Custom Parameters
        $attributeValue = '';
        $map = $this->getParameterToAttributeMap($group);

        foreach ($allVisibleItems as $item) {
            $itemId = $item->getItemId();
            
            $items[$itemId]['item_id']        = $itemId;
            $items[$itemId]['parent_item_id'] = $item->getParentItemId();
            $items[$itemId]['product_id']     = $item->getProductId();
            $items[$itemId]['product_type']   = $item->getProductType();
            $items[$itemId]['sku']            = $this->filter($item->getSku());
            $items[$itemId]['name']           = $this->filter($item->getName());
            $items[$itemId]['store_id']       = $item->getStoreId();
            
            if ($taxFlag) {
                $items[$itemId]['price'] = $this->formatPrice($item->getPriceInclTax());
            } else {
                $items[$itemId]['price'] = $this->formatPrice($item->getPrice());
            }
            
            if ($group == 'quote') {
                $items[$itemId]['qty'] = round($item->getQty(), 0);
            } else {
                $items[$itemId]['qty'] = round($item->getQtyOrdered(), 0);
            }
        }

        foreach ($allItems as $item) {
            $itemId       = $item->getItemId();
            $parentItemId = $item->getParentItemId();
            
            if ($parentItemId) {
                $items[$parentItemId]['children'][$itemId]['item_id']        = $itemId;
                $items[$parentItemId]['children'][$itemId]['parent_item_id'] = $parentItemId;
                $items[$parentItemId]['children'][$itemId]['product_id']     = $item->getProductId();
                $items[$parentItemId]['children'][$itemId]['product_type']   = $item->getProductType();
                $items[$parentItemId]['children'][$itemId]['sku']            = $this->filter($item->getSku());
                $items[$parentItemId]['children'][$itemId]['name']           = $this->filter($item->getName());
                $items[$parentItemId]['children'][$itemId]['store_id']       = $item->getStoreId();
                
                if ($taxFlag) {
                    $items[$parentItemId]['children'][$itemId]['price'] = $this->formatPrice($item->getPriceInclTax());
                } else {
                    $items[$parentItemId]['children'][$itemId]['price'] = $this->formatPrice($item->getPrice());
                }
                
                if ($group == 'quote') {
                    if ($items[$parentItemId]['product_type'] == 'configurable') {
                        $q = $items[$parentItemId]['qty'];
                        $items[$parentItemId]['children'][$itemId]['qty'] = $q;
                    } else {
                        $items[$parentItemId]['children'][$itemId]['qty'] = round($item->getQty(), 0);
                    }
                } else {
                    $items[$parentItemId]['children'][$itemId]['qty'] = round($item->getQtyOrdered(), 0);
                }
            }
        }
        
        foreach ($items as $item) {
            $productId   = $item['product_id'];
            $productType = $item['product_type'];
            $storeId     = $item['store_id'];
            
            // Event options
            // 1 = poduct/parent
            // 2 = children/child
            // 3 = children/child/product and parent
            $option = (int) $this->getConfig(
                'apptrian_facebookpixel/' . $group . '/ident_' . $productType,
                $storeId
            );
            
            $product    = $this->getProductById($productId, $storeId);
            $productSku = $this->filter($product->getSku());
            
            if ($productType == 'bundle' || $productType == 'configurable') {
                if ($option == 1) {
                    // Option 1 means show parent SKU only
                    $qty = $item['qty'];
                    $contents[$i]['id']         = $productSku;
                    $contents[$i]['quantity']   = $qty;
                    $contents[$i]['item_price'] = $item['price'];
                    
                    // Custom Parameters
                    foreach ($map as $parameter => $attribute) {
                        $attributeValue = $this->getAttributeValue(
                            $product,
                            $attribute
                        );
                        
                        if ($attributeValue) {
                            $contents[$i][$parameter] = $this
                                ->filter($attributeValue);
                        }
                    }
                    
                    $numItems += $qty;
                    
                    $i++;
                } else {
                    // Option 2. or 3. means show children SKUs
                    
                    $children  = $item['children'];
                    
                    foreach ($children as $child) {
                        $childProductId = $child['product_id'];
                        $childProduct   = $this->getProductById($childProductId, $storeId);
                        
                        $qty = $child['qty'];
                        
                        if ($productType == 'bundle') {
                            // Budle products may have global qty higher than 1
                            $parentItemId = $child['parent_item_id'];
                            $globalQty    = $items[$parentItemId]['qty'];
                            $qty          = $qty * $globalQty;
                        }
                        
                        $contents[$i]['id']         = $this->filter($childProduct->getSku());
                        $contents[$i]['quantity']   = $qty;
                        $contents[$i]['item_price'] = $child['price'];
                        
                        // For configurable you must use congfigurable price
                        // child price is 0
                        if ($productType == 'configurable') {
                            $contents[$i]['item_price'] = $item['price'];
                        }
                        
                        if ($option == 3) {
                            // Option 3 add parent product SKU on children
                            $contents[$i]['item_group_id'] = $productSku;
                        }
                        
                        // Custom Parameters
                        foreach ($map as $parameter => $attribute) {
                            $attributeValue = $this->getAttributeValue(
                                $childProduct,
                                $attribute
                            );
                            
                            if ($attributeValue) {
                                $contents[$i][$parameter] = $this
                                    ->filter($attributeValue);
                            }
                        }
                        
                        $numItems += $qty;
                        
                        $i++;
                    }
                }
            } else {
                // grouped, simple, virtual, downloadable products
                
                $qty = $item['qty'];
                $contents[$i]['id']         = $productSku;
                $contents[$i]['quantity']   = $qty;
                $contents[$i]['item_price'] = $item['price'];
                
                // Reset parent ID
                $parentId = 0;
                
                if ($productType == 'grouped') {
                    if ($option == 3) {
                        // Get parent grouped product ID
                        $parentId = $this->getParentGroupedProductId($productId);
                    }
                } else {
                    if ($option == 2) {
                        // Get parent product ID
                        $parentId = $this->getParentProductId($productId);
                    }
                }
                
                if ($parentId) {
                    $parent = $this->getProductById($parentId, $storeId);
                    if ($parent) {
                        $contents[$i]['item_group_id'] = $this->filter($parent->getSku());
                    }
                }
                
                // Custom Parameters
                foreach ($map as $parameter => $attribute) {
                    $attributeValue = $this->getAttributeValue(
                        $product,
                        $attribute
                    );
                    
                    if ($attributeValue) {
                        $contents[$i][$parameter] = $this
                            ->filter($attributeValue);
                    }
                }
                
                $numItems += $qty;
                
                $i++;
            }
        }
        
        // Order ID
        $orderIdParam = (string) $this->getConfig(
            'apptrian_facebookpixel/' . $group . '/order_id_param',
            $storeId
        );
        if ($orderIdParam) {
            $data[$orderIdParam] = (string) $obj->getId();
        }
        
        // Order increment ID
        $orderIncrementIdParam = (string) $this->getConfig(
            'apptrian_facebookpixel/' . $group . '/order_increment_id_param',
            $storeId
        );
        if ($orderIncrementIdParam) {
            $data[$orderIncrementIdParam] = (string) $obj->getIncrementId();
        }
        
        // Quote ID
        $quoteIdParam = (string) $this->getConfig(
            'apptrian_facebookpixel/' . $group . '/quote_id_param',
            $storeId
        );
        if ($quoteIdParam) {
            if ($group == 'quote') {
                $data[$quoteIdParam] = (string) $obj->getId();
            } else {
                $data[$quoteIdParam] = (string) $obj->getQuoteId();
            }
        }
        
        $data['contents']     = $contents;
        $data['content_type'] = 'product';
        $data['num_items']    = $numItems;
        $data['value']        = $this->formatPrice($obj->getGrandTotal());
        
        if ($group == 'quote') {
            $data['currency'] = $obj->getQuoteCurrencyCode();
        } else {
            $data['currency'] = $obj->getOrderCurrencyCode();
        }
        
        return $data;
    }
    
    /**
     * Returns category data needed for tracking.
     *
     * @return array|null
     */
    public function getSearchData()
    {
        $data = [];
        
        $requestParams = explode(
            ',',
            $this->getConfig(
                'apptrian_facebookpixel/search/request_params',
                $this->getStoreId()
            )
        );
        
        $searchStrings = [];
        $p = '';
        
        foreach ($requestParams as $param) {
            // If prameter is array
            if (strpos($param, '[') !== false) {
                $p = substr($param, 0, strpos($param, '['));
            } else {
                $p = $param;
            }
            
            $rp = $this->getRequest()->getParam($p);
            
            if (!empty($rp)) {
                if (is_array($rp)) {
                    $v = trim(implode(',', $rp), ',');
                    if (!empty($v)) {
                        $searchStrings[] = $v;
                    }
                }
                
                if (is_string($rp)) {
                    $searchStrings[] = $this->filter(trim($rp));
                }
            }
        }
        
        $paramName    = $this->getSearchParamName();
        $searchString = implode(',', $searchStrings);

        if ($paramName && $searchString) {
            $data[$paramName] = $searchString;
        }
        
        return $data;
    }
    
    /**
     * Returns configuration value for event name
     *
     * @param string $group
     * @return string
     */
    public function getEventName($group)
    {
        return $this->filter(
            (string) $this->getConfig(
                'apptrian_facebookpixel/' . $group . '/event_name',
                $this->getStoreId()
            )
        );
    }
    
    /**
     * Returns configuration value for category event name
     *
     * @return string
     */
    public function getCategoryEventName()
    {
        if ($this->categoryEventName === null) {
            $this->categoryEventName = $this->getEventName('category');
        }
        
        return $this->categoryEventName;
    }
    
    /**
     * Returns configuration value for search event name
     *
     * @return string
     */
    public function getSearchEventName()
    {
        if ($this->searchEventName === null) {
            $this->searchEventName = $this->getEventName('search');
        }
        
        return $this->searchEventName;
    }
    
    /**
     * Returns configuration value for event param
     *
     * @param string $group
     * @return string
     */
    public function getParamName($group)
    {
        return $this->filter(
            (string) $this->getConfig(
                'apptrian_facebookpixel/' . $group . '/param_name',
                $this->getStoreId()
            )
        );
    }
    
    /**
     * Returns configuration value for search event name
     *
     * @return string
     */
    public function getSearchParamName()
    {
        if ($this->searchParamName === null) {
            $this->searchParamName = $this->getParamName('search');
        }
        
        return $this->searchParamName;
    }
    
    /**
     * Based on provided configuration path returns configuration value.
     *
     * @param string $configPath
     * @param string|int $scope
     * @return string
     */
    public function getConfig($configPath, $scope = 'default')
    {
        return $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scope
        );
    }
    
    /**
     * Removes new lines and tabs from the string
     * and prepares string for JavaScript.
     *
     * @param string $str
     * @return string
     */
    public function filter($str)
    {
        return trim(str_replace(["\t","\n","\r\n","\r"], '', $str));
    }
    
    /**
     * Returns store object
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        if ($this->store === null) {
            $this->store = $this->storeManager->getStore();
        }
        
        return $this->store;
    }
    
    /**
     * Returns Store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        if ($this->storeId === null) {
            $this->storeId = $this->getStore()->getId();
        }
        
        return $this->storeId;
    }
    
    /**
     * Returns base currency code
     * (3 letter currency code like USD, GBP, EUR, etc.)
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        if ($this->baseCurrencyCode === null) {
            $this->baseCurrencyCode = strtoupper(
                $this->getStore()->getBaseCurrencyCode()
            );
        }
        
        return $this->baseCurrencyCode;
    }
    
    /**
     * Returns current currency code
     * (3 letter currency code like USD, GBP, EUR, etc.)
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        if ($this->currentCurrencyCode === null) {
            $this->currentCurrencyCode = strtoupper(
                $this->getStore()->getCurrentCurrencyCode()
            );
        }
        
        return $this->currentCurrencyCode;
    }
    
    /**
     * Returns flag based on "Stores > Cofiguration > Sales > Tax
     * > Price Display Settings > Display Product Prices In Catalog"
     * Returns 0 or 1 instead of 1, 2, 3.
     *
     * @return int
     */
    public function getDisplayTaxFlag()
    {
        if ($this->taxDisplayFlag === null) {
            // Tax Display
            // 1 - excluding tax
            // 2 - including tax
            // 3 - including and excluding tax
            $flag = $this->taxConfig->getPriceDisplayType($this->getStoreId());
            
            // 0 means price excluding tax, 1 means price including tax
            if ($flag == 1) {
                $this->taxDisplayFlag = 0;
            } else {
                $this->taxDisplayFlag = 1;
            }
        }
        
        return $this->taxDisplayFlag;
    }
    
    /**
     * Returns Stores > Cofiguration > Sales > Tax > Calculation Settings
     * > Catalog Prices configuration value
     *
     * @return int
     */
    public function getCatalogTaxFlag()
    {
        // Are catalog product prices with tax included or excluded?
        if ($this->taxCatalogFlag === null) {
            $this->taxCatalogFlag = (int) $this->getConfig(
                'tax/calculation/price_includes_tax',
                $this->getStoreId()
            );
        }
        
        // 0 means excluded, 1 means included
        return $this->taxCatalogFlag;
    }
    
    /**
     * Returns product price.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductPrice($product)
    {
        $price = 0.0;
        
        switch ($product->getTypeId()) {
            case 'bundle':
                $price =  $this->getBundleProductPrice($product);
                break;
            case 'configurable':
                $price = $this->getConfigurableProductPrice($product);
                break;
            case 'grouped':
                $price = $this->getGroupedProductPrice($product);
                break;
            default:
                $price = $this->getFinalPrice($product);
        }
        
        return $price;
    }
    
    /**
     * Returns bundle product price.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getBundleProductPrice($product)
    {
        $includeTax = (bool) $this->getDisplayTaxFlag();
        
        return $this->getFinalPrice(
            $product,
            $product->getPriceModel()->getTotalPrices(
                $product,
                'min',
                $includeTax,
                1
            )
        );
    }
    
    /**
     * Returns configurable product price.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getConfigurableProductPrice($product)
    {
        if ($product->getFinalPrice() === 0) {
            $simpleCollection = $product->getTypeInstance()
                ->getUsedProducts($product);
            
            foreach ($simpleCollection as $simpleProduct) {
                if ($simpleProduct->getPrice() > 0) {
                    return $this->getFinalPrice($simpleProduct);
                }
            }
        }
        
        return $this->getFinalPrice($product);
    }
    
    /**
     * Returns grouped product price.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getGroupedProductPrice($product)
    {
        $assocProducts = $product->getTypeInstance(true)
            ->getAssociatedProductCollection($product)
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('tax_class_id')
            ->addAttributeToSelect('tax_percent');
        
        $minPrice = INF;
        foreach ($assocProducts as $assocProduct) {
            $minPrice = min($minPrice, $this->getFinalPrice($assocProduct));
        }
        
        return $minPrice;
    }
    
    /**
     * Returns final price.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $price
     * @return string
     */
    public function getFinalPrice($product, $price = null)
    {
        if ($price === null) {
            $price = $product->getFinalPrice();
        }
        
        if ($price === null) {
            $price = $product->getData('special_price');
        }
        
        $productType = $product->getTypeId();
        
        // 1. Convert to current currency if needed
        
        // Convert price if base and current currency are not the same
        // Except for configurable products they already have currency converted
        if (($this->getBaseCurrencyCode() !== $this->getCurrentCurrencyCode())
            && $productType != 'configurable'
        ) {
            // Convert to from base currency to current currency
            $price = $this->getStore()->getBaseCurrency()
                ->convert($price, $this->getCurrentCurrencyCode());
        }
        
        // 2. Apply tax if needed
        
        // Simple, Virtual, Downloadable products price is without tax
        // Grouped products have associated products without tax
        // Bundle products price already have tax included/excluded
        // Configurable products price already have tax included/excluded
        if ($productType != 'configurable' && $productType != 'bundle') {
            // If display tax flag is on and catalog tax flag is off
            if ($this->getDisplayTaxFlag() && !$this->getCatalogTaxFlag()) {
                $price = $this->catalogHelper->getTaxPrice(
                    $product,
                    $price,
                    true,
                    null,
                    null,
                    null,
                    $this->getStoreId(),
                    false,
                    false
                );
            }
        }
        
        // Case when catalog prices are with tax but display tax is set to
        // to exclude tax. Applies for all products except for bundle
        if ($productType != 'bundle') {
            // If display tax flag is off and catalog tax flag is on
            if (!$this->getDisplayTaxFlag() && $this->getCatalogTaxFlag()) {
                $price = $this->catalogHelper->getTaxPrice(
                    $product,
                    $price,
                    false,
                    null,
                    null,
                    null,
                    $this->getStoreId(),
                    true,
                    false
                );
            }
        }
        
        return $price;
    }
    
    /**
     * Returns formated price.
     *
     * @param string $price
     * @param bool $toFloat
     * @param string $currencyCode
     * @return string
     */
    public function formatPrice($price, $toFloat = true, $currencyCode = '')
    {
        $formatedPrice = number_format($price, 2, '.', '');
        
        if ($currencyCode) {
            return $formatedPrice . ' ' . $currencyCode;
        } else {
            if ($toFloat) {
                return round((float)$formatedPrice, 2);
            } else {
                return $formatedPrice;
            }
        }
    }
    
    /**
     * Returns object attribute value or values. Third param is optional, if
     * set to false it will return array of values for multiselect attributes.
     *
     * @param \Magento\Catalog\Model\Category|\Magento\Catalog\Model\Product $o
     * @param string $attrCode
     * @param bool $toString
     * @return string
     */
    public function getAttributeValue($o, $attrCode, $toString = true)
    {
        $attrValue = '';
        
        if ($o->getData($attrCode)) {
            $attrValue = $o->getAttributeText($attrCode);
            
            if (!$attrValue) {
                $attrValue = $o->getData($attrCode);
            }
        }
        
        if ($toString && is_array($attrValue)) {
            $attrValue = implode(', ', $attrValue);
        }
        
        return $attrValue;
    }
    
    /**
     * Returns array map from parameter mapping configuration.
     * Default is 'product' but you can specify for mapping others.
     *
     * @return array
     */
    public function getParameterToAttributeMap($type = 'product')
    {
        $map = [];
        
        $data = $this->getConfig(
            'apptrian_facebookpixel/' . $type . '/mapping',
            $this->getStoreId()
        );
        
        if (!$data) {
            return $map;
        }
        
        $pairs = explode('|', $data);
        
        foreach ($pairs as $pair) {
            $pairArray = explode('=', $pair);
            
            if (isset($pairArray[0]) && isset($pairArray[1])) {
                $cleanedParameter = trim($pairArray[0]);
                $cleanedAttribute = trim($pairArray[1]);
                
                if ($cleanedParameter && $cleanedAttribute) {
                    $map[$cleanedParameter] = $cleanedAttribute;
                }
            }
        }
        
        return $map;
    }
    
    /**
     * Returns category object. If ID is not supplied it will rerurn
     * current category from registry.
     *
     * @param null|int $id
     * @param int|string $storeId
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory($id = null, $storeId = '')
    {
        $category = null;
        
        if ($id) {
            $category = $this->getCategoryById($id, $storeId);
        } else {
            $category = $this->coreRegistry->registry('current_category');
        }
        
        return $category;
    }
    
    /**
     * Returns category object loaded by ID.
     * Used in getCategoryData() method to retreive category attributes.
     *
     * @param int $id
     * @param int|string $storeId
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategoryById($id, $storeId = '')
    {
        if (!$storeId) {
            $storeId = $this->getStoreId();
        }
        
        return $this->categoryFactory->create()->setStoreId($storeId)->load($id);
    }
    
    /**
     * Returns product object. If ID is not supplied it will rerurn
     * current product from registry.
     *
     * @param null|int $id
     * @param int|string $storeId
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct($id = null, $storeId = '')
    {
        $product = null;
        
        if ($id) {
            $product = $this->getProductById($id, $storeId);
        } else {
            $product = $this->coreRegistry->registry('current_product');
        }
        
        return $product;
    }
    
    /**
     * Returns product object loaded by ID.
     * Used in getOrderOrQuoteData() method to retreive product attributes.
     *
     * @param int $id
     * @param int|string $storeId
     * @return \Magento\Catalog\Model\Product
     */
    public function getProductById($id, $storeId = '')
    {
        if (!$storeId) {
            $storeId = $this->getStoreId();
        }
        
        return $this->productFactory->create()->setStoreId($storeId)->load($id);
    }
    
    /**
     * Returns product object loaded by SKU.
     * Used in getOrderOrQuoteData() method to retreive product attributes.
     *
     * @param string $sku
     * @param int|string $storeId
     * @return \Magento\Catalog\Model\Product
     */
    public function getProductBySku($sku, $storeId = '')
    {
        if (!$storeId) {
            $storeId = $this->getStoreId();
        }
        
        $product = $this->productFactory->create();
        return $product->setStoreId($storeId)->load($product->getIdBySku($sku));
    }
    
    /**
     * Returns product children collection
     * Used in getOrderOrQuoteData() method to retreive product attributes.
     *
     * @param \Magento\Catalog\Model\Product $p
     * @return array|\Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductChildren($p)
    {
        $children = [];
        $productType = $p->getTypeId();
        
        switch ($productType) {
            case 'bundle':
                $children = $p->getTypeInstance(true)->getSelectionsCollection(
                    $p->getTypeInstance(true)->getOptionsIds($p),
                    $p
                );
                break;
            case 'configurable':
                $children = $p->getTypeInstance()->getUsedProducts($p);
                break;
            case 'grouped':
                $children = $p->getTypeInstance()->getAssociatedProducts($p);
                break;
            default:
                $children = [];
        }
        
        return $children;
    }
    
    /**
     * Returns bundle product options data used for detection of selected SKUs.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getBundleProductOptionsData($product)
    {
        $optionsData = [];
        
        // Get all the selection products used in bundle product
        $selectionCollection = $product->getTypeInstance(true)
            ->getSelectionsCollection(
                $product->getTypeInstance(true)->getOptionsIds($product),
                $product
            );
        
        $selectionArray = [];
        
        foreach ($selectionCollection as $selection) {
            $selectionArray['product_id'] = $selection->getProductId();
            $selectionArray['product_quantity'] = (float) $selection->getSelectionQty();
            $optionsData[$selection->getOptionId()][$selection->getSelectionId()] = $selectionArray;
        }
        
        return $optionsData;
    }
    
    /**
     * Get Options for Configurable Product Options
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getConfigurableProductOptionsData($product)
    {
        $options = [];
        
        if (empty($this->allowedProducts)) {
            $this->allowedProducts = $this->getAllowedProducts($product);
        }
        
        $allowAttributes = $this->getAllowedAttributes($product);

        foreach ($this->allowedProducts as $p) {
            $productId = $p->getId();
            foreach ($allowAttributes as $attribute) {
                $productAttribute   = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue     = $p
                    ->getData($productAttribute->getAttributeCode());
                
                $options[$productId][$productAttributeId] = $attributeValue;
            }
        }
        
        return $options;
    }
    
    /**
     * Get allowed attributes
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getAllowedAttributes($product)
    {
        return $product->getTypeInstance()->getConfigurableAttributes($product);
    }
    
    /**
     * Get Allowed Products
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product[]
     */
    public function getAllowedProducts($product)
    {
        $products = [];
        
        $allProducts = $product->getTypeInstance()->getUsedProducts($product);
        
        foreach ($allProducts as $p) {
            $products[] = $p;
        }
        
        return $products;
    }
    
    /**
     * Based on product ID returns parent product SKU
     * or empty string for no parent sku.
     *
     * @param int $productId
     * @return string
     */
    public function getParentProductSku($productId)
    {
        $parentSku = '';
        
        $parentId = $this->getParentProductId($productId);
                
        if ($parentId) {
            $parent = $this->getProductById($parentId);
            if ($parent) {
                $parentSku = $this->filter($parent->getSku());
            }
        }
        
        return $parentSku;
    }
    
    /**
     * Based on product ID returns parent product ID or null for no parent.
     *
     * @param int $productId
     * @return null|int
     */
    public function getParentProductId($productId)
    {
        $parentId = null;
        
        // Configurable
        $parentId = $this->getParentConfigurableProductId($productId);
        if ($parentId) {
            return $parentId;
        }
        
        // Bundle
        $parentId = $this->getParentBundleProductId($productId);
        if ($parentId) {
            return $parentId;
        }
        
        // Grouped
        $parentId = $this->getParentGroupedProductId($productId);
        if ($parentId) {
            return $parentId;
        }
        
        return $parentId;
    }
    
    /**
     * Based on product ID returns parent bundle product ID or null for no parent.
     *
     * @param int $productId
     * @return null|int
     */
    public function getParentBundleProductId($productId)
    {
        $parentId = null;
        
        // Bundle
        $parentIds = $this->bundleProductModel
            ->getParentIdsByChild($productId);
        
        if (!empty($parentIds) && isset($parentIds[0])) {
            $parentId = $parentIds[0];
            return $parentId;
        }
        
        return $parentId;
    }
    
    /**
     * Based on product ID returns parent configurable product ID or null for no parent.
     *
     * @param int $productId
     * @return null|int
     */
    public function getParentConfigurableProductId($productId)
    {
        $parentId = null;
        
        // Configurable
        $parentIds = $this->configurableProductModel
            ->getParentIdsByChild($productId);
        
        if (!empty($parentIds) && isset($parentIds[0])) {
            $parentId = $parentIds[0];
            return $parentId;
        }
        
        return $parentId;
    }
    
    /**
     * Based on product ID returns parent grouped product ID or null for no parent.
     *
     * @param int $productId
     * @return null|int
     */
    public function getParentGroupedProductId($productId)
    {
        $parentId = null;
        
        // Grouped
        $parentIds = $this->groupedProductModel
            ->getParentIdsByChild($productId);
        
        if (!empty($parentIds) && isset($parentIds[0])) {
            $parentId = $parentIds[0];
            return $parentId;
        }
        
        return $parentId;
    }
    
    /**
     * Returns customer object loaded by customer ID.
     *
     * @param int $id
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomerById($id)
    {
        return $this->customerFactory->create()->load($id);
    }
    
    /**
     * Returns address object loaded by address ID.
     *
     * @param int $id
     * @return \Magento\Customer\Model\Address
     */
    public function getCustomerAddressById($id)
    {
        return $this->addressFactory->create()->load($id);
    }
    
    /**
     * Returns region object loaded by region ID.
     *
     * @param int $id
     * @return \Magento\Directory\Model\Region
     */
    public function getRegionById($id)
    {
        return $this->regionFactory->create()->load($id);
    }
    
    /**
     * Returns region 2 letter code or name based on provided region ID.
     *
     * @param int $id
     * @return string
     */
    public function getRegionCodeOrNameById($id)
    {
        if (!$id) {
            return '';
        }
        
        $region = $this->getRegionById($id);
        $code   = $region->getCode();
        $name   = $region->getDefaultName();
        
        // FB wants only 2 letter codes otherwise name
        if ($this->stringLength($code) == 2) {
            return $code;
        } else {
            return $name;
        }
    }
    
    /**
     * Converts datetime string to date string.
     *
     * @param string $datetimeString
     * @return string
     */
    public function datetimeToDate($datetimeString)
    {
        $date = '';
        
        if ($datetimeString) {
            $date = date('Y-m-d', strtotime($datetimeString));
        }
        
        return $date;
    }
    
    /**
     * Returns string length.
     *
     * @param string $str
     * @return int
     */
    public function stringLength($str)
    {
        if (function_exists('mb_strlen')) {
            $length = mb_strlen($str, 'UTF-8');
        } else {
            $length = strlen($str);
        }
        
        return (int) $length;
    }
    
    /**
     * Converts string to lowercase.
     *
     * @param string $s
     * @return string
     */
    public function convertToLowercase($s)
    {
        if (function_exists('mb_strtolower')) {
            $str = mb_strtolower($s, 'UTF-8');
        } else {
            $str = strtolower($s);
        }
        
        return $str;
    }

    /**
     * Strips all non numeric characters.
     *
     * @param string $str
     * @return string
     */
    public function stripNonNumeric($str)
    {
        return preg_replace('/[^\p{N}]+/', '', $str);
    }
    
    /**
     * Strips all spaces and converts to lowercase.
     *
     * @param string $str
     * @return string
     */
    public function formatData($str)
    {
        return $this->filter(
            $this->convertToLowercase(
                preg_replace('/\s+/', '', $str)
            )
        );
    }
}
