<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerBalance\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Customer\Controller\RegistryConstants;

/**
 * Controller for Customer account -> Store Credit ajax tab and all its contents
 */
abstract class Customerbalance extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\CustomerBalance\Model\Balance
     */
    protected $_balance;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\CustomerBalance\Model\Balance $balance
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\CustomerBalance\Model\Balance $balance,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_balanceFactory = $balance;
        $this->_customerFactory = $customerFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $this->_request = $request;
        if (!$this->_objectManager->get('Magento\CustomerBalance\Helper\Data')->isEnabled()) {
            if ($request->getActionName() != 'noroute') {
                $this->_forward('noroute');
                return $this->getResponse();
            }
        }
        return parent::dispatch($request);
    }

    /**
     * Instantiate customer model
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function initCurrentCustomer()
    {
        $customer = $this->_customerFactory->create()->load((int)$this->getRequest()->getParam('id'));
        if (!$customer->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Failed to initialize customer'));
        }
        $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customer->getId());
    }

    /**
     * Check is allowed customer management
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Customer::manage');
    }
}
