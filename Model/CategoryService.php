<?php

namespace Moogento\Sitemap\Model;

use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Model\ResourceModel\Stock\Status as StockStatusResource;
use Magento\Store\Model\StoreManagerInterface;

class CategoryService
{
    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;
    /**
     * @var StockStatusResource
     */
    private $stockStatusResource;
    /**
     * @var Visibility
     */
    private $productVisibility;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param CollectionFactory $productCollectionFactory
     * @param StockStatusResource $stockStatusResource
     * @param Visibility $productVisibility
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        StockStatusResource $stockStatusResource,
        Visibility $productVisibility,
        StoreManagerInterface $storeManager
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->stockStatusResource = $stockStatusResource;
        $this->productVisibility = $productVisibility;
        $this->storeManager = $storeManager;
    }

    /**
     * @param int $categoryId
     * @return Collection
     */
    public function getProductCollectionByCatId(int $categoryId)
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addCategoriesFilter(['eq' => $categoryId])
            ->addAttributeToSelect(['entity_id', 'name', 'url_key', 'url_path'])
            ->addStoreFilter($this->storeManager->getStore()->getId())
            ->addAttributeToFilter('status', 1)
            ->addUrlRewrite()
            ->setVisibility($this->productVisibility->getVisibleInSiteIds());

        return $collection;
    }

    /**
     * @param int $categoryId
     * @return int|void
     */
    public function getProductCountByCatId(int $categoryId)
    {
        $collection = $this->getProductCollectionByCatId($categoryId);
        return count($collection->getAllIds());
    }
}
