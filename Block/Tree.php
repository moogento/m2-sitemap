<?php

namespace Moogento\Sitemap\Block;

use Magento\Catalog\Api\CategoryManagementInterface;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\View\Element\Template\Context;
use Moogento\Sitemap\Model\Config;
use Psr\Log\LoggerInterface;

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
     * Tree constructor.
     * @param CategoryManagementInterface $categoryManagement
     * @param LoggerInterface $logger
     * @param CategoryRepository $categoryRepository
     * @param Config $config
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        CategoryManagementInterface $categoryManagement,
        LoggerInterface $logger,
        CategoryRepository $categoryRepository,
        Config $config,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->categoryManagement = $categoryManagement;
        $this->categoryRepository = $categoryRepository;
        $this->logger = $logger;
        $this->config = $config;
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
    public function renderSitemap()
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
        $title = 'Sitemap';
        $this->pageConfig->getTitle()->set($title);
        // add Home breadcrumb
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            )->addCrumb(
                'search',
                ['label' => $title, 'title' => $title]
            );
        }

        return parent::_prepareLayout();
    }
}
