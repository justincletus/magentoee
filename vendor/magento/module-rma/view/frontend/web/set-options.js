/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery'
], function ($) {
    'use strict';

    return function (config, element) {
        $(element).on('mage.setUpRmaOptions', function (e, rmaTrackInfo) {
            rmaTrackInfo.options.deleteLabelUrl = config.deleteLabelUrl;
            rmaTrackInfo.options.deleteMsg = config.deleteMsg;
        });
    };
});
