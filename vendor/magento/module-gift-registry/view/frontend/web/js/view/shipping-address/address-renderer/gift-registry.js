/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data'
], function(ko, Component, selectShippingAddressAction, quote, checkoutData) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'Magento_GiftRegistry/shipping-address/address-renderer/gift-registry'
        },

        initObservable: function () {
            this._super();
            this.isSelected = ko.computed(function() {
                var isSelected = false;
                var shippingAddress = quote.shippingAddress();
                if (shippingAddress) {
                    isSelected = shippingAddress.getKey() == this.address().getKey();
                }
                return isSelected;
            }, this);

            return this;
        },

        /** Set selected customer shipping address  */
        selectAddress: function() {
            selectShippingAddressAction(this.address());
            checkoutData.setSelectedShippingAddress(this.address().getKey());
        }
    });
});
