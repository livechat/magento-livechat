<?php

namespace LiveChat\LiveChat\Helper;
use \Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	const LC_LICENSE_ID = 'lc_block_config/account/license_id';

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
}
