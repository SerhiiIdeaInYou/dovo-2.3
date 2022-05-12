<?php
/**
 * Page header block
 */

namespace Dovo\Base\Block\Sidebar;


use Magento\Framework\View\Element\Template\Context;

class Social extends \Magento\Framework\View\Element\Template
{
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
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    public function getStoreManager() {
        return $this->_storeManager;
    }

    public function getFBLink() {

    }
    public function getInstaLink() {

    }

}
