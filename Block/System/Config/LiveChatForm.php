<?php
namespace LiveChat\LiveChat\Block\System\Config;

use \LiveChat\LiveChat\Helper\Data;

class LiveChatForm extends \Magento\Framework\View\Element\Template
{
	/**
	 * Path to block template
	 */
	const CHECK_TEMPLATE = 'system/config/livechat_form.phtml';
	
	private $dataHelper;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		Data $dataHelper,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->dataHelper = $dataHelper;
		$this->urlinterface = $context->getUrlBuilder();
	}
	
	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		if (!$this->getTemplate()) {
			$this->setTemplate(static::CHECK_TEMPLATE);
		}
		return $this;
	}
	
	public function getLicenseId()
	{
		return $this->dataHelper->getLicenseId();
	}
	
	public function getLicenseEmail()
	{
		return $this->dataHelper->getLicenseEmail();
	}
	
	public function isSetCartProducts()
	{
		return $this->dataHelper->showCustomParam(Data::LC_CP_SHOW_CART_PRODUCTS);
	}
	
	public function isSetTotalCartValue()
	{
		return $this->dataHelper->showCustomParam(Data::LC_CP_SHOW_TOTAL_CART_VALUE);
	}
	
	public function isSetTotalOrdersCount()
	{
		return $this->dataHelper->showCustomParam(Data::LC_CP_SHOW_TOTAL_ORDERS_COUNT);
	}
	
	public function isSetLastOrderDetalils()
	{	
		return $this->dataHelper->showCustomParam(Data::LC_CP_SHOW_LAST_ORDER_DETAILS);
	}
}