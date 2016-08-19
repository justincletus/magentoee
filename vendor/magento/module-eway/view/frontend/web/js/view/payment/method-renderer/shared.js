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
        'Magento_Checkout/js/model/error-processor',
        'Magento_Eway/js/view/payment/form-builder',
        'mage/translate',
        'Magento_Ui/js/modal/alert',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function (
        $,
        Component,
        quote,
        customer,
        errorProcessor,
        formBuilder,
        $t,
        alert,
        fullScreenLoader
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Magento_Eway/payment/eway-shared-form',
                redirectAfterPlaceOrder: false,
                active: false,
                scriptLoaded: false,
                resultAction: {
                    'Cancel': function () {
                        $.post(this.getCancelOrderUrl())
                            .done(function() {
                                fullScreenLoader.stopLoader();
                            });
                    },
                    'Complete': function (transactionID) {
                        fullScreenLoader.startLoader();
                        this.sendUpdateRequest({'transaction_id': transactionID});
                    }
                },
                imports: {
                    onActiveChange: 'active'
                }
            },

            initObservable: function () {
                this._super()
                    .observe('active scriptLoaded');

                return this;
            },

            isActive: function () {
                var active = this.getCode() === this.isChecked();
                this.active(active);

                return active;
            },

            onActiveChange: function (isActive) {
                if (isActive && !this.scriptLoaded()) {
                    this.loadScript();
                }
            },

            loadScript: function () {
                var state = this.scriptLoaded;
                $('body').trigger('processStart');
                require([this.getUrl()], function () {
                    state(true);
                    $('body').trigger('processStop');
                });
            },

            getCode: function() {
                return 'eway';
            },

            getUrl: function () {
                return window.checkoutConfig.payment[this.getCode()].cryptUrl;
            },

            getEndpoint: function () {
                return window.checkoutConfig.payment[this.getCode()].endpoint;
            },

            resultCallback: function (result, transactionID) {
                if (!this.resultAction[result]) {
                    fullScreenLoader.stopLoader();
                    alert({
                        content: $t('Transaction has been declined, please, try again later.')
                    });
                    
                    return;
                }
                (this.resultAction[result].bind(this))(transactionID);
            },

            sendUpdateRequest: function (data) {
                formBuilder.build(
                    {
                        action: this.getPaymentUpdateUrl(),
                        fields: data
                    }
                ).submit();
            },

            getPaymentUpdateUrl: function () {
                return window.checkoutConfig.payment[this.getCode()].paymentUpdateUrl;
            },

            getCancelOrderUrl: function () {
                return window.checkoutConfig.payment[this.getCode()].orderCancelUrl;
            },

            getAccessCodeUrl: function () {
                return window.checkoutConfig.payment[this.getCode()].paymentGetAccessCodeUrl;
            },

            afterPlaceOrder: function () {
                var self = this;
                $.get(self.getAccessCodeUrl())
                    .done(
                    function(response) {
                        $('body').on('DOMNodeInserted', 'iframe', function(e) {
                            $(e.target).load(function() {
                                fullScreenLoader.stopLoader();
                            });
                        });
                        eCrypt.showModalPayment(
                            {
                                endpoint: this.getEndpoint(),
                                accessCode: response['access_code']
                            },
                            this.resultCallback.bind(this)
                        );
                    }.bind(this)
                ).fail(
                    function (response) {
                        var resultCallback = function() {
                            fullScreenLoader.stopLoader();
                            errorProcessor.process(response, self.messageContainer);
                        };
                        $.post(self.getCancelOrderUrl())
                            .done(resultCallback)
                            .fail(resultCallback);
                    }
                );
            }
        });
    }
);
