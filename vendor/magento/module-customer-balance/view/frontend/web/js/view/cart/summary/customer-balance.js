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
        'Magento_CustomerBalance/js/view/summary/customer-balance'
    ],
    function (Component) {
        'use strict';

        var balanceRemoveUrl  = window.checkoutConfig.payment.customerBalance.balanceRemoveUrl;

        return Component.extend({


            getRemoveUrl: function () {

                return balanceRemoveUrl;
            },


            isFullMode: function () {

                return true;
            }
        });
    }
);
