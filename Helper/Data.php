<?php

namespace LiveChat\LiveChat\Helper;

use \Magento\Customer\Model\Session;
use \Magento\Checkout\Model\Cart;
use \Magento\Framework\App\Helper\Context;
use \Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use \Magento\Sales\Model\Order;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const LAST_ORDER_DETAILS_PATTERN =
        'Created at: %createdAt%, updated at: %updatedAt%, status: %status%, state: %state%, grand total: %grandTotal% %currency%';
    const PRODUCT_DETAILS_PATTERN = '%name% (%qty%) %price% %currency%; ';
    const LC_LICENSE_ID = 'lc_block_config/account/license_id';
    const LC_CP_SHOW_CART_PRODUCTS = 'lc_block_config/custom_params/cart_products';
    const LC_CP_SHOW_TOTAL_CART_VALUE = 'lc_block_config/custom_params/total_cart_value';
    const LC_CP_SHOW_TOTAL_ORDERS_COUNT = 'lc_block_config/custom_params/total_orders_count';
    const LC_CP_SHOW_LAST_ORDER_DETAILS = 'lc_block_config/custom_params/last_order_details';

    /**
     * @var Cart
     */
    private $cart;
    /**
     * @var Session
     */
    private $customerSession;
    /**
     * @var CollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @param Context $context
     * @param Cart $cart
     * @param Session $customerSession
     * @param CollectionFactory $orderCollectionFactory
     */
    public function __construct(
        Context $context, Cart $cart, Session $customerSession, CollectionFactory $orderCollectionFactory
    ) {
        $this->cart = $cart;
        $this->customerSession = $customerSession;
        $this->orderCollectionFactory = $orderCollectionFactory;

        parent::__construct($context);
    }

    /**
     * Returns license id.
     * @return integer
     */
    public function getLicenseId()
    {
        return $this->scopeConfig->getValue(self::LC_LICENSE_ID, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Returns last order details.
     * @return string
     */
    public function getLastOrderDetails()
    {
        if (null === ($customerId = $this->customerSession->getCustomer()->getId())) {
            return null;
        }

        $orderRecord = $this->orderCollectionFactory->create()->addFieldToFilter('customer_id', $customerId)
            ->addOrder('created_at', 'DESC')->fetchItem();

        if (!($orderRecord instanceof Order)) {
            return null;
        }

        return str_replace(
            array('%createdAt%', '%updatedAt%', '%status%', '%state%', '%grandTotal%', '%currency%'),
            array(
                $orderRecord->getData('updated_at'),
                $orderRecord->getData('created_at'),
                $orderRecord->getData('status'),
                $orderRecord->getData('state'),
                number_format(round($orderRecord->getData('grand_total'), 2), 2),
                $orderRecord->getData('order_currency_code')
            ),
            self::LAST_ORDER_DETAILS_PATTERN
        );
    }

    /**
     * Returns total orers count.
     * @return integer
     */
    public function getTotalOrdersCount()
    {
        if (null === ($customerId = $this->customerSession->getCustomer()->getId())) {
            return null;
        }

        return $this->orderCollectionFactory->create()->addFieldToFilter('customer_id', $customerId)
            ->addOrder('created_at', 'DESC')->count();
    }

    /**
     * Check by key if custom param should be shown.
     * @param string $key
     * @return boolean
     */
    public function showCustomParam($key)
    {
        return (boolean) $this->scopeConfig->getValue($key, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Returns current cart details.
     *
     * @return string
     */
    public function getProductDetails()
    {
        $productDetails = null;
        foreach ($this->cart->getQuote()->getAllItems() as $item) {
            $data = $item->getQuote()->getData();
            $productDetails .= str_replace(
                array('%name%', '%qty%', '%price%', '%currency%'),
                array(
                    $item->getName(),
                    $item->getQty(),
                    number_format(round($item->getPrice(), 2), 2),
                    $data['quote_currency_code']
                ),
                self::PRODUCT_DETAILS_PATTERN
            );
        }

        return (null !== $productDetails) ? rtrim($productDetails, self::PRODUCT_DETAILS_PATTERN) : null;
    }

    /**
     * Returns current cart total.
     *
     * @return string
     */
    public function getCartGrandTotal()
    {
        $cartData = $this->cart->getQuote()->getData();

        if (!array_key_exists('items_count', $cartData) || 0 >= (int) $cartData['items_count']) {
            return null;
        }

        if (array_key_exists('base_grand_total', $cartData)) {
            return number_format(round($cartData['base_grand_total'], 2), 2) . ' ' . $cartData['quote_currency_code'];
        }

        return null;
    }

    public function getCustomerEmail()
    {
        return $this->customerSession->getCustomer()->getEmail();
    }

    public function getCustomerName()
    {
        return $this->customerSession->getCustomer()->getName();
    }
}
