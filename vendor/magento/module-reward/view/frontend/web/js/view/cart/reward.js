/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'Magento_Reward/js/view/summary/reward'
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            rewardPointsRemoveUrl: window.checkoutConfig.review.reward.removeUrl,

            /**
             * @override
             */
            isAvailable: function () {
                return this.getPureValue() !== 0;
            }
        });
    }
);
