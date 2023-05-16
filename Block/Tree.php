<?php

namespace Moogento\Sitemap\Block;

use Magento\Catalog\Api\CategoryManagementInterface;
use Magento\Catalog\Helper\Category;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Cms\Model\Page;
use Magento\Framework\View\Element\Template\Context;
use Moogento\Sitemap\Model\CategoryService;
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
     * @var CategoryService
     */
    private $categoryService;

    private $isHideEmptyCat;

    /**
     * Tree constructor.
     * @param CategoryManagementInterface $categoryManagement
     * @param LoggerInterface $logger
     * @param CategoryRepository $categoryRepository
     * @param Config $config
     * @param Category $category
     * @param CmsPageCollection $cmsPageCollection
     * @param CategoryService $categoryService
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
        CategoryService $categoryService,
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
        $this->categoryService = $categoryService;
        $this->init();
    }

    /**
     * @return void
     */
    protected function init()
    {
        $this->isHideEmptyCat = $this->config->isActiveHideEmptyCat();
    }

    /**
     * @return string
     */
    public function getCategoryTree(): string
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
     * @param array $catTree
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function generate(array $catTree): string
    {
        if (!$this->config->isActiveIncludeCategories()) {
            return '';
        }
        $showProducts = $this->config->canShowProducts();
        $tree = '<ul>';
        foreach ($catTree as $item) {
            if ((int)$item['is_active'] == 1) {
                $tree .= '<li class="cat_item';
                if ((int)$item['parent_id'] == 2) {
                    $tree .= ' cat_parent';
                }
                $tree .= '">';

                if ((int)$item['parent_id'] == 2) {
                    $tree .= sprintf(
                        '<h2><a class="cat-name" href="%s">%s</a></h2>',
                        $this->getCategoryUrl((int)$item['entity_id']),
                        $item['name']
                    );
                } elseif (count($item['children_data']) > 0) {
                    $productsCount = (int)$this->categoryService->getProductCountByCatId((int)$item['entity_id']);
                    if ($productsCount == 0) {
                        $tree .= sprintf(
                            '<a class="cat-name" href="%s">%s</a>',
                            $this->getCategoryUrl((int)$item['entity_id']),
                            $item['name']
                        );
                    } else {
                        $tree .= $this->prepareString($this->getCategoryUrl((int)$item['entity_id']), $item['name'], $productsCount);
                    }
                } else {
                    $productsCount = (int)$this->categoryService->getProductCountByCatId((int)$item['entity_id']);
                    if ($this->isHideEmptyCat) {
                        if ($productsCount > 0) {
                            $tree .= $this->prepareString($this->getCategoryUrl((int)$item['entity_id']), $item['name'], $productsCount);
                        }
                    } else {
                        $tree .= $this->prepareString($this->getCategoryUrl((int)$item['entity_id']), $item['name'], $productsCount);
                    }
                }
                if ($showProducts) {
                    $tree .= $this->getProductListByCatId((int)$item['entity_id']);
                }
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
     * @param string $url
     * @param string $name
     * @param int $count
     * @return string
     */
    protected function prepareString(string $url, string $name, int $count): string
    {
        if(!$count || $count == 0 || $count == 1) {
            return sprintf(
                '<a class="cat-name" href="%s">%s</a>',
                $url,
                $name
            );    
        }
        
        return sprintf(
            '<a class="cat-name" href="%s">%s</a><span class="prod_count">(%s)</span>',
            $url,
            $name,
            $count
        );
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
     * @param int $categoryId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryUrl(int $categoryId): string
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
        $title = 'Sitemap';
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
        $this->pageConfig->setKeywords('sitemap categories subcategories');
        $this->pageConfig->setDescription(sprintf('See & Search %s with our interactive sitemap', $this->_storeManager->getWebsite()->getName()));
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
                $linkData = explode(',', $link, 2);
                if (count($linkData) > 1) {
                    $result[$linkData[0]] = $linkData[1];
                } else {
                    $result[] = $linkData[0];
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
            foreach ($links as $link => $label) {
                $sanitizedLabel = $this->sanitizeLabel($label);

                if (preg_match('/<h2>(.+)<\/h2>/', $sanitizedLabel)) {
                    $result .= "</ul><ul><li class=\"cat_item cat_parent\">$sanitizedLabel</li>";
                } else {
                    $result .= sprintf('<li class="cat_item"><a class="cat-name" href="%s">%s</a></li>', $link, $sanitizedLabel);
                }
            }
            $result .= '</ul>';

            $result = str_replace('<ul></ul>', '', $result);
        }

        return $result;
    }

    /**
     * Sanitize label by allowing specific HTML tags
     *
     * @param string $label
     * @return string
     */
    private function sanitizeLabel(string $label): string
    {
        $allowedTags = '<b><em><ul><span><br><h2>';
        $sanitizedLabel = strip_tags($label, $allowedTags);

        return $sanitizedLabel;
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
            $result .= sprintf('<li class="cat_item"><a class="cat-name" href="%s">%s</a></li>', $page->getIdentifier(), $page->getTitle());
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

        if ($incLinks) {
            $result .= $this->generateCustomLinks();
        }

        if ($incCms) {
            $result .= '<ul>';
            $result .= sprintf('<li class="cat_item cat_parent"><h2>%s</h2>', __('Additional'));
            $result .= $this->generateCmsLinks();
            $result .= '</li></ul>';
        }
        return $result;
    }

    public function getProductListByCatId($categoryId)
    {
        $list = '';
        $collection = $this->categoryService->getProductCollectionByCatId($categoryId);
        if ($collection->count()) {
            $list .= '<ul class="products-list">';
            foreach ($collection as $product) {
                $list .= sprintf('<li class="product"><a href="%s">%s</a></li>', $product->getProductUrl(), $product->getName());
            }
            $list .= '</ul>';
        }
        return $list;
    }
}
