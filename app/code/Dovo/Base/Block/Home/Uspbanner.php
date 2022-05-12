<?php
/**
 * Page header block
 */

namespace Dovo\Base\Block\Home;

use Dovo\HeaderImage\Helper\Data as HelperData;
use Magento\Framework\View\Element\Template\Context;

class Uspbanner extends \Magento\Framework\View\Element\Template
{
    /**
     * Theme helper
     *
     * @var HelperData
     */
    protected $helperData;
    /**
     * @param Context $context
     * @param HelperData $helperData
     * @param HelperTemplateHtmlHeader $helperTemplateHtmlHeader
     * @param HelperConnectorUltraMegamenu $helperConnectorUltraMegamenu
     * @param array $data
     */
    public function __construct(
        HelperData $helperData,
        Context $context,
        array $data = []
    ) {
        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * Get helper
     *
     * @return HelperData
     */
    public function getHelperData()
    {
        return $this->helperData;
    }

}
