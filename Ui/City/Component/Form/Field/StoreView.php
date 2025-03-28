<?php

declare(strict_types=1);

namespace GiftGroup\GeoPage\Ui\City\Component\Form\Field;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Form\Field;
use Magento\Framework\App\RequestInterface;

/**
 * Class to disable the store view dropdown field in the edit form.
 */
class StoreView extends Field
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * StoreView constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param RequestInterface $request
     * @param array<mixed> $components
     * @param array<mixed> $data
     * @return void
     */
    public function __construct(
        ContextInterface   $context,
        UiComponentFactory $uiComponentFactory,
        RequestInterface   $request,
        array              $components = [],
        array              $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->request = $request;
    }

    /**
     * Disable the store view select field if the page is an edit item page.
     *
     * @return void
     * @throws LocalizedException
     */
    public function prepare(): void
    {
        parent::prepare();
        $id = $this->request->getParam('id');
        if ($id) {
            $this->_data['config']['disabled'] = true;
        }
    }
}
