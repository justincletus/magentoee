/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'Magento_GiftWrapping/js/view/summary/totals'
    ],
    function (Component) {
        'use strict';

        return Component.extend({

            /**
             * @override
             */
            isFullMode: function () {
                return true;
            }
        });
    }
);
