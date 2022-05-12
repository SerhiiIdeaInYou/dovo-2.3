<?php
/**
 * Page header block
 */

namespace Marketingseals\WordpressIntegration\Block;

use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Store\StoreManager;

//use Marketingseals\Base\Helper\Data as HelperData;
use Marketingseals\WordpressIntegration\Helper\Data as WpHelper;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\HTTP\Client\Curl;
use Psr\Log\LoggerInterface;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;


class WordpressIntegrator extends \Magento\Framework\View\Element\Template
{
    /**
     * Theme helper
     *
     * @var HelperData
     */
    protected $theme;

    protected $_storeManager;
    protected $_wpHelper;
    protected $_curl;
    protected $_logger;


    /**
     * @param Context $context
     * @param HelperData $helperData
     * @param WpHelper $wpHelper ;
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        //HelperData $helperData,
        WpHelper $wpHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        Curl $curl,
        array $data = []
    )
    {
        //$this->theme = $helperData;
        $this->_wpHelper = $wpHelper;
        $this->_curl = $curl;
        $this->_logger = $logger;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
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

    public function getStoreManager()
    {
        return $this->_storeManager;
    }

    protected function getApiBaseUrl()
    {
        $urlParams = '/wp-json/wp-api-menus/v2';
        return $this->_wpHelper->getCfg('base_url', 'base', $this->_storeManager->getStore()->getId()) . $urlParams;
    }

    protected function getApiUrlPrimaryMenu()
    {
        return $this->getApiBaseUrl() . '/menu-locations/';
    }

    protected function getApiUrlFooterMenu() {
        return $this->getApiBaseUrl() . '/menus/';
    }

    protected function getFooterMenuIds() {
        $ids = array();

        $count = $this->getFooterWidgetCount();
        for($i = 1; $i <= $count; $i++) {
            $ids[] = $this->_wpHelper->getCfg('footer_widget_'.$i.'_id', 'footer_menus', $this->_storeManager->getStore()->getId());
        }
        return $ids;

    }

    public function getFooterWidgetCount() {
        return $this->_wpHelper->getCfg('footer_widget_count', 'footer_menus', $this->_storeManager->getStore()->getId());
    }

    public function getMainMenu()
    {
        $storeId = $this->getStoreManager()->getStore()->getId();
        $menu = array();
        try {
            $this->_curl->get($this->getApiUrlPrimaryMenu() . $this->_wpHelper->getCfg('menu_id', 'main_menu', $storeId));
            //response will contain the output in form of JSON string
            $menu = json_decode($this->_curl->getBody());
            //$menu = json_decode(file_get_contents($this->getApiUrl() . $id));

        } catch (\Throwable $exception) {
            $this->_logger->critical($exception);
        }
        return $menu;
    }

    public function getFooterMenus()
    {
        $menus = array();
        if(!empty($widget = $this->getFooterMenuIds())) {
            foreach($widget as $item) {
                try {
                    $this->_curl->get($this->getApiUrlFooterMenu() . $item);
                    //response will contain the output in form of JSON string
                    $menus[] = json_decode($this->_curl->getBody());
                } catch (\Throwable $exception) {
                    $this->_logger->critical($exception);
                }
            }
            return $menus;
        }
        return null;
    }

    public function getMailChimpTitle() {
        return $this->_wpHelper->getCfg('mailchimp_title', 'footy_top', $this->_storeManager->getStore()->getId());
    }
    public function getMailChimpText() {
        return $this->_wpHelper->getCfg('mailchimp_text', 'footy_top', $this->_storeManager->getStore()->getId());
    }
    public function getMailChimpSnippet() {
        return $this->_wpHelper->getCfg('mailchimp_snippet', 'footy_top', $this->_storeManager->getStore()->getId());
    }
    public function getMailChimpHint() {
        return $this->_wpHelper->getCfg('mailchimp_hint', 'footy_top', $this->_storeManager->getStore()->getId());
    }

    public function getCopyrightText() {
        return $this->_wpHelper->getCfg('footer_copyright', 'footer_menus', $this->_storeManager->getStore()->getId());
    }

    public function getLegalMenu() {
        $id = $this->_wpHelper->getCfg('footer_widget_legal', 'footer_menus', $this->_storeManager->getStore()->getId());
        $menus = array();
        try {
            $this->_curl->get($this->getApiUrlFooterMenu() . $id);
            //response will contain the output in form of JSON string
            $menus[] = json_decode($this->_curl->getBody());
        } catch (\Throwable $exception) {
            $this->_logger->critical($exception);
        }
        return $menus;
    }
}
