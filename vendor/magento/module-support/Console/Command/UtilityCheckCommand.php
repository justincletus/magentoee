<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Command for checking list of required utilities
 */
class UtilityCheckCommand extends AbstractBackupCommand
{
    /**
     * Name of input argument
     */
    const INPUT_KEY_UTILITIES = 'utilities';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('support:utility:check')
            ->setDescription('Check required backup utilities')
            ->setDefinition([
                new InputOption(self::INPUT_KEY_UTILITIES, 'u', InputOption::VALUE_NONE, 'Show list of utilities'),
            ]);
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->shellHelper->initPaths();

            if ($input->getOption(self::INPUT_KEY_UTILITIES)) {
                $output->writeln('Utilities list:');
                foreach ($this->shellHelper->getUtilities() as $name => $path) {
                    $output->writeln(sprintf('%s => %s', $name, $path));
                }
            }
        } catch (\Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());
        }
    }
}
