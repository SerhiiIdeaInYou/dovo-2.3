<?php
/**
 * Page header block
 */

namespace Dovo\SocialMenu\Block;


use Magento\Framework\View\Element\Template\Context;

class Social extends \Magento\Framework\View\Element\Template
{
    /**
     * @param Context $context
     * @param HelperData $helperData
     * @param HelperTemplateHtmlHeader $helperTemplateHtmlHeader
     * @param HelperConnectorUltraMegamenu $helperConnectorUltraMegamenu
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
    

    public function getFBLink() {

    }
    public function getInstaLink() {

    }

}
