/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Customer store credit(balance) application
 */
/*global define,alert*/
define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/action/get-totals',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/error-processor',
        'mage/storage',
        'Magento_Ui/js/model/messageList',
        'mage/translate',
        'Magento_Checkout/js/model/payment/method-list',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function (
        $,
        ko,
        getTotalsAction,
        quote,
        urlBuilder,
        paymentService,
        errorProcessor,
        storage,
        messageList,
        $t,
        paymentMethodList,
        fullScreenLoader
    ) {
        'use strict';
        return function () {
            var message = $t('Your store credit was successfully applied');
            messageList.clear();
            fullScreenLoader.startLoader();
            return storage.post(
                urlBuilder.createUrl('/carts/mine/balance/apply', {})
            ).done(
                function (response) {
                    if (response) {
                        var deferred = $.Deferred();

                        getTotalsAction([], deferred);

                        $.when(deferred).done(function() {
                            paymentService.setPaymentMethods(
                                paymentMethodList()
                            );
                        });
                        messageList.addSuccessMessage({'message': message});
                    }
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response);
                }
            ).always(
                function() {
                    fullScreenLoader.stopLoader();
                }
            );
        };
    }
);
