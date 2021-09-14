<?php

namespace Moogento\Sitemap\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

/**
 * @method Hint setElement
 */
class Hint extends Template implements RendererInterface
{
    /**
     * @var string
     */
    protected $_template = 'Moogento_Sitemap::system/config/hint.phtml';

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }

    /**
     * Render form element as HTML
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     * @return string
     */
    public function getLogo(): string
    {
        return $this->_scopeConfig->getValue('moogento/logo/url')
            . 'lic/moogento_logo_sitemap.png';
    }

    /**
     * @return string
     */
    public function getInfo(): string
    {
        return $this->_scopeConfig->getValue('moogento/logo/url')
            . 'lic/moogento_sitemap.js';
    }
}
