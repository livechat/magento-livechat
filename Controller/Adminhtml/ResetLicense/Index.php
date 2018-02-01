<?php
namespace LiveChat\LiveChat\Controller\Adminhtml\ResetLicense;

use Magento\Backend\App\Action\Context;

class Index extends \Magento\Backend\App\Action
{
	protected $resultPageFactory;
	protected $configWriter;

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
		$this->configWriter->save('lc_block_config/account/license_email', '0');
		$this->configWriter->save('lc_block_config/account/license_id', '0');
		$this->configWriter->save('lc_block_config/custom_params/cart_products', '0');
		$this->configWriter->save('lc_block_config/custom_params/total_cart_value', '0');
		$this->configWriter->save('lc_block_config/custom_params/total_orders_count', '0');
		$this->configWriter->save('lc_block_config/custom_params/last_order_details', '0');
				
		$result = $this->resultJsonFactory->create();

		$this->clearCache();
		
		return $result->setData(['success' => 'license removed']);
	}
}