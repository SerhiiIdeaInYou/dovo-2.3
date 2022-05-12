<?php
namespace Harrigo\RegisterCheckout\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class Register extends \Magento\Framework\App\Action\Action
{
	protected $resultPageFactory;
	protected  $storeManager;
	
	/**
	 * Constructor
	 * 
	 * @param \Magento\Framework\App\Action\Context  $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
	)
	{
	    $this->storeManager = $storeManager;
		$this->resultPageFactory = $resultPageFactory;
		$this->_resultFactory = $context->getResultFactory();
		parent::__construct($context);
	}
	
	/**
	 * Execute view action
	 * 
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute()
	{
	    if(isset($_POST['lang'])) {
	        $lang = explode('/', $_POST['lang']);
        }
	    if(in_array('en', $lang)) {
	        $this->storeManager->setCurrentStore(2);
        }
		//if (isset($_POST["cart"])) {
        //echo $this->storeManager->getStore()->getId();
			$resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
			return $resultLayout;   
		//}
		//$this->_redirect('checkout/');

	}
}