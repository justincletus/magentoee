<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Cybersource\Gateway\Command;

use Magento\Payment\Gateway\Command;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Sales\Model\Order;
use Magento\Cybersource\Gateway\Request\SilentOrder\PaymentTokenBuilder;

class AuthorizeStrategyCommand implements CommandInterface
{
    /**
     * Secure Acceptance authorize command name
     */
    const SECURE_ACCEPTANCE_AUTHORIZE = 'secure_acceptance_authorize';

    /**
     * Simple order authorize command name
     */
    const SIMPLE_ORDER_AUTHORIZE = 'simple_order_authorize';

    /**
     * Simple order subscription command name
     */
    const SIMPLE_ORDER_SUBSCRIPTION = 'simple_order_subscription';

    /**
     * @var Command\CommandPoolInterface
     */
    private $commandPool;

    /**
     * @param Command\CommandPoolInterface $commandPool
     */
    public function __construct(
        Command\CommandPoolInterface $commandPool
    ) {
        $this->commandPool = $commandPool;
    }

    /**
     * Executes command basing on business object
     *
     * @param array $commandSubject
     * @return null|Command\ResultInterface
     * @throws LocalizedException
     */
    public function execute(array $commandSubject)
    {
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = SubjectReader::readPayment($commandSubject);

        $paymentInfo = $paymentDO->getPayment();

        if ($paymentInfo->getAdditionalInformation(PaymentTokenBuilder::PAYMENT_TOKEN)) {
            return $this->commandPool
                ->get(self::SECURE_ACCEPTANCE_AUTHORIZE)
                ->execute($commandSubject);
        }

        $this->commandPool
            ->get(self::SIMPLE_ORDER_SUBSCRIPTION)
            ->execute($commandSubject);

        return $this->commandPool
            ->get(self::SIMPLE_ORDER_AUTHORIZE)
            ->execute($commandSubject);
    }
}
