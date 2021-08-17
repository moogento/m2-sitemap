<?php

namespace Moogento\Sitemap\Model;

use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Model\ResourceModel\Stock\Status as StockStatusResource;

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
     * @param CollectionFactory $productCollectionFactory
     * @param StockStatusResource $stockStatusResource
     * @param Visibility $productVisibility
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        StockStatusResource $stockStatusResource,
        Visibility $productVisibility
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->stockStatusResource = $stockStatusResource;
        $this->productVisibility = $productVisibility;
    }

    /**
     * @param int $categoryId
     * @return Collection
     */
    protected function getProdCollectionByCatId(int $categoryId)
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['entity_id']);
        $collection->addCategoriesFilter(['in' => $categoryId]);
        $collection->setVisibility($this->productVisibility->getVisibleInSiteIds());
        return $collection;
    }

    /**
     * @param int $categoryId
     * @return int|void
     */
    public function getProductCountByCatId(int $categoryId)
    {
        $collection = $this->getProdCollectionByCatId($categoryId);
        $this->stockStatusResource->addIsInStockFilterToCollection($collection);
        return count($collection->getAllIds());
    }
}
