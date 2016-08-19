<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MultipleWishlist\Controller\Index;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action;

class Add extends \Magento\Wishlist\Controller\Index\Add
{
    /**
     * @var \Magento\MultipleWishlist\Model\WishlistEditor
     */
    protected $wishlistEditor;

    /**
     * @param Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\MultipleWishlist\Model\WishlistEditor $wishlistEditor
     */
    public function __construct(
        Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        ProductRepositoryInterface $productRepository,
        \Magento\MultipleWishlist\Model\WishlistEditor $wishlistEditor
    ) {
        $this->wishlistEditor = $wishlistEditor;
        parent::__construct($context, $customerSession, $wishlistProvider, $productRepository);
    }

    /**
     * Add item to wishlist
     * Create new wishlist if wishlist params (name, visibility) are provided
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $customerId = $this->_customerSession->getCustomerId();
        $name = $this->getRequest()->getParam('name');
        $visibility = $this->getRequest()->getParam('visibility', 0) === 'on' ? 1 : 0;
        if ($name !== null) {
            try {
                $wishlist = $this->wishlistEditor->edit($customerId, $name, $visibility);
                $this->messageManager->addSuccess(
                    __(
                        'Wish list "%1" was saved.',
                        $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($wishlist->getName())
                    )
                );
                $this->getRequest()->setParam('wishlist_id', $wishlist->getId());
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t create the wish list right now.'));
            }
        }
        return parent::execute();
    }
}
