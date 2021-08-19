<?php

namespace Moogento\Sitemap\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;

class Index extends Action
{
    /**
     * @var PageFactory
     */
    private $pageFactory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->pageFactory  = $pageFactory;
        $this->storeManager = $storeManager;
    }


    /**
     * @ingeritdoc
     */
    public function execute()
    {
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()
            ->set(sprintf('Sitemap for %s', $this->storeManager->getWebsite()->getName()));

        return $page;
    }
}
