/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'Magento_Checkout/js/model/resource-url-manager'
    ],
    function($, resourceUrlManager) {
        "use strict";
        return $.extend(resourceUrlManager, {
            getUrlForEstimationShippingMethodsForGiftRegistry: function(quote) {
                var params = (this.getCheckoutMethod() ===  'guest') ? {quoteId: quote.getQuoteId()} : {};
                var urls = {
                    'guest': '/guest-giftregistry/:quoteId/estimate-shipping-methods',
                    'customer': '/giftregistry/mine/estimate-shipping-methods'
                };
                return this.getUrl(urls, params);
            }
        });
    }
);
