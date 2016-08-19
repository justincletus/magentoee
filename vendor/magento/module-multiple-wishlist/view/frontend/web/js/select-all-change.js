/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery'
], function ($) {
    'use strict';

    return function (config, element) {
        $(element).change(function () {
            var selectAll = $(this);

            $('[data-role="select-product"]', '.products-grid.wishlist').filter(':enabled')
                .prop('checked', selectAll.prop('checked'));
        });
    };
});
