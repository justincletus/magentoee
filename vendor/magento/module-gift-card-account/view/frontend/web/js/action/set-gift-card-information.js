/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        '../model/payment/gift-card-messages',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/action/get-totals',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-list',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function (
        $,
        quote,
        urlBuilder,
        storage,
        messageList,
        errorProcessor,
        customer,
        getTotalsAction,
        paymentService,
        paymentMethodList,
        fullScreenLoader
    ) {
        'use strict';

        return function (giftCardCode) {
            var serviceUrl,
                payload,
                message = 'Gift Card ' + giftCardCode + ' was added.';
            /**
             * Checkout for guest and registered customer.
             */
            if (!customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/carts/guest-carts/:cartId/giftCards', {
                    cartId: quote.getQuoteId()
                });
                payload = {
                    cartId: quote.getQuoteId(),
                    giftCardAccountData: {gift_cards: giftCardCode}
                };
            } else {
                serviceUrl = urlBuilder.createUrl('/carts/mine/giftCards', {});
                payload = {
                    cartId: quote.getQuoteId(),
                    giftCardAccountData: {gift_cards: giftCardCode}
                };
            }
            messageList.clear();
            fullScreenLoader.startLoader();
            storage.post(
                serviceUrl, JSON.stringify(payload)
            ).done(
                function (response) {
                    var deferred = $.Deferred();
                    if (response) {
                        getTotalsAction([], deferred);
                        $.when(deferred).done(function () {
                            paymentService.setPaymentMethods(
                                paymentMethodList()
                            );
                        });
                        messageList.addSuccessMessage({'message': message});
                    }
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response, messageList);
                }
            ).always(
                function() {
                    fullScreenLoader.stopLoader();
                }
            );
        };
    }
);
