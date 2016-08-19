<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerSegment\Model\Segment\Condition;

/**
 * Segment condition for sales rules
 */
class Segment extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * @var string
     */
    protected $_inputType = 'multiselect';

    /**
     * Adminhtml data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_adminhtmlData;

    /**
     * Customer segment data
     *
     * @var \Magento\CustomerSegment\Helper\Data
     */
    protected $_customerSegmentData;

    /**
     * @var \Magento\CustomerSegment\Model\Customer
     */
    protected $_customer;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\CustomerSegment\Model\Customer $customer
     * @param \Magento\CustomerSegment\Helper\Data $customerSegmentData
     * @param \Magento\Backend\Helper\Data $adminhtmlData
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\CustomerSegment\Model\Customer $customer,
        \Magento\CustomerSegment\Helper\Data $customerSegmentData,
        \Magento\Backend\Helper\Data $adminhtmlData,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->_customer = $customer;
        $this->_customerSegmentData = $customerSegmentData;
        $this->_adminhtmlData = $adminhtmlData;
        parent::__construct($context, $data);
    }

    /**
     * Default operator input by type map getter
     *
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            $this->_defaultOperatorInputByType = ['multiselect' => ['==', '!=', '()', '!()']];
            $this->_arrayInputTypes = ['multiselect'];
        }
        return $this->_defaultOperatorInputByType;
    }

    /**
     * Render chooser trigger
     *
     * @return string
     */
    public function getValueAfterElementHtml()
    {
        return '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="' .
            $this->_assetRepo->getUrl(
                'images/rule_chooser_trigger.gif'
            ) . '" alt="" class="v-middle rule-chooser-trigger" title="' . __(
                'Open Chooser'
            ) . '" /></a>';
    }

    /**
     * Value element type getter
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Chooser URL getter
     *
     * @return string
     */
    public function getValueElementChooserUrl()
    {
        return $this->_adminhtmlData->getUrl(
            'customersegment/index/chooserGrid',
            ['value_element_id' => $this->_valueElement->getId(), 'form' => $this->getJsFormObject()]
        );
    }

    /**
     * Enable chooser selection button
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getExplicitApply()
    {
        return true;
    }

    /**
     * Render element HTML
     *
     * @return string
     */
    public function asHtml()
    {
        $this->_valueElement = $this->getValueElement();
        return $this->getTypeElementHtml() . __(
            'If Customer Segment %1 %2',
            $this->getOperatorElementHtml(),
            $this->_valueElement->getHtml()
        ) .
            $this->getRemoveLinkHtml() .
            '<div class="rule-chooser" url="' .
            $this->getValueElementChooserUrl() .
            '"></div>';
    }

    /**
     * Specify allowed comparison operators
     *
     * @return \Magento\CustomerSegment\Model\Segment\Condition\Segment
     */
    public function loadOperatorOptions()
    {
        parent::loadOperatorOptions();
        $this->setOperatorOption(
            [
                '==' => __('matches'),
                '!=' => __('does not match'),
                '()' => __('is one of'),
                '!()' => __('is not one of'),
            ]
        );
        return $this;
    }

    /**
     * Present selected values as array
     *
     * @return array
     */
    public function getValueParsed()
    {
        $value = $this->getData('value');
        $value = array_map('trim', explode(',', $value));
        return $value;
    }

    /**
     * Validate if qoute customer is assigned to role segments
     *
     * @param   \Magento\Quote\Model\Quote\Address|\Magento\Framework\Model\AbstractModel $object
     * @return  bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        if (!$this->_customerSegmentData->isEnabled()) {
            return false;
        }
        if ($model->getQuote()) {
            $customer = $model->getQuote()->getCustomer();
        }
        if (!isset($customer)) {
            return false;
        }

        $quoteWebsiteId = $model->getQuote()->getStore()->getWebsite()->getId();
        $segments = [];
        if (!$customer->getId()) {
            $visitorSegmentIds = $this->_customerSession->getCustomerSegmentIds();
            if (is_array($visitorSegmentIds) && isset($visitorSegmentIds[$quoteWebsiteId])) {
                $segments = $visitorSegmentIds[$quoteWebsiteId];
            }
        } else {
            $segments = $this->_customer->getCustomerSegmentIdsForWebsite($customer->getId(), $quoteWebsiteId);
        }

        return $this->validateAttribute($segments);
    }
}
