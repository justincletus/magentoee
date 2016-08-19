/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_GiftRegistry/js/model/recipient-address',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/shipping-rate-service',
        'Magento_GiftRegistry/js/model/shipping-rate-processor/gift-registry',
        'Magento_Checkout/js/model/shipping-save-processor',
        'Magento_GiftRegistry/js/model/shipping-save-processor/gift-registry'
    ],
    function (
        Component,
        recipientAddress,
        addressList,
        shippingRateService,
        shippingRateProcessor,
        shippingSaveProcessor,
        giftRegistryShippingSaveProcessor
    ) {
        'use strict';

        //Register gift registry address provider
        if(window.checkoutConfig.giftRegistry.available && window.checkoutConfig.giftRegistry.id) {
            addressList.push(new recipientAddress(window.checkoutConfig.giftRegistry.id));
        }

        //Register gift registry rate processor
        shippingRateService.registerProcessor('gift-registry', shippingRateProcessor);

        //Register gist registry save shipping address processor
        shippingSaveProcessor.registerProcessor('gift-registry', giftRegistryShippingSaveProcessor);

        /** Add view logic here if needed */
        return Component.extend({});
    }
);
