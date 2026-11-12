<?php
namespace LiveChat\LiveChat\Controller\GetVisitor;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_customerSession;
    /**
     * @var UrlInterface
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession
    ){
        $this->_customerSession = $customerSession;
        return parent::__construct($context);
    } 

    public function execute()
    {
        $visitor_data = 'var livechat_visitor_data = '.json_encode($this->getCustomerDetails());
       
        /** @var \Magento\Framework\App\ObjectManager $om */
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var     \Magento\Framework\App\ResponseInterface|\Magento\Framework\App\Response\Http $response */
        $response = $om->get('Magento\Framework\App\ResponseInterface');
        $response->setHeader('Content-type', 'application/javascript', $overwriteExisting = true);
        $response->setBody($visitor_data); 
        return $response;
    }

	/**
     * Returns last order details.
     * @return string
     */
    public function getCustomerDetails()
    {
        $result = array();

        if (null !== ($email = $this->getCustomerEmail())) {
            $result['email'] =  $email;
        }

        if (null !== ($name = trim($this->getCustomerName())) && '' !== $name) {
            $result['name'] = $name;
        }
        
        return $result;
    }

	/**
     * Returns customers email.
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->_customerSession->getCustomer()->getEmail();
    }

	/**
     * Returns customers name.
     * @return string
     */
    public function getCustomerName()
    {
        return $this->_customerSession->getCustomer()->getName();
    }
}