<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCardAccount\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class Giftcardaccount extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $_dateFilter;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_fileFactory = $fileFactory;
        $this->_dateFilter = $dateFilter;
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_GiftCardAccount::customer_giftcardaccount');
    }

    /**
     * Load GCA from request
     *
     * @param string $idFieldName
     * @return \Magento\GiftCardAccount\Model\Giftcardaccount
     */
    protected function _initGca($idFieldName = 'id')
    {
        $id = (int)$this->getRequest()->getParam($idFieldName);
        $model = $this->_objectManager->create('Magento\GiftCardAccount\Model\Giftcardaccount');
        if ($id) {
            $model->load($id);
        }
        $this->_coreRegistry->register('current_giftcardaccount', $model);
        return $model;
    }
}
