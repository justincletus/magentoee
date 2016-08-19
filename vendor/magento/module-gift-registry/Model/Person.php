<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistry\Model;

/**
 * Entity registrants data model
 *
 * @method \Magento\GiftRegistry\Model\ResourceModel\Person _getResource()
 * @method \Magento\GiftRegistry\Model\ResourceModel\Person getResource()
 * @method \Magento\GiftRegistry\Model\Person setEntityId(int $value)
 * @method string getFirstname()
 * @method \Magento\GiftRegistry\Model\Person setFirstname(string $value)
 * @method string getLastname()
 * @method \Magento\GiftRegistry\Model\Person setLastname(string $value)
 * @method string getEmail()
 * @method \Magento\GiftRegistry\Model\Person setEmail(string $value)
 * @method string getRole()
 * @method \Magento\GiftRegistry\Model\Person setRole(string $value)
 * @method string getCustomValues()
 * @method \Magento\GiftRegistry\Model\Person setCustomValues(string $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Person extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var Entity
     */
    protected $entity;

    /**
     * Gift registry data
     *
     * @var \Magento\GiftRegistry\Helper\Data
     */
    protected $_giftRegistryData = null;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\GiftRegistry\Helper\Data $giftRegistryData
     * @param \Magento\GiftRegistry\Model\ResourceModel\Person $resource
     * @param Entity $entity
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\GiftRegistry\Helper\Data $giftRegistryData,
        \Magento\GiftRegistry\Model\ResourceModel\Person $resource,
        Entity $entity,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_giftRegistryData = $giftRegistryData;
        $this->entity = $entity;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\GiftRegistry\Model\ResourceModel\Person');
    }

    /**
     * Validate registrant attribute values
     *
     * @return array|bool
     */
    public function validate()
    {
        // not Checking entityId !!!
        $errors = [];

        if (!\Zend_Validate::is($this->getFirstname(), 'NotEmpty')) {
            $errors[] = __('Please enter the first name.');
        }

        if (!\Zend_Validate::is($this->getLastname(), 'NotEmpty')) {
            $errors[] = __('Please enter the last name.');
        }

        if (!\Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = __('Please enter a valid email address(for example, daniel@x.com).');
        }

        $customValues = $this->getCustom();
        $attributes = $this->entity->getRegistrantAttributes();

        $errorsCustom = $this->_giftRegistryData->validateCustomAttributes($customValues, $attributes);
        if ($errorsCustom !== true) {
            $errors = empty($errors) ? $errorsCustom : array_merge($errors, $errorsCustom);
        }
        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Unpack "custom" value array
     *
     * @return $this
     */
    public function unserialiseCustom()
    {
        if (is_string($this->getCustomValues())) {
            $this->setCustom(unserialize($this->getCustomValues()));
        }
        return $this;
    }
}
