<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Model\ResourceModel;

/**
 * Base resource model for reports
 */
class Report extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Support\Model\Report\Config
     */
    protected $reportConfig;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Support\Model\Report\Config $reportConfig
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Support\Model\Report\Config $reportConfig,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $resourcePrefix = null
    ) {
        $this->reportConfig = $reportConfig;
        $this->objectManager = $objectManager;
        parent::__construct($context, $resourcePrefix);
    }

    /**
     * Set main table name and id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('support_report', 'report_id');
    }

    /**
     * Prepare system report data to be saved
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $groups = $object->getReportGroups();
        $flags = $this->reportConfig->getSectionNamesByGroup($groups);
        $object->setReportFlags(implode(',', $flags));
        $object->setReportGroups(implode(',', $groups));
        $object->setReportData(serialize($object->getReportData()));

        parent::_afterSave($object);

        return $this;
    }

    /**
     * Unserialize system report data
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        try {
            $data = unserialize($object->getReportData());
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('There was an error while loading system report data.')
            );
        }
        $object->setReportData($data);

        parent::_afterLoad($object);

        return $this;
    }
}
