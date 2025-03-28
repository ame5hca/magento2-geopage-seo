<?php

declare(strict_types=1);

namespace GiftGroup\GeoPage\Ui\City\Component\Form\Field;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Form\Field;
use Magento\Framework\App\RequestInterface;

/**
 * Class to disable the city code field in edit form.
 */
class CityCode extends Field
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * CityCode constructor.
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
     * Disable the city code field in edit form.
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
