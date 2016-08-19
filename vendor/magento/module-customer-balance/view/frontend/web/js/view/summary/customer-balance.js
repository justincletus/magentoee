/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
/**
 * Customer balance summary block info
 */
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component, totals) {
        return Component.extend({
            defaults: {
                template: 'Magento_CustomerBalance/summary/customer-balance'
            },
            totals: totals.totals(),
            /**
             * Used balance without any formatting
             *
             * @return {number}
             */
            getPureValue: function () {
                var price = 0;
                if (this.totals) {
                    var segment = totals.getSegment('customerbalance');
                    if (segment) {
                        price = segment['value'];
                    }
                }
                return price;
            },
            /**
             * Used balance with currency sign and localization
             *
             * @return {string}
             */
            getValue: function () {
                return this.getFormattedPrice(this.getPureValue());
            },
            /**
             * Availability status
             *
             * @returns {boolean}
             */
            isAvailable: function() {
                return this.isFullMode() && this.getPureValue() != 0;
            }
        });
    }
);
