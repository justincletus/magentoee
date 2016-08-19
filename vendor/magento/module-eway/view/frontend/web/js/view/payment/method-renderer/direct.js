/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'jquery',
        'Magento_Payment/js/view/payment/cc-form',
        'Magento_Checkout/js/model/payment/additional-validators'
    ],
    function ($, ccFormComponent, additionalValidators) {
        'use strict';

        return ccFormComponent.extend({
            defaults: {
                template: 'Magento_Eway/payment/eway-direct-form',
                active: false,
                scriptLoaded: false,
                imports: {
                    onActiveChange: 'active'
                }
            },
            placeOrderHandler: null,
            validateHandler: null,


            initObservable: function () {
                this._super()
                    .observe('active scriptLoaded');

                return this;
            },


            setPlaceOrderHandler: function (handler) {
                this.placeOrderHandler = handler;
            },


            setValidateHandler: function (handler) {
                this.validateHandler = handler;
            },


            context: function () {
                return this;
            },


            isShowLegend: function () {
                return true;
            },


            getCode: function () {
                return 'eway';
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


            getUrl: function () {
                return window.checkoutConfig.payment[this.getCode()].cryptUrl;
            },


            getEncryptKey: function () {
                return window.checkoutConfig.payment[this.getCode()].encryptKey;
            },


            getData: function () {
                var isEncrypt = window.eCrypt && this.isActive();
    
                return {
                    'method': this.item.method,
                    'additionalData': {
                        'cc_type': this.creditCardType(),
                        'cc_exp_year': this.creditCardExpYear(),
                        'cc_exp_month': this.creditCardExpMonth(),

                        'cc_number': isEncrypt ?
                            window.eCrypt.encryptValue(this.creditCardNumber(), this.getEncryptKey()) : '',
                        'cc_cid': isEncrypt ?
                            window.eCrypt.encryptValue(this.creditCardVerificationNumber(), this.getEncryptKey()) : '',
                        'cc_ss_start_month': this.creditCardSsStartMonth(),
                        'cc_ss_start_year': this.creditCardSsStartYear()
                    }
                };
            },


            placeOrder: function () {
                if (this.validateHandler() && additionalValidators.validate()) {
                    this.isPlaceOrderActionAllowed(false);
                    this._super();
                }
            }
        });
    }
);
