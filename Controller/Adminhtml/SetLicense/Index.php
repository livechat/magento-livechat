<?php
namespace LiveChat\LiveChat\Controller\Adminhtml\SetLicense;

use Magento\Backend\App\Action\Context;

class Index extends \Magento\Backend\App\Action
{
	protected $resultPageFactory;
	protected $configWriter;
	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	private $resultJsonFactory;
	/**
	 * @var \Magento\Framework\App\Cache\ManagerFactory
	 */
	private $cacheManagerFactory;

	public function __construct(
		Context $context,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
		\Magento\Framework\App\Cache\ManagerFactory $cacheManagerFactory
	) {
		parent::__construct($context);
		$this->resultJsonFactory = $resultJsonFactory;
		$this->configWriter = $configWriter;
		$this->cacheManagerFactory = $cacheManagerFactory;
	}
	
	private function clearCache() {
	  $cacheManager = $this->cacheManagerFactory->create();
	  $types = $cacheManager->getAvailableTypes();
	  $cacheManager->clean($types);
	}

	public function execute()
	{
		$post = $this->getRequest()->getPostValue();
		
		$this->configWriter->save('lc_block_config/account/license_email', $post['email']);
		$this->configWriter->save('lc_block_config/account/license_id', $post['license']);

		$this->configWriter->save('lc_block_config/custom_params/cart_products', '1');
		$this->configWriter->save('lc_block_config/custom_params/total_cart_value', '1');
		$this->configWriter->save('lc_block_config/custom_params/total_orders_count', '1');
		$this->configWriter->save('lc_block_config/custom_params/last_order_details', '1');
				
		$result = $this->resultJsonFactory->create();

		$this->clearCache();
		
		return $result->setData(['success' => 'license saved']);
	}
}