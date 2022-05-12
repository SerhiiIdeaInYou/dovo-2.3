<?php
/**
 * Page header block
 */

namespace Dovo\HeaderImage\Block;

use Dovo\HeaderImage\Helper\Data as HelperData;
use Infortis\Base\Helper\Template\Theme\Html\Header as HelperTemplateHtmlHeader;
use Infortis\Infortis\Helper\Connector\Infortis\UltraMegamenu as HelperConnectorUltraMegamenu;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Page\Title as Title;

class HeaderImage extends \Magento\Framework\View\Element\Template
{
    /**
     * Theme helper
     *
     * @var HelperData
     */
    protected $theme;
    protected $_date;

    /**
     * Header block helper
     *
     * @var HelperTemplateHtmlHeader
     */
    protected $helperHeader;

    /**
     * Connector helper for menu module
     *
     * @var HelperConnectorUltraMegamenu
     */
    protected $connectorMenu;
    protected $pageTitle;

    /**
     * @param Context $context
     * @param HelperData $helperData
     * @param HelperTemplateHtmlHeader $helperTemplateHtmlHeader
     * @param HelperConnectorUltraMegamenu $helperConnectorUltraMegamenu
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        HelperTemplateHtmlHeader $helperTemplateHtmlHeader,
        HelperConnectorUltraMegamenu $helperConnectorUltraMegamenu,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        Title $pageTitle,
        array $data = []
    ) {
        $this->_date = $date;
        $this->theme = $helperData;
        $this->pageTitle = $pageTitle;
        $this->helperHeader = $helperTemplateHtmlHeader;
        $this->connectorMenu = $helperConnectorUltraMegamenu;
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

    /**
     * Get Page Title
     *
     * @return Title
     */
    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    /**
     * Get helper
     *
     * @return HelperTemplateHtmlHeader
     */
    public function getHelperHeader()
    {
        return $this->helperHeader;
    }

    /**
     * Get helper
     *
     * @return HelperConnectorUltraMegamenu
     */
    public function getHelperConnectorMenu()
    {
        return $this->connectorMenu;
    }

    /**
     * Retrieve welcome text.
     * Method is a copy from Magento\Theme\Block\Html\Header class.
     *
     * @return string
     */
    public function getWelcome()
    {
        if (empty($this->_data['welcome'])) {
            $this->_data['welcome'] = $this->_scopeConfig->getValue(
                'design/header/welcome',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return __($this->_data['welcome']);
    }
    /**
     * Get array of static block identifiers from module config
     *
     * @return array
     */
    protected function getConfigCategories()
    {
        $blockIds = $this->theme->getCfg('general/blocks');
        if (empty($blockIds))
        {
            return [];
        }

        return $this->getStaticBlockIds($blockIds);
    }

    public function isNotificationEnabled()
    {
        $isEnabled = $this->theme->getNotificationCfg('notification_active', $this->_storeManager->getStore()->getId());
        $startDate = $this->theme->getNotificationCfg('notification_start', $this->_storeManager->getStore()->getId());
        $endDate = $this->theme->getNotificationCfg('notification_end', $this->_storeManager->getStore()->getId());

        $now = $this->_date->date()->format('Y-m-d');

        if ($isEnabled) {

            if (strtotime($now) < strtotime($startDate)) {
                return false;
            }
            if (strtotime($now) > strtotime($endDate)) {
                return false;
            }
            return true;
        }
        return false;
    }

    public function getNotificationText()
    {
        return $this->theme->getNotificationCfg('text_area', $this->_storeManager->getStore()->getId());
    }

}
