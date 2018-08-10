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
}
