<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MysqlMq\Model;

use Magento\Framework\MessageQueue\PublisherInterface;

/**
 * Test for MySQL publisher class.
 *
 * @magentoDbIsolation disabled
 */
class PublisherConsumerTest extends \PHPUnit_Framework_TestCase
{
    const MAX_NUMBER_OF_TRIALS = 3;

    /**
     * @var PublisherInterface
     */
    protected $publisher;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $objectManagerConfiguration = [
            'Magento\Framework\MessageQueue\Config\Reader' => [
                'arguments' => [
                    'fileResolver' => ['instance' => 'Magento\MysqlMq\Config\Reader\FileResolver'],
                ],
            ],
        ];
        $this->objectManager->configure($objectManagerConfiguration);
        /** @var \Magento\Framework\MessageQueue\Config\Data $queueConfig */
        $queueConfig = $this->objectManager->get('Magento\Framework\MessageQueue\Config\Data');
        $queueConfig->reset();
        $this->publisher = $this->objectManager->create('Magento\Framework\MessageQueue\PublisherInterface');
    }

    protected function tearDown()
    {
        $this->consumeMessages('demoConsumerQueueOne', PHP_INT_MAX);
        $this->consumeMessages('demoConsumerQueueTwo', PHP_INT_MAX);
        $this->consumeMessages('demoConsumerQueueThree', PHP_INT_MAX);
        $this->consumeMessages('demoConsumerQueueFour', PHP_INT_MAX);
        $this->consumeMessages('demoConsumerQueueFive', PHP_INT_MAX);
        $this->consumeMessages('demoConsumerQueueOneWithException', PHP_INT_MAX);

        $objectManagerConfiguration = [
            'Magento\Framework\MessageQueue\Config\Reader' => [
                'arguments' => [
                    'fileResolver' => ['instance' => 'Magento\Framework\Config\FileResolverInterface'],
                ],
            ],
        ];
        $this->objectManager->configure($objectManagerConfiguration);
        /** @var \Magento\Framework\MessageQueue\Config\Data $queueConfig */
        $queueConfig = $this->objectManager->get('Magento\Framework\MessageQueue\Config\Data');
        $queueConfig->reset();
    }

    /**
     * @magentoDataFixture Magento/MysqlMq/_files/queues.php
     */
    public function testPublishConsumeFlow()
    {
        /** @var \Magento\MysqlMq\Model\DataObjectFactory $objectFactory */
        $objectFactory = $this->objectManager->create('Magento\MysqlMq\Model\DataObjectFactory');
        /** @var \Magento\MysqlMq\Model\DataObject $object */
        $object = $objectFactory->create();
        for ($i = 0; $i < 10; $i++) {
            $object->setName('Object name ' . $i)->setEntityId($i);
            $this->publisher->publish('demo.object.created', $object);
        }
        for ($i = 0; $i < 5; $i++) {
            $object->setName('Object name ' . $i)->setEntityId($i);
            $this->publisher->publish('demo.object.updated', $object);
        }
        for ($i = 0; $i < 3; $i++) {
            $object->setName('Object name ' . $i)->setEntityId($i);
            $this->publisher->publish('demo.object.custom.created', $object);
        }

        $outputPattern = '/(Processed \d+\s)/';
        /** There are total of 10 messages in the first queue, total expected consumption is 7, 3 then 0 */
        $this->consumeMessages('demoConsumerQueueOne', 7, 7, $outputPattern);
        /** Consumer all messages which left in this queue */
        $this->consumeMessages('demoConsumerQueueOne', PHP_INT_MAX, 3, $outputPattern);
        $this->consumeMessages('demoConsumerQueueOne', 7, 0, $outputPattern);

        /** Verify that messages were added correctly to second queue for update and create topics */
        $this->consumeMessages('demoConsumerQueueTwo', 20, 15, $outputPattern);

        /** Verify that messages were NOT added to fourth queue */
        $this->consumeMessages('demoConsumerQueueFour', 11, 0, $outputPattern);

        /** Verify that messages were added correctly by '*' pattern in bind config to third queue */
        $this->consumeMessages('demoConsumerQueueThree', 20, 15, $outputPattern);

        /** Verify that messages were added correctly by '#' pattern in bind config to fifth queue */
        $this->consumeMessages('demoConsumerQueueFive', 20, 18, $outputPattern);
    }

    /**
     * @magentoDataFixture Magento/MysqlMq/_files/queues.php
     */
    public function testPublishAndConsumeWithFailedJobs()
    {
        /** @var \Magento\MysqlMq\Model\DataObjectFactory $objectFactory */
        $objectFactory = $this->objectManager->create('Magento\MysqlMq\Model\DataObjectFactory');
        /** @var \Magento\MysqlMq\Model\DataObject $object */
        /** Try consume messages for MAX_NUMBER_OF_TRIALS and then consumer them without exception */
        $object = $objectFactory->create();
        for ($i = 0; $i < 5; $i++) {
            $object->setName('Object name ' . $i)->setEntityId($i);
            $this->publisher->publish('demo.object.created', $object);
        }
        $outputPattern = '/(Processed \d+\s)/';
        for ($i = 0; $i < self::MAX_NUMBER_OF_TRIALS; $i++) {
            $this->consumeMessages('demoConsumerQueueOneWithException', PHP_INT_MAX, 0, $outputPattern);
        }
        $this->consumeMessages('demoConsumerQueueOne', PHP_INT_MAX, 0, $outputPattern);

        /** Try consume messages for MAX_NUMBER_OF_TRIALS+1 and then consumer them without exception */
        for ($i = 0; $i < 5; $i++) {
            $object->setName('Object name ' . $i)->setEntityId($i);
            $this->publisher->publish('demo.object.created', $object);
        }
        /** Try consume messages for MAX_NUMBER_OF_TRIALS and then consumer them without exception */
        for ($i = 0; $i < self::MAX_NUMBER_OF_TRIALS + 1; $i++) {
            $this->consumeMessages('demoConsumerQueueOneWithException', PHP_INT_MAX, 0, $outputPattern);
        }
        /** Make sure that messages are not accessible anymore after number of trials is exceeded */
        $this->consumeMessages('demoConsumerQueueOne', PHP_INT_MAX, 0, $outputPattern);
    }

    /**
     * @magentoDataFixture Magento/MysqlMq/_files/queues.php
     */
    public function testPublishAndConsumeSchemaDefinedByMethod()
    {
        /** @var \Magento\MysqlMq\Model\DataObjectFactory $objectFactory */
        $objectFactory = $this->objectManager->create('Magento\MysqlMq\Model\DataObjectFactory');
        /** @var \Magento\MysqlMq\Model\DataObject $object */
        $object = $objectFactory->create();
        $id = 33;
        $object->setName('Object name ' . $id)->setEntityId($id);
        $requiredStringParam = 'Required value';
        $optionalIntParam = 44;
        $this->publisher->publish('test.schema.defined.by.method', [$object, $requiredStringParam, $optionalIntParam]);

        $outputPattern = "/Processed '{$object->getEntityId()}'; "
            . "Required param '{$requiredStringParam}'; Optional param '{$optionalIntParam}'/";
        $this->consumeMessages('delayedOperationConsumer', PHP_INT_MAX, 1, $outputPattern);
    }

    /**
     * Make sure that consumers consume correct number of messages.
     *
     * @param string $consumerName
     * @param int|null $messagesToProcess
     * @param int|null $expectedNumberOfProcessedMessages
     * @param string|null $outputPattern
     */
    protected function consumeMessages(
        $consumerName,
        $messagesToProcess,
        $expectedNumberOfProcessedMessages = null,
        $outputPattern = null
    ) {
        /** @var \Magento\Framework\MessageQueue\ConsumerFactory $consumerFactory */
        $consumerFactory = $this->objectManager->create('Magento\Framework\MessageQueue\ConsumerFactory');
        $consumer = $consumerFactory->get($consumerName);
        ob_start();
        $consumer->process($messagesToProcess);
        $consumersOutput = ob_get_contents();
        ob_end_clean();
        if ($outputPattern) {
            $this->assertEquals(
                $expectedNumberOfProcessedMessages,
                preg_match_all($outputPattern, $consumersOutput)
            );
        }
    }
}
