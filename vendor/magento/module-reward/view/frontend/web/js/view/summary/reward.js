/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component, totals) {
        return Component.extend({
            defaults: {
                template: 'Magento_Reward/summary/reward'
            },
            totals: totals.totals(),
            getPureValue: function () {
                var price = 0;
                if (this.totals) {
                    var segment = totals.getSegment('reward');
                    if (segment) {
                        price = segment['value'];
                    }
                }
                return price;
            },
            getValue: function () {
                return this.getFormattedPrice(this.getPureValue());
            },
            getRewardPoints: function() {
                return totals.totals().extension_attributes.reward_points_balance;
            },
            isAvailable: function() {
                return this.isFullMode() && this.getPureValue() != 0;
            }
        });
    }
);
