<?php
/**
 * Page header block
 */

namespace Dovo\Base\Block\Header;

use Infortis\Base\Helper\Data as HelperData;
use Infortis\Base\Helper\Template\Theme\Html\Header as HelperTemplateHtmlHeader;
use Infortis\Infortis\Helper\Connector\Infortis\UltraMegamenu as HelperConnectorUltraMegamenu;
use Magento\Framework\View\Element\Template\Context;

class MainNav extends \Magento\Framework\View\Element\Template
{
    /**
     * Theme helper
     *
     * @var HelperData
     */
    protected $theme;

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
    protected $_storeManager;


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
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->theme = $helperData;
        $this->_storeManager = $storeManager;
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

    public function getStoreManager() {
        return $this->_storeManager;
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

}
