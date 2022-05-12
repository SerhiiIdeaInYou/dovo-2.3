<?php

namespace Jajuma\Shariff\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

class ServicesListing extends AbstractFieldArray
{
    protected $_template = 'Jajuma_Shariff::system/config/form/field/services.phtml';

    protected $servicesRenderer = null;

    protected $activeRenderer = null;

    protected function getServicesRenderer()
    {
        if (!$this->servicesRenderer) {
            $this->servicesRenderer = $this->getLayout()->createBlock(
                Services::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->servicesRenderer;
    }

    protected function getActiveRenderer()
    {
        if (!$this->activeRenderer) {
            $this->activeRenderer = $this->getLayout()->createBlock(
                Active::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->activeRenderer;
    }

    /**
     * Prepare to render
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'service_name',
            [
                'label' => __('Services'),
                'style' => 'pointer-events: none'
            ]
        );
        $this->addColumn(
            'service_id',
            [
                'label' => __(),
                'style' => 'display:none'
            ]
        );
        $this->addColumn(
            'sort_number',
            [
                'label' => __('Sort'),
            ]
        );
        $this->addColumn(
            'active',
            [
                'label' => __('Active'),
                'renderer' => $this->getActiveRenderer(),
            ]
        );
        $this->_addAfter = false;
    }


    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        $service = $row->getServiceId();
        $options = [];
        if ($service) {
            $active = $row->getActive();
            $options['option_' . $this->getActiveRenderer()->calcOptionHash($active)]
                = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }
}
