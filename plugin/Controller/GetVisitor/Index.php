<?php
namespace LiveChat\LiveChat\Controller\GetVisitor;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_customerSession = $customerSession;
        return parent::__construct($context);
    }

    /**
     * Outputs the visitor data as a JavaScript variable.
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $visitor_data = 'var livechat_visitor_data = '.json_encode($this->getCustomerDetails());
       
        /** @var \Magento\Framework\App\ObjectManager $om */
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var     \Magento\Framework\App\ResponseInterface|\Magento\Framework\App\Response\Http $response */
        $response = $om->get(\Magento\Framework\App\ResponseInterface::class);
        $response->setHeader('Content-type', 'application/javascript', $overwriteExisting = true);
        $response->setBody($visitor_data);
        return $response;
    }

    /**
     * Returns customer details.
     *
     * @return array
     */
    public function getCustomerDetails()
    {
        $result = [];

        if (null !== ($email = $this->getCustomerEmail())) {
            $result['email'] =  $email;
        }

        if (null !== ($name = trim($this->getCustomerName())) && '' !== $name) {
            $result['name'] = $name;
        }
        
        return $result;
    }

    /**
     * Returns customer email.
     *
     * @return string|null
     */
    public function getCustomerEmail()
    {
        return $this->_customerSession->getCustomer()->getEmail();
    }

    /**
     * Returns customer name.
     *
     * @return string
     */
    public function getCustomerName()
    {
        return $this->_customerSession->getCustomer()->getName();
    }
}
