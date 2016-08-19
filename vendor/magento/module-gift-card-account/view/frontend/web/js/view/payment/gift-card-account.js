/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magento_GiftCardAccount/js/action/set-gift-card-information',
        'Magento_GiftCardAccount/js/action/get-gift-card-information',
        'Magento_Checkout/js/model/totals',
        'Magento_GiftCardAccount/js/model/gift-card',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        "mage/validation"

    ],
    function (
        $,
        ko,
        Component,
        setGiftCardAction,
        getGiftCardAction,
        totals,
        giftCardAccount,
        quote,
        priceUtils
    ) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magento_GiftCardAccount/payment/gift-card-account',
                giftCartCode: ''
            },
            isLoading: getGiftCardAction.isLoading,
            giftCardAccount: giftCardAccount,
            initObservable: function () {
                this._super()
                    .observe('giftCartCode');
                return this;
            },
            setGiftCard: function () {
                if (this.validate()) {
                    setGiftCardAction([this.giftCartCode()])
                }
            },
            checkBalance: function () {
                if (this.validate()) {
                    getGiftCardAction.check(this.giftCartCode())
                }
            },
            getAmount: function (price) {
                return priceUtils.formatPrice(price, quote.getPriceFormat());
            },
            validate: function () {
                var form = '#giftcard-form';
                return $(form).validation() && $(form).validation('isValid');
            }
        })
    }
);
