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
        $visitor_data = 'var livechat_visitor_data = '. json_encode($this->getCustomerDetails());
    
        header('Content-type: application/javascript');
        echo $visitor_data;    
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