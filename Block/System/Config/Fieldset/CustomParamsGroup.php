<?php

namespace LiveChat\LiveChat\Block\System\Config\Fieldset;

use LiveChat\LiveChat\Helper\Data;

class CustomParamsGroup extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    /**
     * Render custom params settings group.
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $licenseId = $this->getData('form')->getConfigValue(Data::LC_LICENSE_ID);

        if (null === $licenseId) {
            return null;
        }

        return parent::render($element);
    }
}
