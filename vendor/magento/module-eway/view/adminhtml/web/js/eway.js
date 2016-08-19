/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'uiComponent',
    'underscore'
], function ($, Class) {
    'use strict';

    return Class.extend({

        defaults: {
            formSelector: '#edit_form',
            active: false,
            scriptLoaded: false,
            imports: {
                onActiveChange: 'active'
            }
        },

        initObservable: function () {
            this._super()
                .observe('active scriptLoaded');
            $(this.formSelector).off('changePaymentMethod.' + this.code)
                .on('changePaymentMethod.' + this.code, this.changePaymentMethod.bind(this));

            return this;
        },

        onActiveChange: function (isActive) {
            this.disableEventListeners();

            if (isActive) {
                window.order.addExcludedPaymentMethod(this.code);

                if (!this.scriptLoaded()) {
                    this.loadScript();
                }
                this.enableEventListeners();
            }
        },

        enableEventListeners: function () {
            $(this.formSelector).on('invalid-form.validate.' + this.code, this.invalidFormValidate.bind(this))
                .on('afterValidate.beforeSubmit', this.beforeSubmit.bind(this));
        },

        disableEventListeners: function () {
            $(self.formSelector).off('invalid-form.validate.' + this.code)
                .off('afterValidate.beforeSubmit');
        },

        loadScript: function () {
            var state = this.scriptLoaded;

            $('body').trigger('processStart');
            require([this.cryptUrl], function () {
                state(true);
                $('body').trigger('processStop');
            });

            return this;
        },

        changePaymentMethod: function (event, method) {
            this.active(method === this.code);

            return this;
        },

        invalidFormValidate: function () {
            return this;
        },

        beforeSubmit: function () {
            return this;
        }

    });
});
