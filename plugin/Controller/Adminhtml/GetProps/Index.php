<?php
namespace LiveChat\LiveChat\Controller\Adminhtml\GetProps;

use \LiveChat\LiveChat\Helper\Data;
use Magento\Backend\App\Action\Context;

class Index extends \Magento\Backend\App\Action
{
	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;
	/**
	 * @var \Magento\Framework\App\Config\Storage\WriterInterface
	 */
	protected $configWriter;
	
	/**
	 * @var \Magento\Framework\App\Cache\ManagerFactory
	 */
	private $cacheManagerFactory;
	/**
	 * @var Data
	 */
	private $dataHelper;
	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	private $resultJsonFactory;

	/**
	 * @param Context $context
	 * @param Data $dataHelper
	 * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	 * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
	 * @param \Magento\Framework\App\Cache\ManagerFactory $cacheManagerFactory
	 */
	public function __construct(
		Context $context,
		Data $dataHelper,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
		\Magento\Framework\App\Cache\ManagerFactory $cacheManagerFactory
	) {
		parent::__construct($context);
		$this->dataHelper = $dataHelper;
		$this->resultJsonFactory = $resultJsonFactory;
		$this->configWriter = $configWriter;
		$this->cacheManagerFactory = $cacheManagerFactory;
	}
	
	/**
	 * Cleans all cache types.
	 *
	 * @return void
	 */
	private function clearCache()
	{
		$cacheManager = $this->cacheManagerFactory->create();
		$types = $cacheManager->getAvailableTypes();
		$cacheManager->clean($types);
	}
	
	/**
	 * Returns the saved license and custom params settings.
	 *
	 * @return \Magento\Framework\Controller\Result\Json
	 */
	public function execute()
	{
		$license_settings = [];
		
		$license_settings['license_email'] = $this->dataHelper->getLicenseEmail();
		$license_settings['cart_products'] = $this->dataHelper->showCustomParam(Data::LC_CP_SHOW_CART_PRODUCTS);
		$license_settings['total_cart_value'] =
				$this->dataHelper->showCustomParam(Data::LC_CP_SHOW_TOTAL_CART_VALUE);
		$license_settings['total_orders_count'] =
				$this->dataHelper->showCustomParam(Data::LC_CP_SHOW_TOTAL_ORDERS_COUNT);
		$license_settings['last_order_details'] =
				$this->dataHelper->showCustomParam(Data::LC_CP_SHOW_LAST_ORDER_DETAILS);
		
		$result = $this->resultJsonFactory->create();

		return $result->setData(['license_settings' => json_encode($license_settings)]);
	}
}
