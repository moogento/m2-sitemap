<?php

namespace Moogento\Sitemap\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Moogento\Sitemap\Helper\VersionHelper;

/**
 * @method Hint setElement
 */
class Hint extends Template implements RendererInterface
{
    /**
     * @var VersionHelper
     */
    protected $versionHelper;

    /**
     * @var string
     */
    protected $_template = 'Moogento_Sitemap::system/config/hint.phtml';

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context, 
        VersionHelper $versionHelper,
        array $data = [])
    {
        $this->versionHelper = $versionHelper;
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

    public function getLogo()
    {
        return 'https://l2.moogento.com/media/img/logo/sitemap.png';
    }

    public function getVersion()
    {
        return $this->versionHelper->getModuleVersion();
    }
}
