<?php

declare(strict_types=1);

namespace GiftGroup\GeoPage\Ui\City\Component\Form\Field;

use Magento\Ui\Component\Form\Field;
use GiftGroup\GeoPage\Model\Config;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class to provide default data to textarea fields
 */
class DefaultValue extends Field
{
    private $config;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Config $config,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->config = $config;
    }

    public function prepare(): void
    {
        parent::prepare();
        $this->_data['config']['default'] = $this->config->getDefaultValue($this->getName());
    }
}
