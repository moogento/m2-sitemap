<?php

namespace Moogento\Sitemap\Model\Source;

use Magento\Cms\Model\ResourceModel\Page\Collection;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;

class CmsPage
{
    /**
     * @var CollectionFactory
     */
    private $cmsPageCollectionFactory;

    /**
     * @param CollectionFactory $cmsPageCollectionFactory
     */
    public function __construct(
        CollectionFactory $cmsPageCollectionFactory
    ) {
        $this->cmsPageCollectionFactory = $cmsPageCollectionFactory;
    }

    /**
     * Get list cms pages
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        /** @var Collection $collection */
        $collection = $this->cmsPageCollectionFactory->create();
        foreach ($collection as $item) {
            $options[] = ['value' => $item->getIdentifier(), 'label' => $item->getTitle()];
        }

        return $options;
    }
}
