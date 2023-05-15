<?php

namespace Moogento\Sitemap\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    const XML_PATH_SITEMAP_ENABLE = 'moositemap/sitemap/enable';
    const XML_PATH_SITEMAP_FOOTER_LINK = 'moositemap/sitemap/footer';
    const XML_PATH_SITEMAP_INCLUDE_CATEGORIES_YN = 'moositemap/sitemap/include_categories_yn';
    const XML_PATH_SITEMAP_INCLUDE_CMS_YN = 'moositemap/sitemap/include_cms_yn';
    const XML_PATH_SITEMAP_INCLUDE_CMS_LISTING = 'moositemap/sitemap/include_cms_listing';
    const XML_PATH_SITEMAP_INCLUDE_LINKS_YN = 'moositemap/sitemap/include_links_yn';
    const XML_PATH_SITEMAP_INCLUDE_LINKS_LISTING = 'moositemap/sitemap/include_links_listing';
    const XML_PATH_SITEMAP_HIDE_EMPTY_CATEGORIES_YN = 'moositemap/sitemap/hide_empty_categories_yn';
    const XML_PATH_SITEMAP_SHOW_PRODUCTS_YN = 'moositemap/sitemap/show_products';

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
    public function getSitemapEnable($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SITEMAP_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return mixed
     */
    public function getSitemapFooterLinkEnable($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SITEMAP_FOOTER_LINK,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return mixed
     */
    public function isActiveIncludeCategories($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SITEMAP_INCLUDE_CATEGORIES_YN,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return mixed
     */
    public function isActiveIncludeCms($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SITEMAP_INCLUDE_CMS_YN,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return mixed
     */
    public function getIncludeCms($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SITEMAP_INCLUDE_CMS_LISTING,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return mixed
     */
    public function isActiveIncludeLinks($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SITEMAP_INCLUDE_LINKS_YN,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return mixed
     */
    public function getIncludeLinks($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SITEMAP_INCLUDE_LINKS_LISTING,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return mixed
     */
    public function getManageStock()
    {
        return $this->scopeConfig->getValue(
            \Magento\CatalogInventory\Model\Configuration::XML_PATH_MANAGE_STOCK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function isActiveHideEmptyCat($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SITEMAP_HIDE_EMPTY_CATEGORIES_YN,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return mixed
     */
    public function canShowProducts($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SITEMAP_SHOW_PRODUCTS_YN,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
