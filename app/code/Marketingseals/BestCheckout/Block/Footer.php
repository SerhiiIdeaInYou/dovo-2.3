<?php
/**
 * Page footer block
 */

namespace Marketingseals\BestCheckout\Block;

use Infortis\Base\Helper\Data as HelperData;
use Magento\Framework\View\Element\Template\Context;

class Footer extends \Magento\Framework\View\Element\Template
{
    /**
     * Theme helper
     *
     * @var HelperData
     */
    protected $theme;
    protected $_storeManager;

    /**
     * Header block helper
     *
     * @var ...
     */
    // protected $helperHeader;

    /**
     * @param Context $context
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->theme = $helperData;
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

    public function getStoreId() {
        $storeID = $this->_storeManager->getStore()->getId();
        if(empty($storeID)) {
            $storeID = '1';
        }
        return $storeID;
    }

}
