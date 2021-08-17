<?php

namespace Moogento\Sitemap\Block;

use Magento\Catalog\Api\CategoryManagementInterface;
use Magento\Catalog\Helper\Category;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Cms\Model\Page;
use Magento\Framework\View\Element\Template\Context;
use Moogento\Sitemap\Model\Config;
use Psr\Log\LoggerInterface;
use Magento\Cms\Model\ResourceModel\Page\Collection as CmsPageCollection;

class Tree extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CategoryManagementInterface
     */
    private $categoryManagement;

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var Config
     */
    private $config;
    /**
     * @var Category
     */
    private $category;
    /**
     * @var CmsPageCollection
     */
    private $cmsPageCollection;

    /**
     * Tree constructor.
     * @param CategoryManagementInterface $categoryManagement
     * @param LoggerInterface $logger
     * @param CategoryRepository $categoryRepository
     * @param Config $config
     * @param Category $category
     * @param CmsPageCollection $cmsPageCollection
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        CategoryManagementInterface $categoryManagement,
        LoggerInterface $logger,
        CategoryRepository $categoryRepository,
        Config $config,
        Category $category,
        CmsPageCollection $cmsPageCollection,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->categoryManagement = $categoryManagement;
        $this->categoryRepository = $categoryRepository;
        $this->logger = $logger;
        $this->config = $config;
        $this->category = $category;
        $this->cmsPageCollection = $cmsPageCollection;
    }

    /**
     * @return string
     */
    public function getCategoryTree()
    {
        if (!$this->isEnableSitemap()) {
            return '';
        }
        $rootCategoryId = 2;
        $res = '';
        try {
            $categoryTree = $this->categoryManagement->getTree($rootCategoryId);
            if (isset($categoryTree['children_data'])) {
                $childrenData = $categoryTree['children_data'];
                if (count($childrenData) > 0) {
                    $res = $this->generate($childrenData);
                    $res .= $this->getAdditionalLinks();
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return $res;
    }

    /**
     * @param $catTree
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function generate($catTree)
    {
        $tree = '<ul>';
        foreach ($catTree as $item) {
            if ((int)$item['is_active'] == 1) {
                $tree .= '<li class="cat_item';
                if ((int)$item['parent_id'] == 2) {
                    $tree .= ' cat_parent';
                }
                $tree .= '">';

                if ((int)$item['parent_id'] == 2) {
                    $tree .= '<h2>';
                }

                $tree .= '<a class="cat-name" href="' . $this->getCategoryUrl((int)$item['entity_id']) . '">' . $item['name'] . '</a>';

                if ((int)$item['parent_id'] == 2) {
                    $tree .= '</h2>';
                }

                $tree .= '<span class="prod_count">(' . (int)$item['product_count'] . ')</span>';

                $childrenData = $item['children_data'];
                if (is_array($childrenData) && count($childrenData) > 0) {
                    $tree .= $this->generate($childrenData);
                }
                $tree .= '</li>';
            }
        }
        $tree .= '</ul>';
        return $tree;
    }

    /**
     * @return mixed
     */
    public function isEnableSitemap()
    {
        return $this->config->getSitemapEnable();
    }

    /**
     * @return string
     */
    public function renderSitemap(): string
    {
        return $this->getCategoryTree();
    }

    /**
     * @param $categoryId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryUrl($categoryId)
    {
        $category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
        return $category->getUrl();
    }

    /**
     * Prepare layout
     * @return $this
     */
    protected function _prepareLayout(): Tree
    {
        $title = 'Site Map';
        $this->pageConfig->getTitle()->set($title);
        // add Home breadcrumb
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            )->addCrumb(
                'search',
                ['label' => $title]
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * @return array
     */
    public function getIncludedPages(): array
    {
        if ($this->config->isActiveIncludeCms()) {
            return explode(',', $this->config->getIncludeCms());
        }

        return [];
    }

    /**
     * @return array
     */
    protected function getAdditionLinks(): array
    {
        $result = [];
        $savedData = $this->config->getIncludeLinks();
        if (!empty($savedData)) {
            $Links = explode("\n", $savedData);
            foreach ($Links as $link) {
                $linkData = explode(',', $link);
                if (count($linkData) > 1) {
                    $result[$linkData[0]] = $linkData[1];
                }
            }
        }
        return $result;
    }

    /**
     * Get category collection
     * @return \Magento\Framework\Data\Tree\Node\Collection
     */
    public function getCategoryCollection()
    {
        return $this->category->getStoreCategories(false, true);
    }

    /**
     * @return string
     */
    public function generateCustomLinks(): string
    {
        $result = '';
        $links = $this->getAdditionLinks();
        if (count($links) > 0) {
            $result .= '<ul>';
            foreach ($links as $key => $value) {
                $result .= sprintf('<li><a class="add_link" href="%s">%s</a></li>', $key, $value);
            }
            $result .= '</ul>';
        }
        return $result;
    }

    /**
     * @return string
     */
    public function generateCmsLinks(): string
    {
        $result = '';
        $pages = $this->getCmsPageCollection();
        $result .= '<ul>';
        foreach ($pages as $page) {
            $result .= sprintf('<li><a class="add_link" href="%s">%s</a></li>', $page->getIdentifier(), $page->getTitle());
        }
        $result .= '</ul>';
        return $result;
    }

    /**
     * @return CmsPageCollection
     */
    protected function getCmsPageCollection(): CmsPageCollection
    {
        return $this->cmsPageCollection->addFieldToFilter('is_active', Page::STATUS_ENABLED)
            ->addFieldToFilter('identifier', ['in' => $this->getIncludedPages()]);
    }

    /**
     * @return string
     */
    public function getAdditionalLinks(): string
    {
        $result = '';
        $incLinks = $this->config->isActiveIncludeLinks();
        $incCms = $this->config->isActiveIncludeCms();
        if ($incLinks || $incCms) {
            $result .= '<ul class="add_link_list_wrapper">';
            $result .= sprintf('<li class="link_list_label"><span>%s</span></li>', __('Additional'));
            if ($incLinks) {
                $result .= $this->generateCustomLinks();
            }
            if ($incCms) {
                $result .= $this->generateCmsLinks();
            }
            $result .= '</ul>';
        }
        return $result;
    }
}
