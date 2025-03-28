<?php

namespace GiftGroup\GeoPage\Model\Sitemap;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Framework\Filesystem\File\WriteInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Escaper;
use Magento\Framework\Filesystem\File\Write as FileWrite;
use Magento\Framework\Filesystem\Directory\Write as DirectoryWrite;

/**
 * Sitemap model.
 */
abstract class AbstractSitemap
{
    protected const OPEN_TAG_KEY = 'start';

    protected const CLOSE_TAG_KEY = 'end';

    protected const TYPE_URL = 'url';

    protected const SITEMAP_PATH = '/sitemaps/';

    /**
     * Last mode date min value
     */
    public const LAST_MOD_MIN_VAL = '0000-01-01 00:00:00';

    /**
     * Sitemap start and end tags
     *
     * @var array
     */
    protected $tags = [];

    /**
     * Number of lines in sitemap
     *
     * @var int
     */
    protected $lineCount = 0;

    /**
     * Current sitemap file size
     *
     * @var int
     */
    protected $fileSize = 0;

    /**
     * @var DirectoryWrite
     */
    protected $directory;

    /**
     * @var FileWrite
     */
    protected $stream;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * Last mode min timestamp value
     *
     * @var int
     */
    protected $lastModMinTsVal;

    protected $storeRepository;

    protected $store;

    public function __construct(
        Escaper $escaper,
        Filesystem $filesystem,
        StoreRepositoryInterface $storeRepository
    ) {
        $this->escaper = $escaper;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::PUB);
        $this->storeRepository = $storeRepository;
    }

    /**
     * Get file handler
     *
     * @return WriteInterface
     * @throws LocalizedException
     */
    protected function getStream()
    {
        if ($this->stream) {
            return $this->stream;
        }
        throw new LocalizedException(__('File handler unreachable'));
    }

    /**
     * Initialize sitemap
     *
     * @return void
     */
    protected function initSitemapBaseLayout()
    {
        $this->tags = [
            self::TYPE_URL => [
                self::OPEN_TAG_KEY => '<?xml version="1.0" encoding="UTF-8"?>' .
                    PHP_EOL .
                    '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' .
                    ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' .
                    PHP_EOL,
                self::CLOSE_TAG_KEY => '</urlset>',
            ],
        ];
    }

    /**
     * Check sitemap file location and permissions
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function preChecks()
    {
        $path = $this->getSitemapPath();

        /**
         * Check path is allow
         */
        if ($path && preg_match('#\.\.[\\\/]#', $path)) {
            throw new LocalizedException(__('Please define a correct path.'));
        }
        /**
         * Check exists and writable path
         */
        if (!$this->directory->isExist($path)) {
            throw new LocalizedException(
                __(
                    'Please create the specified folder "%1" before saving the sitemap.',
                    $this->escaper->escapeHtml($this->getSitemapPath())
                )
            );
        }

        if (!$this->directory->isWritable($path)) {
            throw new LocalizedException(
                __('Please make sure that "%1" is writable by the web-server.', $this->getSitemapPath())
            );
        }

        return $this;
    }

    public function generateXml()
    {
        $this->initSitemapBaseLayout();
        $this->preChecks();
        foreach ($this->storeRepository->getList() as $store) {
            $this->setStore($store);
            $items = $this->getItems($store->getId());
            if (!count($items)) {
                continue;
            }
            foreach ($items as $item) {
                $xmlRow = $this->getSitemapRow(
                    $item['url'],
                    $item['updated_at']
                );
                if (!$this->fileSize) {
                    $this->createSitemap();
                }
                $this->writeSitemapRow($xmlRow);
                $this->lineCount++;
                $this->fileSize += strlen($xmlRow);
            }
            $this->finalizeSitemap();
        }

        return $this;
    }

    protected function getSitemapRow($url, $lastmod = null)
    {
        $url = $this->getUrl($url);
        $row = '<loc>' . $this->escaper->escapeUrl($url) . '</loc>';
        if ($lastmod) {
            $row .= '<lastmod>' . $this->getFormattedLastmodDate($lastmod) . '</lastmod>';
        }
        $row .= '<changefreq>weekly</changefreq>';

        return '<url>' . $row . '</url>';
    }

    /**
     * Create new sitemap file
     *
     * @param null|string $fileName
     * @param string $type
     * @return void
     * @throws LocalizedException
     */
    protected function createSitemap($fileName = null, $type = self::TYPE_URL)
    {
        if (!$fileName) {
            $fileName = $this->getCurrentSitemapFilename();
        }

        $path = ($this->getSitemapPath() !== null ? rtrim($this->getSitemapPath(), '/') : '') . '/' . $fileName;
        $this->stream = $this->directory->openFile($path);

        $fileHeader = sprintf($this->tags[$type][self::OPEN_TAG_KEY], $type);
        $this->stream->write($fileHeader);
        $this->fileSize = strlen($fileHeader . sprintf($this->tags[$type][self::CLOSE_TAG_KEY], $type));
    }

    /**
     * Write sitemap row
     *
     * @param string $row
     * @return void
     */
    protected function writeSitemapRow($row)
    {
        $this->getStream()->write($row . PHP_EOL);
    }

    /**
     * Write closing tag and close stream
     *
     * @param string $type
     * @return void
     */
    protected function finalizeSitemap($type = self::TYPE_URL)
    {
        if ($this->stream) {
            $this->stream->write(sprintf($this->tags[$type][self::CLOSE_TAG_KEY], $type));
            $this->stream->close();
        }

        // Reset all counters
        $this->lineCount = 0;
        $this->fileSize = 0;
    }

    /**
     * Get current sitemap filename
     *
     * @param int $index
     * @return string
     */
    abstract protected function getCurrentSitemapFilename();

    /**
     * Get base dir
     *
     * @return string
     */
    protected function getBaseDir()
    {
        return $this->directory->getAbsolutePath();
    }

    /**
     * Get store base url
     *
     * @param string $type
     * @return string
     */
    protected function getStoreBaseUrl($type = UrlInterface::URL_TYPE_LINK)
    {
        $store = $this->getStore();
        $isSecure = $store->isUrlSecure();
        return rtrim($store->getBaseUrl($type, $isSecure), '/') . '/';
    }

    /**
     * Get url
     *
     * @param string $url
     * @param string $type
     * @return string
     */
    protected function getUrl($url, $type = UrlInterface::URL_TYPE_LINK)
    {
        return $this->getStoreBaseUrl($type) . ($url !== null ? ltrim($url, '/') : '');
    }

    /**
     * Get date in correct format applicable for lastmod attribute
     *
     * @param string $date
     * @return string
     */
    protected function getFormattedLastmodDate($date)
    {
        if ($this->lastModMinTsVal === null) {
            $this->lastModMinTsVal = strtotime(self::LAST_MOD_MIN_VAL);
        }
        $timestamp = max(strtotime($date), $this->lastModMinTsVal);
        return date('c', $timestamp);
    }

    protected function getStore()
    {
        return $this->store;
    }

    protected function setStore($store)
    {
        $this->store = $store;
        return $this;
    }

    protected function getSitemapPath()
    {
        return self::SITEMAP_PATH;
    }

    abstract protected function getItems($storeId);
}
