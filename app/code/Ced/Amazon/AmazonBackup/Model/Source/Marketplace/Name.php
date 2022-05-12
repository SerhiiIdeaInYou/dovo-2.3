<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement(EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_Amazon
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Amazon\Model\Source\Marketplace;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Class Name
 * @package Ced\Amazon\Model\Source\Marketplace
 */
class Name extends AbstractSource
{
    /** @var \Amazon\Sdk\Marketplace  */
    public $marketplace;

    public function __construct(\Amazon\Sdk\Marketplace $marketplace)
    {
        $this->marketplace = $marketplace;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        $marketplace = $this->marketplace->getCollection();
        foreach ($marketplace as &$item) {
            $item['label'] = isset($item['name']) ? $item['name'] : $item['code'];
        }

        return $marketplace;
    }
}
