<?php
namespace Piimega\Maksuturva\Controller\Adminhtml\Index;

class Run extends \Magento\Backend\App\Action
{
    protected $_maksuturvaCronModel;
    protected $_checkoutSession;
    protected $registry;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Piimega\Maksuturva\Model\Cron $maksuturvaCron,
        \Magento\Checkout\Model\Session $checkoutsession,
        \Magento\Framework\Registry $registry
    )
    {
        $this->_maksuturvaCronModel = $maksuturvaCron;
        $this->_checkoutSession = $checkoutsession;
        $this->registry = $registry;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);

        try{
            $this->registry->register('run_cron_manually', true);
            if($this->getRequest()->getParam('is_long_term')){
                $this->_maksuturvaCronModel->checkPaymentStatus("-7 days");
            }else{
                $this->_maksuturvaCronModel->checkPaymentStatus("-2 hours");
            }

            $this->messageManager->addSuccess(__('Run Query Maksuturva API for orders missing payments manually success'));
        }catch (Exception $e){
            $this->messageManager->addError(__('Unknown exception happen.'));
        }
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        return $resultRedirect;
    }
}