<?php
namespace LiveChat\LiveChat\Controller\Adminhtml\SetProps;

use Magento\Backend\App\Action\Context;

class Index extends \Magento\Backend\App\Action
{
	protected $resultPageFactory;
	protected $configWriter;
	
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
		
		$this->configWriter->save('lc_block_config/custom_params/cart_products', $post['cart_products']);
		$this->configWriter->save('lc_block_config/custom_params/total_cart_value', $post['total_cart_value']);
		$this->configWriter->save('lc_block_config/custom_params/total_orders_count', $post['total_orders_count']);
		$this->configWriter->save('lc_block_config/custom_params/last_order_details', $post['last_order_details']);
				
		$result = $this->resultJsonFactory->create();

		$this->clearCache();
		return $result->setData(['success' => 'custom_params saved']);
	}
}