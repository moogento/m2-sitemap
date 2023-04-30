<?php

namespace Moogento\Sitemap\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Json\DecoderInterface;

class VersionHelper extends AbstractHelper
{
    /**
     * @var ComponentRegistrarInterface
     */
    protected $componentRegistrar;

    /**
     * @var File
     */
    protected $fileDriver;

    /**
     * @var DecoderInterface
     */
    protected $jsonDecoder;

    /**
     * VersionHelper constructor.
     *
     * @param Context $context
     * @param ComponentRegistrarInterface $componentRegistrar
     * @param File $fileDriver
     * @param DecoderInterface $jsonDecoder
     */
    public function __construct(
        Context $context,
        ComponentRegistrarInterface $componentRegistrar,
        File $fileDriver,
        DecoderInterface $jsonDecoder
    ) {
        $this->componentRegistrar = $componentRegistrar;
        $this->fileDriver = $fileDriver;
        $this->jsonDecoder = $jsonDecoder;
        parent::__construct($context);
    }

    /**
     * Get current module version number from composer.json.
     *
     * @return string|null
     */
    public function getModuleVersion()
    {
        try {
            $modulePath = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'Moogento_Sitemap');
            $composerFilePath = $modulePath . '/composer.json';

            if ($this->fileDriver->isExists($composerFilePath)) {
                $composerJsonContent = $this->fileDriver->fileGetContents($composerFilePath);
                $composerData = $this->jsonDecoder->decode($composerJsonContent);
                if (isset($composerData['version'])) {
                    return $composerData['version'];
                }
            }
        } catch (\Exception $e) {
        }

        return null;
    }
}
