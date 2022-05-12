<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Jajuma\Shariff\Helper;

use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const JS_CONFIG_PATH = "shariff/setting/js_config";
    const CSS_CONFIG_PATH = "shariff/setting/css_config";

    const POSITION_HOMEPAGE_PATH = "shariff/position/homepage";
    const POSITION_CATEGORY_PATH = "shariff/position/category_page";
    const POSITION_CMS_PATH = "shariff/position/cms_page";
    const POSITION_PRODUCT_PATH = "shariff/position/product_page";

    const BUTTON_STYLE_PATH = "shariff/icon/button_style";
    const THEME_PATH = "shariff/icon/theme";
    const LIMIT_PATH = "shariff/icon/limit";
    const SERVICES_PATH = "shariff/icon/services";
    const SERVICES_LIST_PATH = "shariff/icon/services_list";
    const LANG_PATH = "shariff/icon/lang";
    const TWITTER_VIA_PATH = "shariff/icon/twitter_via";
    const FLATTR_USER_PATH = "shariff/icon/flattr_user";
    const FLATTR_CATEGORY_PATH = "shariff/icon/flattr_category";
    const MAIL_SUBJECT_PATH = "shariff/icon/mail_subject";
    const MAIL_BODY_PATH = "shariff/icon/mail_body";
    const INFO_URL_PATH = "shariff/icon/info_url";
    const INFO_DISPLAY_PATH = "shariff/icon/info_display";
    const REFERRER_TRACK_PATH = "shariff/icon/referrer_track";

    public function getCssConfiguration($store = null)
    {
        return $this->scopeConfig->getValue(self::CSS_CONFIG_PATH,
        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $store
        );
    }

    public function getJsConfiguration($store = null)
    {
        return $this->scopeConfig->getValue(self::JS_CONFIG_PATH,
        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $store
        );
    }

    public function getConfig($configPath, $store = null)
    {
        return $this->scopeConfig->getValue($configPath,
        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $store
        );
    }
}
