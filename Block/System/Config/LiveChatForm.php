<?php

namespace LiveChat\LiveChat\Block\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Hidden extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Unset some non-related element parameters
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Disable and hide element.
     *
     * @param AbstractElement $element
     * @return type
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setType('hidden');

        return $element->getElementHtml();
    }
}
