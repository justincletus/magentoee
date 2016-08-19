<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MultipleWishlist\Controller\Index;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\Action\Context;
use Magento\MultipleWishlist\Controller\IndexInterface;
use Magento\Wishlist\Controller\WishlistProviderInterface;

class Deletewishlist extends \Magento\MultipleWishlist\Controller\AbstractIndex
{
    /**
     * @var \Magento\Wishlist\Controller\WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @param Context $context
     * @param WishlistProviderInterface $wishlistProvider
     */
    public function __construct(Context $context, WishlistProviderInterface $wishlistProvider)
    {
        $this->wishlistProvider = $wishlistProvider;
        parent::__construct($context);
    }

    /**
     * Delete wishlist by id
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws NotFoundException
     */
    public function execute()
    {
        try {
            $wishlist = $this->wishlistProvider->getWishlist();
            if (!$wishlist) {
                throw new NotFoundException(__('Page not found.'));
            }
            if ($this->_objectManager->get('Magento\MultipleWishlist\Helper\Data')->isWishlistDefault($wishlist)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('You can\'t delete the default wish list.')
                );
            }
            $wishlist->delete();
            $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
            $this->messageManager->addSuccess(
                __(
                    'Wish List "%1" has been deleted.',
                    $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($wishlist->getName())
                )
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $message = __('We can\'t delete the wish list right now.');
            $this->messageManager->addException($e, $message);
        }
    }
}
