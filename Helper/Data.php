<?php

namespace LiveChat\LiveChat\Helper;

use \Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
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

	/**
	 * @param Context $context
	 */
	public function __construct(
		Context $context
	) {
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

	public function getLicenseEmail()
	{
		return $this->scopeConfig->getValue(self::LC_LICENSE_EMAIL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
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
}
