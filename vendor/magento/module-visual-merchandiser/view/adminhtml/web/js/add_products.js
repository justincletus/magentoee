/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true expr:true*/
/*global setLocation:true*/
define([
    'jquery',
    'Magento_Ui/js/core/app',
    'uiRegistry',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
], function ($, bootstrap, registry) {
    'use strict';

    $.widget('mage.visualMerchandiserAddProducts', {
        options: {
            dialogUrl: null,
            dialogButton: null
        },
        isGridLoaded: false,
        registry: null,
        positionCacheName: 'position_cache_valid',

        /**
         * @private
         */
        _create: function () {
            this.registry = registry;
            this.bootstrap = bootstrap;

            this.element.modal(this._getConfig());
            this._bind();
        },

        /**
         * @private
         */
        _bind: function () {
            this._on({
                requestUpdate: this.updateGrid,
                requestReload: this.reloadGrid
            });

            $(document).on('click', this.options.dialogButton, $.proxy(this.openDialog, this));
        },

        /**
         * Open the dialog
         */
        openDialog: function () {
            this.element.modal('openModal');
        },

        /**
         * Close the dialog
         */
        closeDialog: function () {
            this.element.modal('closeModal');
        },

        /**
         * Update the grid with changes
         */
        updateGrid: function () {
            var waitForAjaxLoad;

            this.registry.get('merchandiser_product_listing.merchandiser_product_listing_data_source').reload();

            $('body').trigger('processStart');

            /**
             * Wait for Ajax load
             */
            waitForAjaxLoad = function () {
                if ($.active) {
                    window.setTimeout(waitForAjaxLoad, 100);
                } else {
                    $('body').trigger('processStop');
                }
            };
            window.setTimeout(waitForAjaxLoad, 500);
        },

        /**
         * Perform a full grid update, caches will be invalidated
         * changes to grid will be lost.
         */
        reloadGrid: function () {
            this._invalidateCache();
            this.updateGrid();
        },

        /**
         * Invalidate grid selection cache
         * @private
         */
        _invalidateCache: function () {
            this.registry.set(this.positionCacheName, false);
        },

        /**
         * @returns {{type: String, title: String, opened: *, buttons: *}}
         * @private
         */
        _getConfig: function () {
            return {
                title: $.mage.__('Add Products'),
                opened: $.proxy(this._opened, this),
                buttons: this._getButtonsConfig()
            };
        },

        /**
         * @private
         */
        _opened: function () {
            if (!this.isGridLoaded) {
                this._invalidateCache();
                $.ajax({
                    type: 'GET',
                    url: this.options.dialogUrl,
                    context: $('body'),
                    success: $.proxy(this._ajaxSuccess, this)
                });
            } else {
                this.updateGrid();
            }
        },

        /**
         * @param {String} data
         * @private
         */
        _ajaxSuccess: function (data) {
            this._validateAjax(data);
            this.bootstrap(JSON.parse(data));
            this.isGridLoaded = true;
        },

        /**
         * @param {Object} response
         * @private
         */
        _validateAjax: function (response) {
            if (response.ajaxExpired && response.ajaxRedirect) {
                setLocation(response.ajaxRedirect);
            } else if (response.url) {
                setLocation(response.url);
            }
        },

        /**
         * @returns {*[]}
         * @private
         */
        _getButtonsConfig: function () {
            return [{
                text: $.mage.__('Save and Close'),
                class: '',
                click: $.proxy(this._save, this)
            }];
        },

        /**
         * @private
         */
        _save: function () {
            var idColumn = this
                .registry
                .get('merchandiser_product_listing.merchandiser_product_listing.merchandiser_product_columns.ids');

            this._invalidateCache();

            this._trigger('dialogSave', null, [
                idColumn.selected(),
                this
            ]);
        }
    });

    return $.mage.visualMerchandiserAddProducts;
});
