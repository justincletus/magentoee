<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ResourceConnections\Console\Command;

use Magento\ResourceConnections\App\DeploymentConfig;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\App\DeploymentConfig\Writer as ConfigWriter;
use Magento\Framework\App\DeploymentConfig\Reader as ConfigReader;

/**
 * @codeCoverageIgnore
 */
class AddSlave extends \Symfony\Component\Console\Command\Command
{

    /**
     * DB host name
     */
    const HOST = 'host';

    /**
     * Checkout DB name
     */
    const DB_NAME = 'dbname';

    /**
     * Checkout DB user
     */
    const USER_NAME = 'username';

    /**
     * Checkout DB user password
     */
    const PASSWORD = 'password';

    /**
     * New connection name
     */
    const CONNECTION = 'connection';

    /**
     * Linked resource name
     */
    const RESOURCE = 'resource';

    /**
     * @var ConfigWriter
     */
    private $configWriter;

    /**
     * @var ConfigReader
     */
    private $configReader;


    /**
     * Constructor.
     *
     * @param string|null $name The name of the command; passing null means it must be set in configure()
     * @param ConfigWriter $configWriter
     * @param ConfigReader $configReader
     * @throws \LogicException When the command name is empty
     *
     */
    public function __construct(
        ConfigWriter $configWriter,
        ConfigReader $configReader,
        $name = null
    ) {
        $this->configWriter = $configWriter;
        $this->configReader = $configReader;
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCommandName()
    {
        return 'setup:db-schema:add-slave';
    }

    /**
     * {@inheritdoc}
     */
    protected function getCommandDescription()
    {
        return 'Move checkout quote related tables to a separate DB server';
    }

    /**
     * {@inheritdoc}
     */
    protected function getCommandDefinition()
    {
        return [
            new InputOption(
                self::HOST,
                null,
                InputOption::VALUE_REQUIRED,
                'Slave DB Server host',
                'localhost'
            ),
            new InputOption(
                self::DB_NAME,
                null,
                InputOption::VALUE_REQUIRED,
                'Slave Database Name'
            ),
            new InputOption(
                self::USER_NAME,
                null,
                InputOption::VALUE_REQUIRED,
                'Slave DB user name',
                'root'
            ),
            new InputOption(
                self::PASSWORD,
                null,
                InputOption::VALUE_OPTIONAL,
                'Slave DB user password'
            ),
            new InputOption(
                self::CONNECTION,
                null,
                InputOption::VALUE_OPTIONAL,
                'Slave connection name',
                'default'
            ),
            new InputOption(
                self::RESOURCE,
                null,
                InputOption::VALUE_OPTIONAL,
                'Slave Resource name',
                'default'
            )
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName($this->getCommandName())
            ->setDescription($this->getCommandDescription())
            ->setDefinition($this->getCommandDefinition());
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->generateConfig($input);
        $this->configWriter->saveConfig([ConfigFilePool::APP_ENV => $config], true);
        $output->writeln('Slave has been added successfully!');
    }

    /**
     * Generate environment configuration
     *
     * @param InputInterface $input
     * @return array
     * @throws \Exception
     */
    protected function generateConfig(InputInterface $input)
    {
        $config = $this->configReader->load(ConfigFilePool::APP_ENV);

        if (!isset($config['db'][DeploymentConfig::SLAVE_CONNECTION][$input->getOption(self::CONNECTION)])) {
            $config['db'][DeploymentConfig::SLAVE_CONNECTION][$input->getOption(self::CONNECTION)] = [
                'host' => $input->getOption(self::HOST),
                'dbname' => $input->getOption(self::DB_NAME),
                'username' => $input->getOption(self::USER_NAME),
                'password' => $input->getOption(self::PASSWORD),
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => 'SET NAMES utf8;',
                'active' => '1',
            ];
        } else {
            throw new \InvalidArgumentException('Connection with same name already exists');
        }

        if (!isset($config['resource'][$input->getOption(self::RESOURCE)])) {
            $config['resource'][$input->getOption(self::RESOURCE)] = [
                'connection' => $input->getOption(self::CONNECTION)
            ];
        }
        return $config;
    }
}
