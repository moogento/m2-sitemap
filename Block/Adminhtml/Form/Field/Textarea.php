<?php

namespace Moogento\Sitemap\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Textarea extends Field
{
    /**
     * Add a custom class to the textarea element.
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->addClass('packed_textarea');
        return parent::_getElementHtml($element);
    }
}
