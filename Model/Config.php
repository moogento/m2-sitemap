<?php

namespace Moogento\Sitemap\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    const XML_PATH_SITEMAP_ENABLE = 'moositemap/sitemap/enable';
    const XML_PATH_SITEMAP_FOOTER_LINK = 'moositemap/sitemap/footer';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return mixed
     */
    public function getSitemapEnable()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SITEMAP_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getSitemapFooterLinkEnable()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SITEMAP_FOOTER_LINK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
