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

namespace Ced\Amazon\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * @deprecated
 * Class ServiceUrl
 * @package Ced\Amazon\Model\Source
 */
class ServiceUrl extends AbstractSource
{
    /**
     * @return array
     */
    public function getAllOptions()
    {
        $serviceUrls = [];
        foreach (\Amazon\Sdk\Marketplace::URLS as $serviceUrl) {
            $serviceUrls[] = [
                'value' => $serviceUrl,
                'label' => $serviceUrl,
            ];
        }
        return $serviceUrls;
    }
}