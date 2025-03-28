<?php

namespace GiftGroup\GeoPage\Block\Adminhtml\StatePage\Edit;

use Magento\Framework\App\RequestInterface;
use Magento\Backend\Model\UrlInterface;

/**
 * General button class to hold some common functions
 */
class GenericButton
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * GenericButton construct function
     *
     * @param RequestInterface $request
     * @param UrlInterface $urlBuilder
     * @return void
     */
    public function __construct(
        RequestInterface $request,
        UrlInterface     $urlBuilder
    ) {
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Return the id from the url
     *
     * @return mixed
     */
    public function getId(): mixed
    {
        return $this->request->getParam('id');
    }

    /**
     * Generate url by route and parameters
     *
     * @param string       $route
     * @param array<mixed> $params
     * @return  string
     */
    public function getUrl($route = '', $params = []): string
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
