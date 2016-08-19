<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class RedeemForOrder implements ObserverInterface
{
    /**
     * Reward place order restriction interface
     *
     * @var \Magento\Reward\Observer\PlaceOrder\RestrictionInterface
     */
    protected $_restriction;

    /**
     * Reward model factory
     *
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $_modelFactory;

    /**
     * Core model store manager interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Reward balance validator
     *
     * @var \Magento\Reward\Model\Reward\Balance\Validator
     */
    protected $_validator;

    /**
     * Reward helper.
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $rewardHelper;

    /**
     * @param \Magento\Reward\Observer\PlaceOrder\RestrictionInterface $restriction
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Reward\Model\RewardFactory $modelFactory
     * @param \Magento\Reward\Model\Reward\Balance\Validator $validator
     */
    public function __construct(
        \Magento\Reward\Observer\PlaceOrder\RestrictionInterface $restriction,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Reward\Model\RewardFactory $modelFactory,
        \Magento\Reward\Model\Reward\Balance\Validator $validator
    ) {
        $this->_restriction = $restriction;
        $this->_storeManager = $storeManager;
        $this->_modelFactory = $modelFactory;
        $this->_validator = $validator;
    }

    /**
     * Reduce reward points if points was used during checkout
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (false == $this->_restriction->isAllowed()) {
            return;
        }

        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getOrder();
        /** @var $quote \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        if ($quote->getBaseRewardCurrencyAmount() > 0) {
            $this->_validator->validate($order);

            /** @var $model \Magento\Reward\Model\Reward */
            $model = $this->_modelFactory->create();
            $model->setCustomerId($order->getCustomerId());
            $model->setWebsiteId($this->_storeManager->getStore($order->getStoreId())->getWebsiteId());
            $model->setPointsDelta(-$quote->getRewardPointsBalance());
            $model->setAction(\Magento\Reward\Model\Reward::REWARD_ACTION_ORDER);
            $model->setActionEntity($order);
            $model->updateRewardPoints();
            $order->setRewardPointsBalance($quote->getRewardPointsBalance());
            $order->setRewardCurrencyAmount($quote->getRewardCurrencyAmount());
            $order->setBaseRewardCurrencyAmount($quote->getBaseRewardCurrencyAmount());
        }
    }
}
