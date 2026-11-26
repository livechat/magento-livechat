<?php
namespace LiveChat\LiveChat\Block\System\Config;

use \LiveChat\LiveChat\Helper\Data;

class LiveChatForm extends \Magento\Framework\View\Element\Template
{
	/**
	 * Path to block template
	 */
	public const CHECK_TEMPLATE = 'system/config/livechat_form.phtml';
	
	/**
	 * @var Data
	 */
	private $dataHelper;

	/**
	 * @var \Magento\Framework\UrlInterface
	 */
	private $urlinterface;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param Data $dataHelper
	 * @param array $data = []
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
	 * Sets the default template.
	 *
	 * @return $this
	 */
	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		if (!$this->getTemplate()) {
			$this->setTemplate(static::CHECK_TEMPLATE);
		}
		return $this;
	}
	
	/**
	 * Returns license id.
	 *
	 * @return int|string|null
	 */
	public function getLicenseId()
	{
		return $this->dataHelper->getLicenseId();
	}
	
	/**
	 * Returns license email.
	 *
	 * @return string|null
	 */
	public function getLicenseEmail()
	{
		return $this->dataHelper->getLicenseEmail();
	}
	
	/**
	 * Checks if cart products are sent to LiveChat.
	 *
	 * @return bool
	 */
	public function isSetCartProducts()
	{
		return $this->dataHelper->showCustomParam(Data::LC_CP_SHOW_CART_PRODUCTS);
	}
	
	/**
	 * Checks if total cart value is sent to LiveChat.
	 *
	 * @return bool
	 */
	public function isSetTotalCartValue()
	{
		return $this->dataHelper->showCustomParam(Data::LC_CP_SHOW_TOTAL_CART_VALUE);
	}
	
	/**
	 * Checks if total orders count is sent to LiveChat.
	 *
	 * @return bool
	 */
	public function isSetTotalOrdersCount()
	{
		return $this->dataHelper->showCustomParam(Data::LC_CP_SHOW_TOTAL_ORDERS_COUNT);
	}
	
	/**
	 * Checks if last order details are sent to LiveChat.
	 *
	 * @return bool
	 */
	public function isSetLastOrderDetalils()
	{
		return $this->dataHelper->showCustomParam(Data::LC_CP_SHOW_LAST_ORDER_DETAILS);
	}
}
