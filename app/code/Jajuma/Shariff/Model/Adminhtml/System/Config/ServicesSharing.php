<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Jajuma\Shariff\Model\Adminhtml\System\Config;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Math\Random;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class CountryCreditCard
 */
class ServicesSharing extends Value
{
    /**
     * @var \Magento\Framework\Math\Random
     */
    private $mathRandom;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * ServicesSharing constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param Random $mathRandom
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param array $data
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        Random $mathRandom,
        AbstractResource $resource,
        AbstractDb $resourceCollection,
        SerializerInterface $serializer,
        array $data = []
    ) {
        $this->mathRandom = $mathRandom;
        $this->serializer = $serializer;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Prepare data before save
     *
     * @return $this
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $services = $this->toOptionArraySharingServices();
        $result = [];
        foreach ($value as $data) {
            if (empty($data['service_id'])) {
                continue;
            }
            $serviceId = $data['service_id'];
            $result[$serviceId] = array(
                'sort_number' => $data['sort_number'],
                'active' => $data['active'],
                'value' => $serviceId,
                'label' => $services[$serviceId]
            );
        }
        $this->setValue($this->serializer->serialize($result));
        return $this;
    }

    /**
     * Process data after load
     *
     * @return $this
     */
    public function afterLoad()
    {
        if ($this->getValue()) {
            $value = $this->serializer->unserialize($this->getValue());
            $this->setValue($this->encodeArrayFieldValue($value));
        }
        return $this;
    }

    /**
     * Encode value to be used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function encodeArrayFieldValue(array $value)
    {
        $result = [];
        $services = $this->toOptionArraySharingServices();
        if (!$value) {
            foreach ($services as $serviceLabel => $serviceValue) {
                $id = $this->mathRandom->getUniqueHash('_');
                $result[$id] = [
                    'service_name' => __($serviceValue),
                    'service_id' => $serviceLabel,
                    'sort_number' => 1,
                    'active' => 1
                ];
            }
        } else {
            foreach ($services as $serviceLabel => $serviceValue) {
                $id = $this->mathRandom->getUniqueHash('_');
                if (isset($value[$serviceLabel])) {
                    $result[$id] = [
                        'service_name' => __($serviceValue),
                        'service_id' => $serviceLabel,
                        'sort_number' => $value[$serviceLabel]['sort_number'],
                        'active' => $value[$serviceLabel]['active']
                    ];
                } else {
                    $result[$id] = [
                        'service_name' => __($serviceValue),
                        'service_id' => $serviceLabel,
                        'sort_number' => 1,
                        'active' => 1
                    ];
                }
            }

        }
        return $result;
    }

    /**
     * @return array
     */
    public function toOptionArraySharingServices()
    {
        return [
            'twitter' => 'Twitter',
            'facebook' => 'Facebook',
            'googleplus' => 'Google Plus',
            'linkedin' => 'Linkedin',
            'pinterest' => 'Pinterest',
            'xing' => 'Xing',
            'whatsapp' => 'Whatsapp',
            'mail' => 'Mail',
            'info' => 'Info',
            'addthis' => 'Add This',
            'tumblr' => 'Tumblr',
            'flattr' => 'Flattr',
            'diaspora' => 'Diaspora',
            'reddit' => 'Reddit',
            'stumbleupon' => 'StumbleUpon',
            'threema' => 'Threema',
            'weibo' => 'Weibo',
            'tencent-weibo' => 'Tencent Weibo',
            'qzone' => 'Qzone',
            'print' => 'Print',
            'telegram' => 'Telegram',
            'vk' => 'Vk',
            'flipboard' => 'Flipboard',
        ];
    }
}
