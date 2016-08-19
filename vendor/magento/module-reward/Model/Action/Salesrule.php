<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Model\Action;

/**
 * Reward action for updating balance by salesrule
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Salesrule extends \Magento\Reward\Model\Action\AbstractAction
{
    /**
     * Quote instance, required for estimating checkout reward (rule defined static value)
     *
     * @var \Magento\Quote\Model\Quote|null
     */
    protected $_quote = null;

    /**
     * Reward factory
     *
     * @var \Magento\Reward\Model\ResourceModel\RewardFactory
     */
    protected $_rewardFactory;

    /**
     * @param \Magento\Reward\Model\ResourceModel\RewardFactory $rewardFactory
     * @param array $data
     */
    public function __construct(\Magento\Reward\Model\ResourceModel\RewardFactory $rewardFactory, array $data = [])
    {
        $this->_rewardFactory = $rewardFactory;
        parent::__construct($data);
    }

    /**
     * Retrieve points delta for action
     *
     * @param int $websiteId
     * @return int
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getPoints($websiteId)
    {
        $pointsDelta = 0;
        if ($this->_quote) {
            // known issue: no support for multishipping quote // copied  comment, not checked
            if ($this->_quote->getAppliedRuleIds()) {
                $ruleIds = explode(',', $this->_quote->getAppliedRuleIds());
                $ruleIds = array_unique($ruleIds);
                $data = $this->_rewardFactory->create()->getRewardSalesrule($ruleIds);
                foreach ($data as $rule) {
                    $pointsDelta += (int)$rule['points_delta'];
                }
            }
        }
        return $pointsDelta;
    }

    /**
     * Quote setter
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     * @codeCoverageIgnore
     */
    public function setQuote(\Magento\Quote\Model\Quote $quote)
    {
        $this->_quote = $quote;
        return $this;
    }

    /**
     * Check whether rewards can be added for action
     *
     * @return bool
     */
    public function canAddRewardPoints()
    {
        return true;
    }

    /**
     * Return action message for history log
     *
     * @param array $args Additional history data
     * @return \Magento\Framework\Phrase
     */
    public function getHistoryMessage($args = [])
    {
        $incrementId = isset($args['increment_id']) ? $args['increment_id'] : '';
        return __('Earned promotion extra points from order #%1', $incrementId);
    }

    /**
     * Setter for $_entity and add some extra data to history
     *
     * @param \Magento\Framework\DataObject $entity
     * @return $this
     * @codeCoverageIgnore
     */
    public function setEntity($entity)
    {
        parent::setEntity($entity);
        $this->getHistory()->addAdditionalData(['increment_id' => $this->getEntity()->getIncrementId()]);
        return $this;
    }
}
