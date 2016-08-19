<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Model\Plugin;

use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Model\RuleRepository;
use Magento\SalesRule\Api\Data\RuleExtensionInterface;
use Magento\SalesRule\Api\Data\RuleExtensionFactory;
use Magento\Reward\Model\ResourceModel\RewardFactory;

class RuleRepositoryPlugin
{
    /**
     * @var RuleRepository
     */
    protected $ruleRepository;

    /**
     * @var RuleExtensionFactory
     */
    protected $ruleExtensionFactory;

    /**
     * @var RewardFactory
     */
    protected $rewardFactory;

    /**
     * @param RuleRepository $ruleRepository
     * @param RuleExtensionFactory $ruleExtensionFactory
     * @param RewardFactory $rewardFactory
     */
    public function __construct(
        RuleRepository $ruleRepository,
        RuleExtensionFactory $ruleExtensionFactory,
        RewardFactory $rewardFactory
    ) {
        $this->ruleRepository = $ruleRepository;
        $this->ruleExtensionFactory = $ruleExtensionFactory;
        $this->rewardFactory = $rewardFactory;
    }

    /**
     * @param RuleRepository $subject
     * @param \Closure $proceed
     * @param int $ruleId
     * @return RuleInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetById(RuleRepository $subject, \Closure $proceed, $ruleId)
    {
        /** @var RuleInterface $rule */
        $rule = $proceed($ruleId);

        /** @var \Magento\Reward\Model\ResourceModel\Reward $reward */
        $reward = $this->rewardFactory->create();
        $rewardSalesRule = $reward->getRewardSalesrule($ruleId);
        if (!$rewardSalesRule) {
            return $rule;
        }
        $rewardPointsDelta = $rewardSalesRule['points_delta'];
        $this->addRewardPointsToRule($rewardPointsDelta, $rule);

        return $rule;
    }

    /**
     * Save reward points
     *
     * @param RuleRepository $subject
     * @param \Closure $proceed
     * @param RuleInterface $rule
     * @return $this|RuleInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(RuleRepository $subject, \Closure $proceed, RuleInterface $rule)
    {
        /** @var RuleInterface $savedRule */
        $savedRule = $proceed($rule);

        if (!($rule->getExtensionAttributes() && $rule->getExtensionAttributes()->getRewardPointsDelta())) {
            return $savedRule;
        }
        $rewardPointsDelta = $rule->getExtensionAttributes()->getRewardPointsDelta();

        $this->rewardFactory->create()->saveRewardSalesrule(
            $savedRule->getRuleId(),
            $rewardPointsDelta
        );
        $this->addRewardPointsToRule($rewardPointsDelta, $savedRule);

        return $savedRule;
    }

    /**
     * Add reward points to sales rule
     *
     * @param int $rewardPointsDelta
     * @param RuleInterface $rule
     * @return void
     */
    protected function addRewardPointsToRule($rewardPointsDelta, RuleInterface $rule)
    {
        /** @var RuleExtensionInterface $extensionAttributes */
        $extensionAttributes = $rule->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->ruleExtensionFactory->create();
        }
        $extensionAttributes->setRewardPointsDelta($rewardPointsDelta);
        $rule->setExtensionAttributes($extensionAttributes);
    }
}
