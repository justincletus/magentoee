/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define([], function() {
    /**
     * @param int registryId
     * Returns new address object
     */
    return function (registryId) {
        return {
            giftRegistryId: registryId,

            isDefaultShipping: function() {
                return false;
            },

            getType: function() {
                return 'gift-registry';
            },

            getKey: function() {
                return this.getType() + this.giftRegistryId;
            },

            getCacheKey: function() {
                return this.getKey();
            },

            isEditable: function() {
                return false;
            },
            canUseForBilling: function() {
                return false;
            }
        }
    }
});
