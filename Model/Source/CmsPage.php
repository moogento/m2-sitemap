<?php

namespace Moogento\Sitemap\Model\Source;

use Magento\Cms\Model\Page;
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
    public function toOptionArray(): array
    {
        $options = [];
        $collection = $this->cmsPageCollectionFactory->create();
        foreach ($collection as $item) {
            /** @var Page $item */
            if ($this->isValid($item)) {
                $options[] = ['value' => $item->getIdentifier(), 'label' => $item->getTitle()];
            }
        }

        return $options;
    }

    /**
     * @param Page $item
     * @return bool
     */
    protected function isValid(Page $item): bool
    {
        $pos = strpos($item->getIdentifier(), '404');
        if ($pos !== false) {
            return false;
        }
        $pos = strpos($item->getTitle(), '404');
        if ($pos !== false) {
            return false;
        }
        $pos = strpos($item->getIdentifier(), 'cookie');
        if ($pos !== false) {
            return false;
        }
        $pos = strpos($item->getTitle(), 'cookie');
        if ($pos !== false) {
            return false;
        }
        return true;
    }
}
