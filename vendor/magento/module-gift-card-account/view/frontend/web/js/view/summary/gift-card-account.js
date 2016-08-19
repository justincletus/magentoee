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
        'Magento_Checkout/js/view/summary/abstract-total',
        'mage/url',
        'Magento_Checkout/js/model/totals'
    ],
    function ($, ko, generic, url, totals) {
        'use strict';

        return generic.extend({
            defaults: {
                template: 'Magento_GiftCardAccount/summary/gift-card-account'
            },
            /**
             * Get information about applied gift cards and their amounts
             *
             * @returns {Array}.
             */
            getAppliedGiftCards: function () {
                if (totals.getSegment('giftcardaccount')) {
                    return JSON.parse(totals.getSegment('giftcardaccount').extension_attributes.gift_cards);
                }

                return [];
            },
            isAvailable: function () {
                return this.isFullMode() && totals.getSegment('giftcardaccount')
                    && totals.getSegment('giftcardaccount').value != 0;
            },
            getAmount: function (usedBalance) {
                return this.getFormattedPrice(usedBalance);
            }
        });
    }
);
