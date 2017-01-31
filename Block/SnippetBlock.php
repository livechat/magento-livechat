<?php
namespace LiveChat\LiveChat\Block;

use \LiveChat\LiveChat\Helper\Data;

class SnippetBlock extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Data
     */
    private $dataHelper;
    /**
     * @var UrlInterface
     */
    private $urlinterface;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Data $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Data $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
        $this->urlinterface = $context->getUrlBuilder();
    }

    /**
     * Returns license id.
     *
     * @return integer
     */
    public function getLicenseId()
    {
        return $this->dataHelper->getLicenseId();
    }

    /**
     * Returns customer details.
     *
     * @return array
     */
    public function getCustomerDetails()
    {
        $result = array();

        if (null !== ($email = $this->dataHelper->getCustomerEmail())) {
            $result[] = array('key' => 'email', 'value' => $email);
        }

        if (null !== ($name = trim($this->dataHelper->getCustomerName())) && '' !== $name) {
            $result[] = array('key' => 'name', 'value' => $name);
        }

        return $result;
    }

    /**
     * Returns custom variables.
     *
     * @return array
     */
    public function getCustomVariables()
    {
        $result = array();
        if ($this->isOrderPlacedPage()) {
            $result[] = array('name' => 'LC_ORDER_SUCCESS', 'value' => '1');
        }
        if (
            true === $this->dataHelper->showCustomParam(Data::LC_CP_SHOW_CART_PRODUCTS) &&
            null !== ($productDetails = $this->dataHelper->getProductDetails())
        ) {
            $result[] = array('name' => 'Cart products', 'value' => $productDetails);
        }

        if (
            true === $this->dataHelper->showCustomParam(Data::LC_CP_SHOW_TOTAL_CART_VALUE) &&
            null !== ($cartTotal = $this->dataHelper->getCartGrandTotal())
        ) {
            $result[] = array('name' => 'Cart total value', 'value' => $cartTotal);
        }

        if (
            true === $this->dataHelper->showCustomParam(Data::LC_CP_SHOW_TOTAL_ORDERS_COUNT) &&
            null !== ($ordersCount = $this->dataHelper->getTotalOrdersCount())
        ) {
            $result[] = array('name' => 'Total orders count', 'value' => $ordersCount);
        }

        if (
            true === $this->dataHelper->showCustomParam(Data::LC_CP_SHOW_LAST_ORDER_DETAILS) &&
            null !== ($latOrderDetails = $this->dataHelper->getLastOrderDetails())
        ) {
            $result[] = array('name' => 'Last order details', 'value' => $latOrderDetails);
        }

        return $result;
    }

    /**
     * Checks if there is order success page.
     * @return boolean
     */
    private function isOrderPlacedPage()
    {
        if (
            'checkout/onepage/success/' === str_replace($this->getBaseUrl(), '', $this->urlinterface->getCurrentUrl())
        ) {
            return true;
        }

        return false;
    }
}
