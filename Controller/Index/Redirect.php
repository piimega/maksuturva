<?php
namespace Piimega\Maksuturva\Controller\Index;

class Redirect extends \Piimega\Maksuturva\Controller\Maksuturva
{
    protected $_checkoutSession;
    protected $_salesOrder;
    protected $_resultPageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Checkout\Model\Session $checkoutsession,
        \Piimega\Maksuturva\Helper\Data $maksuturvaHelper,
        array $data = []
    )
    {
        $this->_isScopePrivate = true;
        parent::__construct($context, $orderFactory, $logger, $scopeConfig, $quoteRepository, $checkoutsession, $maksuturvaHelper, $data);
        $this->_resultPageFactory = $resultLayoutFactory;
        $this->_salesOrder = $orderFactory->create();
        $this->_checkoutSession = $checkoutsession;
    }

    public function execute()
    {
        $this->_salesOrder->loadByIncrementId($this->_checkoutSession->getLastRealOrderId());
        $order = $this->_salesOrder;
        $paymentMethod = $order->getPayment()->getMethodInstance();
        //after restoreQuote, we can not get order IncrementId by loadByIncrementId() anymore
        $this->_checkoutSession->restoreQuote();
        $this->getResponse()->setBody(
            $this->_resultPageFactory->create()->getLayout()->createBlock('Piimega\Maksuturva\Block\Redirect\Maksuturva')
                ->setPaymentMethodInstance($paymentMethod)
                ->setOrder($order)->toHtml()
        );
        //restore quote when redirect to Maksuturva
    }
}