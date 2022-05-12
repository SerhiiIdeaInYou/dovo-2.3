<?php

namespace Marketingseals\WordpressIntegration\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * Section name of module configuration
     */
    const CONFIG_SECTION = 'wp_settings';

    /**
     * Get settings
     *
     * @return string
     */
    public function getCfg($optionString, $group,  $storeCode = NULL)
    {
        return $this->scopeConfig->getValue(self::CONFIG_SECTION . '/' . $group . '/' . $optionString, ScopeInterface::SCOPE_STORE, $storeCode);
    }

//    //
//    //  Get section of options from the configuration
//    //  -----------------------------------------------------------------------
//
//    /**
//     * Get selected section from the configuration
//     *
//     * @return array
//     */
//    public function getCfgSection($section, $storeCode = NULL)
//    {
//        return $this->scopeConfig->getValue($section, ScopeInterface::SCOPE_STORE, $storeCode);
//    }
//
//    /**
//     * Get design section from the configuration
//     *
//     * @return array
//     */
//    public function getCfgSectionDesign($storeCode = NULL)
//    {
//        return $this->getCfgSection(self::CONFIG_SECTION_DESIGN, $storeCode);
//    }

//    //
//    //  Get group of options from the configuration
//    //  -----------------------------------------------------------------------
//
//    /**
//     * Get selected group from main configuration
//     *
//     * @return array
//     */
//    public function getCfgGroup($group, $storeCode = NULL)
//    {
//        return $this->scopeConfig->getValue(self::CONFIG_SECTION_SETTINGS . '/' . $group, ScopeInterface::SCOPE_STORE, $storeCode);
//    }
//
//    //
//    //  Get single option from the configuration
//    //  -----------------------------------------------------------------------
//
//    /**
//     * Get single option from main configuration
//     *
//     * @return string
//     */
//    public function getCfg($optionString, $storeCode = NULL)
//    {
//        return $this->scopeConfig->getValue(self::CONFIG_SECTION_SETTINGS . '/' . $optionString, ScopeInterface::SCOPE_STORE, $storeCode);
//    }
//
//    /**
//     * Get single option from design configuration
//     *
//     * @return string
//     */
//    public function getCfgDesign($optionString, $storeCode = NULL)
//    {
//        return $this->scopeConfig->getValue(self::CONFIG_SECTION_DESIGN . '/' . $optionString, ScopeInterface::SCOPE_STORE, $storeCode);
//    }
//
//    /**
//     * Get single option from layout configuration
//     *
//     * @return string
//     */
//    public function getCfgLayout($optionString, $storeCode = NULL)
//    {
//        return $this->scopeConfig->getValue(self::CONFIG_SECTION_LAYOUT . '/' . $optionString, ScopeInterface::SCOPE_STORE, $storeCode);
//
//    }
}
