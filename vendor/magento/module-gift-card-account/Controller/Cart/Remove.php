<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftCardAccount\Controller\Cart;

class Remove extends \Magento\Checkout\Controller\Cart
{
    /**
     * @return \Magento\Framework\Controller\Result\Redirect|void
     */
    public function execute()
    {
        $code = $this->getRequest()->getParam('code');
        if ($code) {
            try {
                $this->_objectManager->create(
                    'Magento\GiftCardAccount\Model\Giftcardaccount'
                )->loadByCode(
                    $code
                )->removeFromCart();
                $this->messageManager->addSuccess(
                    __(
                        'Gift Card "%1" was removed.',
                        $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($code)
                    )
                );
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('You can\'t remove this gift card.'));
            }
            return $this->_goBack();
        } else {
            $this->_forward('noroute');
        }
    }
}
