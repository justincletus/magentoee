/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true*/
/*global define*/
define(
    [
        'uiComponent'
    ],
    function (Component) {
        return Component.extend({
            defaults: {
                template: 'Magento_Reward/authentication/tooltip'
            },
            isAvailable: window.checkoutConfig.authentication.reward.isAvailable,
            tooltipLearnMoreUrl: window.checkoutConfig.authentication.reward.tooltipLearnMoreUrl,
            tooltipMessage: window.checkoutConfig.authentication.reward.tooltipMessage
        });
    }
);
