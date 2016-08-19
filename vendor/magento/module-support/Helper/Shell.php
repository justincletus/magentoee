<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Helper;

use \Magento\Framework\Exception\LocalizedException;

/**
 * Helper for work with shell
 */
class Shell extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * File that contains paths to shell commands
     */
    const PATHS_FILE        = 'Paths.php';

    /**#@+
     * Shell commands
     */
    const UTILITY_NICE      = 'nice';
    const UTILITY_TAR       = 'tar';
    const UTILITY_MYSQLDUMP = 'mysqldump';
    const UTILITY_GZIP      = 'gzip';
    const UTILITY_LSOF      = 'lsof';
    const UTILITY_PHP       = 'php';
    const UTILITY_SED       = 'sed';
    const UTILITY_BASH      = 'bash';
    const UTILITY_MYSQL     = 'mysql';
    /**#@-*/

    const XML_OUTPUT_PATH   = 'support/output_path';

    /**
     * @var \Magento\Framework\ShellInterface
     */
    protected $shell;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    protected $directory;

    /**
     * @var array
     */
    protected $utilities = [];

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ShellInterface $shell
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ShellInterface $shell,
        \Magento\Framework\Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->shell = $shell;
        $this->directory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::ROOT);
    }

    /**
     * Wrapper for execute
     *
     * @param string $command
     * @param array $arguments
     * @return string
     */
    public function execute($command, array $arguments = [])
    {
        return $this->shell->execute($command, $arguments);
    }

    /**
     * Get paths file path
     *
     * @return string
     */
    public function getPathsFileName()
    {
        return $this->getOutputPath() . '/' . self::PATHS_FILE;
    }

    /**
     * Collect paths for required console utilities
     *
     * @param bool $force
     * @return void
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function initPaths($force = false)
    {
        if (!empty($this->utilities)) {
            return;
        }

        $pathsFile = $this->getPathsFileName();
        if (!$force && $this->directory->isExist($pathsFile)) {
            $this->utilities = include($pathsFile);
            return;
        }

        $list = [
            self::UTILITY_GZIP,
            self::UTILITY_LSOF,
            self::UTILITY_MYSQLDUMP,
            self::UTILITY_NICE,
            self::UTILITY_PHP,
            self::UTILITY_TAR,
            self::UTILITY_SED,
            self::UTILITY_BASH,
            self::UTILITY_MYSQL
        ];
        foreach ($list as $name) {
            try {
                $this->utilities[$name] = $this->execute('which ' . $name);
            } catch (LocalizedException $e) {
                throw new \Magento\Framework\Exception\NotFoundException(__('Utility %1 not found', $name));
            }
        }
    }

    /**
     * Get utility path by utility name
     *
     * @param string $name
     * @return mixed
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getUtility($name)
    {
        $this->initPaths();

        if (!isset($this->utilities[$name])) {
            throw new \Magento\Framework\Exception\NotFoundException(__('Unknown utility: %1', $name));
        }

        return $this->utilities[$name];
    }

    /**
     * @return array
     */
    public function getUtilities()
    {
        return $this->utilities;
    }

    /**
     * Get output path
     *
     * @return string
     */
    public function getOutputPath()
    {
        $path = $this->scopeConfig->getValue(self::XML_OUTPUT_PATH);
        if (!$this->directory->isExist($path)) {
            $this->directory->create($path);
        }
        return $path;
    }

    /**
     * Get Item Path
     *
     * @param string $itemName
     * @return string
     */
    public function getFilePath($itemName)
    {
        return $this->getOutputPath() . $itemName;
    }

    /**
     * Get file size
     *
     * @param string $itemName
     * @return int
     */
    public function getFileSize($itemName)
    {
        return filesize($this->getFilePath($itemName));
    }

    /**
     * Check file is locked
     *
     * @param string $filePath
     * @return bool
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function isFileLocked($filePath)
    {
        return (bool)exec($this->getUtility(self::UTILITY_LSOF) . ' ' . $filePath);
    }

    /**
     * Check if php can run bash script
     *
     * @return bool
     */
    public function isExecEnabled()
    {
        $disabledFunctions = explode(',', ini_get('disable_functions'));

        return function_exists('exec') && !in_array('exec', $disabledFunctions);
    }
}
