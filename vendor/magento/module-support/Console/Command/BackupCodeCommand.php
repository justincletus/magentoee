<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Support\Helper\Shell as ShellHelper;

/**
 * Command for displaying current index mode for indexers.
 */
class BackupCodeCommand extends AbstractBackupDumpCommand
{
    /**
     * List paths for code backup
     *
     * @var array
     */
    protected $backupList = [
        'app',
        'bin',
        'composer.*',
        'dev',
        '*.php',
        'lib',
        'pub/*.php',
        'pub/errors',
        'setup',
        'vendor'
    ];

    /**
     * List paths for logs backup
     *
     * @var array
     */
    protected $backupLogsList = [
        'var/log',
        'var/report'
    ];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('support:backup:code')
            ->setDescription('Create Code backup')
            ->setDefinition($this->getInputList());
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $filePath    = $this->getOutputPath($input) . DIRECTORY_SEPARATOR . $this->getBackupName($input);
            $includeLogs = (bool) $input->getOption(self::INPUT_KEY_LOGS);

            $backupCodeCommand = $this->getBackupCodeCommand($filePath);
            $output->writeln($backupCodeCommand);
            $output->writeln($this->shellHelper->execute($backupCodeCommand));

            if ($includeLogs) {
                $backupLogsCommand = $this->getBackupLogsCommand($filePath);
                $output->writeln($backupLogsCommand);
                $output->writeln($this->shellHelper->execute($backupLogsCommand));
            }

            $output->writeln('Code dump was created successfully');
        } catch (\Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());
        }
    }

    /**
     * Get console command for code backup
     *
     * @param string $filePath
     * @return string
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    protected function getBackupCodeCommand($filePath)
    {
        $fileExtension = $this->backupConfig->getBackupFileExtension('code');

        $command = sprintf(
            '%s -n 15 %s -czhf %s %s',
            $this->shellHelper->getUtility(ShellHelper::UTILITY_NICE),
            $this->shellHelper->getUtility(ShellHelper::UTILITY_TAR),
            $filePath . '.' . ($fileExtension ?: 'tar.gz'),
            implode(' ', $this->backupList)
        );

        return $command;
    }

    /**
     * Get console command for logs backup
     *
     * @param string $filePath
     * @return string
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    protected function getBackupLogsCommand($filePath)
    {
        $command = sprintf(
            '%s -n 15 %s -czhf %s %s',
            $this->shellHelper->getUtility(ShellHelper::UTILITY_NICE),
            $this->shellHelper->getUtility(ShellHelper::UTILITY_TAR),
            $filePath . '.logs.tar.gz',
            implode(' ', $this->backupLogsList)
        );

        return $command;
    }
}
