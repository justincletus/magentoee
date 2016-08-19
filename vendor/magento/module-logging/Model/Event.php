<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Logging\Model;

/**
 * Logging event model
 */
class Event extends \Magento\Framework\Model\AbstractModel
{
    const RESULT_SUCCESS = 'success';

    const RESULT_FAILURE = 'failure';

    /**
     * User model factory
     *
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->_userFactory = $userFactory;
    }

    /**
     * Constructor
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Magento\Logging\Model\ResourceModel\Event');
    }

    /**
     * Set some data automatically before saving model
     *
     * @return $this
     */
    public function beforeSave()
    {
        if (!$this->getId()) {
            $this->setStatus($this->getIsSuccess() ? self::RESULT_SUCCESS : self::RESULT_FAILURE);
            if (!$this->getUser() && ($id = $this->getUserId())) {
                $this->setUser($this->_userFactory->create()->load($id)->getUserName());
            }
            if (!$this->hasTime()) {
                $this->setTime(time());
            }
        }
        /**
         * Prepare short details data
         */
        $info = [];
        $info['general'] = $this->getInfo();
        if ($this->getAdditionalInfo()) {
            $info['additional'] = $this->getAdditionalInfo();
        }
        $this->setInfo(serialize($info));
        return parent::beforeSave();
    }

    /**
     * Define if current event has event changes
     *
     * @return bool
     */
    public function hasChanges()
    {
        if ($this->getId()) {
            return (bool)$this->getResource()->getEventChangeIds($this->getId());
        }
        return false;
    }
}
