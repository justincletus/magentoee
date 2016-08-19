/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent'
    ],
    function (Component) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magento_GiftRegistry/billing-address/choice'
            },
            giftRegistryId: window.checkoutConfig.giftRegistry.id,
            hasGiftRegistryInCart: window.checkoutConfig.giftRegistry.available
                && window.checkoutConfig.giftRegistry.id,
            getAdditionalData: function() {
                return {'gift_registry_id': this.giftRegistryId};
            }
        });
    }
);
