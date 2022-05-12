<?php
namespace Dovo\Base\Block\Header;

class CheckoutNav extends \Magento\Framework\View\Element\Template
{
    protected $_customerSession;
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Check customer Login or not
     */
    public function checkCustomerLogin()
    {
        $customer_data = [];
        if (!$this->_customerSession->isLoggedIn()) {
            $customer_data['text'] = "Login";
            $customer_data['url'] = $this->_storeManager->getStore()->getUrl('customer/account/login');
        } else {
            $customer_data['text'] = "Logout";
            $customer_data['url'] = $this->_storeManager->getStore()->getUrl('customer/account/logout');
        }
        return $customer_data;
    }
}