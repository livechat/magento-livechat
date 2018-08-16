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
	 * Checks if cart functions are set
	 *
	 * @return bool
	 */
	public function isCartSet()
	{
		if($this->dataHelper->showCustomParam(Data::LC_CP_SHOW_CART_PRODUCTS) ||
		   $this->dataHelper->showCustomParam(Data::LC_CP_SHOW_TOTAL_CART_VALUE) ||
		   $this->dataHelper->showCustomParam(Data::LC_CP_SHOW_TOTAL_ORDERS_COUNT) ||
		   $this->dataHelper->showCustomParam(Data::LC_CP_SHOW_LAST_ORDER_DETAILS)) {
			return true;
		}
		
		return false;
	}

	/**
	 * Checks if there is order success page.
	 * @return boolean
	 */
	public function isOrderPlacedPage()
	{
		if (
			'checkout/onepage/success/' === str_replace($this->getBaseUrl(), '', $this->urlinterface->getCurrentUrl())
		) {
			return 1;
		}

		return 0;
	}
}
