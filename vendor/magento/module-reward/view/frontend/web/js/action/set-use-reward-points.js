/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'jquery',
        'Magento_Checkout/js/action/get-totals',
        'Magento_Checkout/js/model/url-builder',
        'Magento_Checkout/js/model/payment-service',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Ui/js/model/messageList',
        'mage/translate',
        'Magento_Checkout/js/model/payment/method-list',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function (
        $,
        getTotalsAction,
        urlBuilder,
        paymentService,
        storage,
        errorProcessor,
        messageList,
        $t,
        paymentMethodList,
        fullScreenLoader
    ) {
        'use strict';
        return function () {
            var message = $t('Your reward point was successfully applied');
            messageList.clear();
            fullScreenLoader.startLoader();
            storage.post(
                urlBuilder.createUrl('/reward/mine/use-reward', {}), {}
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
                    }
                    messageList.addSuccessMessage({'message': message});
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
