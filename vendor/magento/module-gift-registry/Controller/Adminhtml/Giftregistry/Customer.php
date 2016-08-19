<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistry\Controller\Adminhtml\Giftregistry;

use Magento\Framework\Exception\LocalizedException;
use Magento\GiftRegistry\Model\Entity;

/**
 * Gift Registry controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Customer extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * @param string $requestParam
     * @return Entity
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _initEntity($requestParam = 'id')
    {
        $entity = $this->_objectManager->create('Magento\GiftRegistry\Model\Entity');
        $entityId = $this->getRequest()->getParam($requestParam);
        if ($entityId) {
            $entity->load($entityId);
            if (!$entity->getId()) {
                throw new LocalizedException(__('Please correct the gift registry entity.'));
            }
        }
        $this->_coreRegistry->register('current_giftregistry_entity', $entity);
        return $entity;
    }

    /**
     * Check the permission
     *
     * @return bool
     * @codeCoverageIgnore
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_GiftRegistry::customer_magento_giftregistry');
    }
}
