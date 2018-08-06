<?php
namespace LiveChat\LiveChat\Controller\GetVisitor;

use \LiveChat\LiveChat\Helper\Data;

class Index extends \Magento\Framework\App\Action\Action
{
	const LAST_ORDER_DETAILS_PATTERN =
		'Created at: %createdAt%, updated at: %updatedAt%, status: %status%, state: %state%, grand total: %grandTotal% %currency%';
	const PRODUCT_DETAILS_PATTERN = '%name% (%qty%) %price% %currency%; ';
	const LC_LICENSE_ID = 'lc_block_config/account/license_id';
	const LC_LICENSE_EMAIL = 'lc_block_config/account/license_email';
	const LC_CP_SHOW_CART_PRODUCTS = 'lc_block_config/custom_params/cart_products';
	const LC_CP_SHOW_TOTAL_CART_VALUE = 'lc_block_config/custom_params/total_cart_value';
	const LC_CP_SHOW_TOTAL_ORDERS_COUNT = 'lc_block_config/custom_params/total_orders_count';
	const LC_CP_SHOW_LAST_ORDER_DETAILS = 'lc_block_config/custom_params/last_order_details';

	protected $_pageFactory;
    protected $_cart;
    protected $_customerSession;
    protected $_orderCollectionFactory;
	protected $_scopeConfig;
	
	/**
	 * @var UrlInterface
	 */
	private $urlinterface;

	private $serializer;


    public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Element\Template\Context $templateContext,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Checkout\Model\Cart $cart, 
        \Magento\Customer\Model\Session $customerSession, 
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ){
        $this->_pageFactory = $pageFactory;
        $this->_cart = $cart;
        $this->_customerSession = $customerSession;
        $this->_orderCollectionFactory = $orderCollectionFactory;
		$this->_scopeConfig = $scopeConfig;
		$this->urlinterface = $templateContext->getUrlBuilder();
		$this->serializer = $serializer;
		return parent::__construct($context);
    } 

	public function execute()
	{
		$visitor_data = 'var visitor_data = '.$this->serializer->serialize($this->getCustomerDetails());
	
		header('Content-type: application/javascript');
		echo $visitor_data;	
	}

	public function getCustomerDetails()
	{
		$result = array();

		if (null !== ($email = $this->getCustomerEmail())) {
			$result['email'] =  $email;
		}

		if (null !== ($name = trim($this->getCustomerName())) && '' !== $name) {
			$result['name'] = $name;
		}

		// return array_merge(...array_values($result));
		return $result;
	}

	public function getCustomerEmail()
	{
		return $this->_customerSession->getCustomer()->getEmail();
	}

	public function getCustomerName()
	{
		return $this->_customerSession->getCustomer()->getName();
	}
}