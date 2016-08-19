/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(['jquery'], function ($) {
    'use strict';

    return function (config, element) {
        $(element).on('change', function (event) {
            event.preventDefault();
            $('form[id^="gr-quick-search-widget"]').hide();
            $('#gr-quick-search-widget-' + this.value + '-form').show();
        });
    };
});
