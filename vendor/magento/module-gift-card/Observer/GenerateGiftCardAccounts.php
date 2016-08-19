<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\GiftCard\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Permission tree retriever
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GenerateGiftCardAccounts implements ObserverInterface
{
    /**
     * Gift card data
     *
     * @var \Magento\GiftCard\Helper\Data
     */
    protected $giftCardData = null;

    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Url model
     *
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $urlModel;

    /**
     * Invoice Repository
     *
     * @var \Magento\Sales\Api\InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * Invoice items collection factory
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\Invoice\Item\CollectionFactory
     */
    protected $itemsFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $localeCurrency;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Sales\Model\ResourceModel\Order\Invoice\Item\CollectionFactory $itemsFactory
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Backend\Model\UrlInterface $urlModel
     * @param \Magento\GiftCard\Helper\Data $giftCardData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\Item\CollectionFactory $itemsFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Backend\Model\UrlInterface $urlModel,
        \Magento\GiftCard\Helper\Data $giftCardData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->storeManager = $storeManager;
        $this->localeCurrency = $localeCurrency;
        $this->itemsFactory = $itemsFactory;
        $this->transportBuilder = $transportBuilder;
        $this->invoiceRepository = $invoiceRepository;
        $this->messageManager = $messageManager;
        $this->urlModel = $urlModel;
        $this->giftCardData = $giftCardData;
        $this->scopeConfig = $scopeConfig;
        $this->eventManager = $eventManager;
    }

    /**
     * Generate gift card accounts after order save
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // sales_order_save_after

        $order = $observer->getEvent()->getOrder();
        $requiredStatus = $this->scopeConfig->getValue(
            \Magento\GiftCard\Model\Giftcard::XML_PATH_ORDER_ITEM_STATUS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $order->getStore()
        );
        $loadedInvoices = [];

        foreach ($order->getAllItems() as $item) {
            if ($item->getProductType() == \Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD) {
                $qty = 0;
                $options = $item->getProductOptions();

                switch ($requiredStatus) {
                    case \Magento\Sales\Model\Order\Item::STATUS_INVOICED:
                        $paidInvoiceItems = isset(
                            $options['giftcard_paid_invoice_items']
                        ) ? $options['giftcard_paid_invoice_items'] : [];
                        // find invoice for this order item
                        $invoiceItemCollection = $this->itemsFactory->create()->addFieldToFilter(
                            'order_item_id',
                            $item->getId()
                        );

                        foreach ($invoiceItemCollection as $invoiceItem) {
                            $invoiceId = $invoiceItem->getParentId();
                            if (isset($loadedInvoices[$invoiceId])) {
                                $invoice = $loadedInvoices[$invoiceId];
                            } else {
                                $invoice = $this->invoiceRepository->get($invoiceId);
                                $loadedInvoices[$invoiceId] = $invoice;
                            }
                            // check, if this order item has been paid
                            if ($invoice->getState() == \Magento\Sales\Model\Order\Invoice::STATE_PAID && !in_array(
                                $invoiceItem->getId(),
                                $paidInvoiceItems
                            )
                            ) {
                                $qty += $invoiceItem->getQty();
                                $paidInvoiceItems[] = $invoiceItem->getId();
                            }
                        }
                        $options['giftcard_paid_invoice_items'] = $paidInvoiceItems;
                        break;
                    default:
                        $qty = $item->getQtyOrdered();
                        if (isset($options['giftcard_created_codes'])) {
                            $qty -= count($options['giftcard_created_codes']);
                        }
                        break;
                }

                $hasFailedCodes = false;
                if ($qty > 0) {
                    $isRedeemable = 0;
                    $option = $item->getProductOptionByCode('giftcard_is_redeemable');
                    if ($option) {
                        $isRedeemable = $option;
                    }

                    $lifetime = 0;
                    $option = $item->getProductOptionByCode('giftcard_lifetime');
                    if ($option) {
                        $lifetime = $option;
                    }

                    $amount = $item->getBasePrice();
                    $websiteId = $this->storeManager->getStore($order->getStoreId())->getWebsiteId();

                    $data = new \Magento\Framework\DataObject();
                    $data->setWebsiteId(
                        $websiteId
                    )->setAmount(
                        $amount
                    )->setLifetime(
                        $lifetime
                    )->setIsRedeemable(
                        $isRedeemable
                    )->setOrderItem(
                        $item
                    );

                    $codes = isset($options['giftcard_created_codes']) ? $options['giftcard_created_codes'] : [];
                    $goodCodes = 0;
                    for ($i = 0; $i < $qty; $i++) {
                        try {
                            $code = new \Magento\Framework\DataObject();
                            $this->eventManager->dispatch(
                                'magento_giftcardaccount_create',
                                ['request' => $data, 'code' => $code]
                            );
                            $codes[] = $code->getCode();
                            $goodCodes++;
                        } catch (\Magento\Framework\Exception\LocalizedException $e) {
                            $hasFailedCodes = true;
                            $codes[] = null;
                        }
                    }
                    if ($goodCodes && $item->getProductOptionByCode('giftcard_recipient_email')) {
                        $sender = $item->getProductOptionByCode('giftcard_sender_name');
                        $senderName = $item->getProductOptionByCode('giftcard_sender_name');
                        $senderEmail = $item->getProductOptionByCode('giftcard_sender_email');
                        if ($senderEmail) {
                            $sender = "{$sender} <{$senderEmail}>";
                        }

                        $codeList = $this->giftCardData->getEmailGeneratedItemsBlock()->setCodes(
                            $codes
                        )->setArea(
                            \Magento\Framework\App\Area::AREA_FRONTEND
                        )->setIsRedeemable(
                            $isRedeemable
                        )->setStore(
                            $this->storeManager->getStore($order->getStoreId())
                        );
                        $balance = $this->localeCurrency->getCurrency(
                            $this->storeManager->getStore($order->getStoreId())->getBaseCurrencyCode()
                        )->toCurrency(
                            $amount
                        );

                        $templateData = [
                            'name' => $item->getProductOptionByCode('giftcard_recipient_name'),
                            'email' => $item->getProductOptionByCode('giftcard_recipient_email'),
                            'sender_name_with_email' => $sender,
                            'sender_name' => $senderName,
                            'gift_message' => $item->getProductOptionByCode('giftcard_message'),
                            'giftcards' => $codeList->toHtml(),
                            'balance' => $balance,
                            'is_multiple_codes' => 1 < $goodCodes,
                            'store' => $order->getStore(),
                            'store_name' => $order->getStore()->getName(),
                            'is_redeemable' => $isRedeemable,
                        ];

                        $transport = $this->transportBuilder->setTemplateIdentifier(
                            $item->getProductOptionByCode('giftcard_email_template')
                        )->setTemplateOptions(
                            [
                                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                                'store' => $item->getOrder()->getStoreId(),
                            ]
                        )->setTemplateVars(
                            $templateData
                        )->setFrom(
                            $this->scopeConfig->getValue(
                                \Magento\GiftCard\Model\Giftcard::XML_PATH_EMAIL_IDENTITY,
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                                $item->getOrder()->getStoreId()
                            )
                        )->addTo(
                            $item->getProductOptionByCode('giftcard_recipient_email'),
                            $item->getProductOptionByCode('giftcard_recipient_name')
                        )->getTransport();

                        $transport->sendMessage();
                        $options['email_sent'] = 1;
                    }
                    $options['giftcard_created_codes'] = $codes;
                    $item->setProductOptions($options);
                    $item->save();
                }
                if ($hasFailedCodes) {
                    $url = $this->urlModel->getUrl('adminhtml/giftcardaccount');
                    $message = __(
                        'Some gift card accounts were not created properly. You can create gift card accounts manually <a href="%1">here</a>.',
                        $url
                    );

                    $this->messageManager->addError($message);
                }
            }
        }

        return $this;
    }
}
