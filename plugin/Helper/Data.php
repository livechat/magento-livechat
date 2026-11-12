<?php

namespace LiveChat\LiveChat\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	public const LAST_ORDER_DETAILS_PATTERN =
		'Created at: %createdAt%, updated at: %updatedAt%, status: %status%, state: %state%, '
		. 'grand total: %grandTotal% %currency%';
	public const PRODUCT_DETAILS_PATTERN = '%name% (%qty%) %price% %currency%; ';
	public const LC_LICENSE_ID = 'lc_block_config/account/license_id';
	public const LC_LICENSE_EMAIL = 'lc_block_config/account/license_email';
	public const LC_CP_SHOW_CART_PRODUCTS = 'lc_block_config/custom_params/cart_products';
	public const LC_CP_SHOW_TOTAL_CART_VALUE = 'lc_block_config/custom_params/total_cart_value';
	public const LC_CP_SHOW_TOTAL_ORDERS_COUNT = 'lc_block_config/custom_params/total_orders_count';
	public const LC_CP_SHOW_LAST_ORDER_DETAILS = 'lc_block_config/custom_params/last_order_details';

	/**
	 * Returns license id.
	 *
	 * @return int|string|null
	 */
	public function getLicenseId()
	{
		return $this->scopeConfig->getValue(self::LC_LICENSE_ID, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Returns license email.
	 *
	 * @return string|null
	 */
	public function getLicenseEmail()
	{
		return $this->scopeConfig->getValue(self::LC_LICENSE_EMAIL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Check by key if custom param should be shown.
	 *
	 * @param string $key
	 * @return bool
	 */
	public function showCustomParam($key)
	{
		return (boolean) $this->scopeConfig->getValue($key, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
}
