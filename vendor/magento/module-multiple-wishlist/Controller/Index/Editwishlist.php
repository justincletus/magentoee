<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MultipleWishlist\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\MultipleWishlist\Controller\IndexInterface;
use Magento\Framework\Controller\ResultFactory;

class Editwishlist extends \Magento\MultipleWishlist\Controller\AbstractIndex
{
    /**
     * @var \Magento\MultipleWishlist\Model\WishlistEditor
     */
    protected $wishlistEditor;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param Context $context
     * @param \Magento\MultipleWishlist\Model\WishlistEditor $wishlistEditor
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        Context $context,
        \Magento\MultipleWishlist\Model\WishlistEditor $wishlistEditor,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->wishlistEditor = $wishlistEditor;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Edit wishlist properties
     *
     * @return \Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $customerId = $this->customerSession->getCustomerId();
        $wishlistName = $this->getRequest()->getParam('name');
        $visibility = $this->getRequest()->getParam('visibility', 0) === 'on' ? 1 : 0;
        $wishlistId = $this->getRequest()->getParam('wishlist_id');
        $wishlist = null;
        try {
            $wishlist = $this->wishlistEditor->edit($customerId, $wishlistName, $visibility, $wishlistId);

            $this->messageManager->addSuccess(
                __(
                    'Wish list "%1" was saved.',
                    $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($wishlist->getName())
                )
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t create the wish list right now.'));
        }

        if (!$wishlist || !$wishlist->getId()) {
            $this->messageManager->addError('Could not create wishlist');
        }

        if ($this->getRequest()->isAjax()) {
            if ($wishlist && $wishlist->getId()) {
                $params = ['wishlist_id' => $wishlist->getId()];
            } else {
                $params = ['redirect' => $this->_url->getUrl('*/*')];
            }
            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($params);
            return $resultJson;
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$wishlist || !$wishlist->getId()) {
            $resultRedirect->setPath('*/*');
        } else {
            $resultRedirect->setPath('wishlist/index/index', ['wishlist_id' => $wishlist->getId()]);
        }
        return $resultRedirect;
    }
}
