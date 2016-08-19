/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'mage/translate',
        'Magento_Reward/js/action/set-use-reward-points'
    ],
    function (Component, quote, $t, setUseRewardPointsAction) {
        'use strict';
        var rewardConfig = window.checkoutConfig.payment.reward;

        return Component.extend({
            defaults: {
                template: 'Magento_Reward/payment/reward'
            },

            label: rewardConfig.label,

            isAvailable: function() {
                var subtotal = parseFloat(quote.totals().grand_total),
                    rewardUsedAmount = parseFloat(quote.totals().extension_attributes.base_reward_currency_amount);
                return rewardConfig.isAvailable && ((subtotal > 0)) && rewardUsedAmount <= 0;
            },

            useRewardPoints: function() {
                setUseRewardPointsAction();
            }
        });
    }
);
