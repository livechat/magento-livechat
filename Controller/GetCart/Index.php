<?php
namespace LiveChat\LiveChat\Controller\GetCart;

use \LiveChat\LiveChat\Helper\Data;
class Index extends \Magento\Framework\App\Action\Action
{
	/**
	 * @var Cart
	 */
	protected $_cart;
	/**
	 * @var Session
	 */
	protected $_customerSession;
	/**
	 * @var CollectionFactory
	 */
	protected $_orderCollectionFactory;
	/**
	 * @var ScopeConfigInterface
	 */
    protected $_scopeConfig;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Cart $cart, 
        \Magento\Customer\Model\Session $customerSession, 
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ){
        $this->_cart = $cart;
        $this->_customerSession = $customerSession;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_scopeConfig = $scopeConfig;
        return parent::__construct($context);
    } 

    public function execute()
    {
        $custom_variables = json_encode($this->getCustomVariables());

        /** @var \Magento\Framework\App\ObjectManager $om */
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var     \Magento\Framework\App\ResponseInterface|\Magento\Framework\App\Response\Http $response */
        $response = $om->get('Magento\Framework\App\ResponseInterface');
        $response->setHeader('Content-type', 'application/json', $overwriteExisting = true);
        $response->setBody($custom_variables); 
        return $response;
    }
    
    /**
     * Returns last order details.
     * @return string
     */
    public function getLastOrderDetails()
    {
        if (null === ($customerId = $this->_customerSession->getCustomer()->getId())) {
            return null;
        }

        $orderRecord = $this->_orderCollectionFactory->create()->addFieldToFilter('customer_id', $customerId)
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
            Data::LAST_ORDER_DETAILS_PATTERN
        );
    }

    /**
     * Returns total orers count.
     * @return integer
     */
    public function getTotalOrdersCount()
    {
        if (null === ($customerId = $this->_customerSession->getCustomer()->getId())) {
            return null;
        }

        return $this->_orderCollectionFactory->create()->addFieldToFilter('customer_id', $customerId)
            ->addOrder('created_at', 'DESC')->count();
    }

    /**
     * Check by key if custom param should be shown.
     * @param string $key
     * @return boolean
     */
    public function showCustomParam($key)
    {
        return (boolean) $this->_scopeConfig->getValue($key, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Returns current _cart details.
     *
     * @return string
     */
    public function getProductDetails()
    {
        $productDetails = null;
        foreach ($this->_cart->getQuote()->getAllItems() as $item) {
            $data = $item->getQuote()->getData();
            $productDetails .= str_replace(
                array('%name%', '%qty%', '%price%', '%currency%'),
                array(
                    $item->getName(),
                    $item->getQty(),
                    number_format(round($item->getPrice(), 2), 2),
                    $data['quote_currency_code']
                ),
                Data::PRODUCT_DETAILS_PATTERN
            );
        }

        return (null !== $productDetails) ? rtrim($productDetails, Data::PRODUCT_DETAILS_PATTERN) : null;
    }

    /**
     * Returns current _cart total.
     *
     * @return string
     */
    public function getCartGrandTotal()
    {
        $cartData = $this->_cart->getQuote()->getData();

        if (!array_key_exists('items_count', $cartData) || 0 >= (int) $cartData['items_count']) {
            return null;
        }

        if (array_key_exists('base_grand_total', $cartData)) {
            return number_format(round($cartData['base_grand_total'], 2), 2) . ' ' . $cartData['quote_currency_code'];
        }

        return null;
    }

    /**
     * Returns custom variables.
     *
     * @return array
     */
    public function getCustomVariables()
    {   
        $is_order_completed = $this->_request->getParam('success');

        $result = array();
        if($is_order_completed) {
            $result[] = array('name' => 'LC_ORDER_SUCCESS', 'value' => '1');
        }
    
        if (
            true === $this->showCustomParam(Data::LC_CP_SHOW_CART_PRODUCTS) &&
            null !== ($productDetails = $this->getProductDetails())
        ) {
            $result[] = array('name' => 'Cart products', 'value' => $productDetails);
        }

        if (
            true === $this->showCustomParam(Data::LC_CP_SHOW_TOTAL_CART_VALUE) &&
            null !== ($cartTotal = $this->getCartGrandTotal())
        ) {
            $result[] = array('name' => 'Cart total value', 'value' => $cartTotal);
        }

        if (
            true === $this->showCustomParam(Data::LC_CP_SHOW_TOTAL_ORDERS_COUNT) &&
            null !== ($ordersCount = $this->getTotalOrdersCount())
        ) {
            $result[] = array('name' => 'Total orders count', 'value' => $ordersCount);
        }

        if (
            true === $this->showCustomParam(Data::LC_CP_SHOW_LAST_ORDER_DETAILS) &&
            null !== ($latOrderDetails = $this->getLastOrderDetails())
        ) {
            $result[] = array('name' => 'Last order details', 'value' => $latOrderDetails);
        }

        return $result;
    }
}