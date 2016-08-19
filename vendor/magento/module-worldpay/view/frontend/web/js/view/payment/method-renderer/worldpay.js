/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'Magento_Worldpay/js/form-builder',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function ($, Component, quote, customer, urlBuilder, storage, formBuilder, errorProcessor, fullScreenLoader) {
        return Component.extend({
            defaults: {
                template: 'Magento_Worldpay/payment/worldpay-form',
                redirectAfterPlaceOrder: false
            },
            /** Open window with  */
            showAcceptanceWindow: function(data, event) {
                window.open(
                    $(event.target).attr('href'),
                    'olcwhatispaypal',
                    'toolbar=no, location=no,' +
                    ' directories=no, status=no,' +
                    ' menubar=no, scrollbars=yes,' +
                    ' resizable=yes, ,left=0,' +
                    ' top=0, width=400, height=350'
                );
                return false;
            },
            afterPlaceOrder: function () {
                var self = this;
                $.get(self.getUrl())
                    .done(function(response) {
                        formBuilder.build(response).submit();
                    }).fail(function (response) {
                        errorProcessor.process(response, self.messageContainer);
                        fullScreenLoader.stopLoader();
                    });
            },
            getCode: function () {
                return 'worldpay';
            },
            getUrl: function () {
                return window.checkoutConfig.payment[this.getCode()].transactionDataUrl;
            }
        });
    }
);
