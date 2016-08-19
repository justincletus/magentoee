/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'uiComponent',
    'Magento_Banner/js/model/banner',
    'underscore',
    'jquery'
], function (Component, Banner, _, $) {
    'use strict';

    function getItems(displayMode, types, displayedBannersIds) {
        var items = [];
        if (!_.isEmpty(Banner.get('data')().items)) {
            var banners = Banner.get('data')().items[displayMode];
            types = types ? types.split(',') : null;
            displayedBannersIds = displayedBannersIds ? displayedBannersIds.split(',') : null;
            var displayedBanners = _.filter(banners, function (banner) {
                return !types ? true : _.isEmpty(_.difference(types, banner.types));
            });
            if (displayedBannersIds) {
                _.each(displayedBannersIds, function(val) {
                    var banner = _.findWhere(banners, {id: val});

                    if (!_.isEmpty(banner)) {
                        items.push({
                            html: banner.content,
                            bannerId: banner.id
                        });
                    }
                });
            } else {
                _.each(displayedBanners, function (banner) {
                    items.push({
                        html: banner.content,
                        bannerId: banner.id
                    });
                });
            }
        }
        return items;
    }

    return Component.extend({
        initialize: function () {
            this._super();

            this.banner = Banner.get('data');

            _.each($('[data-banner-id]'), function(banner) {
                banner = $(banner);
                this['getItems' + banner.data('banner-id')] = getItems.bind(
                    null,
                    banner.data('display-mode'),
                    banner.data('types'),
                    banner.data('ids') + ''
                )
            }, this);
        }
    });
});
