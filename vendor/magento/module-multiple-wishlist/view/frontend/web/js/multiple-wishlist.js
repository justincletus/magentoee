/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true expr:true sub:true*/
/*global alert confirm*/
define([
    "jquery",
    'mage/template',
    "mage/validation/url",
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/confirm',
    "mage/validation/validation",
    "jquery/ui",
    "mage/translate",
    "mage/dataPost",
    "mage/dropdowns"
], function($, mageTemplate, urlValidator, alert, confirm) {
    'use strict';

    $.widget('mage.multipleWishlist', {
        options: {
            createTmplSelector: '#popup-tmpl',
            createTmplData: {
                btnCloseClass: 'close',
                popupWishlistBlockId: 'create-wishlist-block',
                popupWishlistFormId: 'create-wishlist-form',
                url: '#',
                isPublic: false,
                isEdit: false
            },
            errorMsg: $.mage.__('We can\'t create the Wish List right now.'),
            spinnerClass: 'loading'
        },

        _create: function() {
            var _this = this; // in this case $(e.targer) is not the same as $(this)
            this.element.on('click', '[data-wishlist-create]', function() {
                var json = $(this).data('wishlist-create'),
                    url = json['url'] ? json['url'] : _this.options.createUrl,
                    isAjax = json['ajax'];
                _this._showCreateWishlist(url, isAjax);
                return false;
            });
        },

        /**
         * Show create wishlist popup block
         * @private
         * @param url - create wishlist url
         * @param isAjax - if need to use ajax to call create wishlist url
         */
        _showCreateWishlist: function(url, isAjax) {
            this.createTmpl ? this.createTmpl.show() : this._initCreateTmpl();
            $('#' + this.options.createTmplData.popupWishlistFormId).attr('action', url);
            this.createAjax = isAjax;
        },

        /**
         * Initialized jQuery template for create wishlist popup block
         * @private
         */
        _initCreateTmpl: function() {
            this.createTmpl = $(mageTemplate(this.options.createTmplSelector, {
                data: this.options.createTmplData
            }));
            this.createTmpl.on('click', '.' + this.options.createTmplData.btnCloseClass, $.proxy(function() {
                    this.createTmpl.hide();
                }, this))
                .appendTo('body');
            $('#' + this.options.createTmplData.popupWishlistFormId).on('submit', $.proxy(function (event) {
                var form = event.target;
                if (this.createAjax) {
                    event.preventDefault();
                    this._createWishlistAjax(form);
                } else {
                    form.submit();
                }
            }, this));
        },

        /**
         * Call create wishlist url using ajax, when response returns, call callback function
         * @private
         * @param form - create wishlist form
         */
        _createWishlistAjax: function(form) {
            var _form = $(form), _this = this;
            $.ajax({
                url: _form.attr('action'),
                type: 'post',
                cache: false,
                data: _form.serialize(),
                beforeSend: function() {
                    $('#' + _this.options.createTmplData.popupWishlistBlockId).addClass(_this.options.spinnerClass);
                },
                success: function(response) {
                    if (typeof response['wishlist_id'] !== 'undefined') {
                        if (_this._callback) {
                            _this._callback(response.wishlist_id);
                        }
                    } else if (typeof response['redirect'] !== 'undefined') {
                        urlValidator.redirect(response.redirect);
                    } else {
                        alert({
                            content: _this.options.errorMsg
                        });
                    }
                }
            });
        }
    });

    // Extension for mage.wishlist - Move to Wish List
    $.widget('mage.multipleWishlist', $.mage.multipleWishlist, {
        options: {
            wishlistFormSelector: '#wishlist-view-form',
            formTmplSelector: '#form-tmpl-multiple',
            formTmplId: '#wishlist-hidden-form'
        },

        _create: function() {
            this._super();
            this.moveWishlistJson = this.element.find('[data-wishlist-move]').data('wishlist-move');
            this.element.on('click', '[data-wishlist-move-selected]', $.proxy(this._moveSelectedTo, this));
            this.element.on('click', '[data-wishlist-move-item]', $.proxy(this._moveItemTo, this));
        },

        /**
         * Move one wishlist item to another wishlist
         * @private
         * @param e - Item in wishlist got clicked
         */
        _moveItemTo: function(e) {
            var json = $(e.currentTarget).data('wishlist-move-item'),
                tmplJson = {
                    qty: this._getQty($(e.currentTarget)),
                    itemId: json['itemId'],
                    url: this.moveWishlistJson.moveItemUrl
                };
            if (json['new']) {
                this._moveItemToNew(tmplJson);
            } else {
                tmplJson.wishlistId = json['wishlistId'];
                $(mageTemplate(this.options.formTmplSelector, {
                    data: tmplJson
                })).appendTo('body');
                $(this.options.formTmplId).submit();
            }
        },

        /**
         * Get wishlist item qty
         * @private
         * @param elem
         * @return {(int|null)} - Item qty
         */
        _getQty: function(elem) {
            var qty = elem.closest('div.product-item-info').find('input.qty');
            return qty.length ? qty[0].value : null;
        },

        /**
         * Move selected wishlist items to another wishlist
         * @private
         * @param e - "Move to Wish List" button
         */
        _moveSelectedTo: function(e) {
            var json = $(e.currentTarget).data('wishlist-move-selected'),
                wishlistId = json['wishlistId'];
            if (!this._itemsSelected()) {
                alert({
                    content: $.mage.__('Please select items to move.')
                });

                return;
            }
            if (json['new']) {
                this._moveSelectedToNew();
            } else {
                var url = this.moveWishlistJson.moveSelectedUrl.replace("%wishlist_id%", wishlistId);
                $(this.options.wishlistFormSelector).attr('action', url).submit();
            }
        },

        /**
         * Move selected wishlist items to a new wishlist: involve show create wishlist popup,
         * using ajax to get new wishlistId, and passing wishlistId to _callback, which submits to moveSelectedUrl
         * @private
         * @param url - target url(can be move or copy)
         */
        _moveSelectedToNew: function(url) {
            this._callback = function(wishlistId) {
                var _url = (url || this.moveWishlistJson.moveSelectedUrl).replace("%wishlist_id%", wishlistId);
                $(this.options.wishlistFormSelector).attr('action', _url).submit();
            };
            this._showCreateWishlist(this.options.createUrl, true);
        },

        /**
         * Move one wishlist item to a new wishlist: involve show create wishlist popup,
         * using ajax to get new wishlistId, and passing wishlistId to _callback, which submits to moveItemUrl
         * @private
         * @param tmplJson - a closure variable holds itemId, qty, and url
         */
        _moveItemToNew: function(tmplJson) {
            this._callback = function(wishlistId) {
                tmplJson.wishlistId = wishlistId;
                $(mageTemplate(this.options.formTmplSelector, {
                    data: tmplJson
                })).appendTo('body');
                $(this.options.formTmplId).submit();
            };
            this._showCreateWishlist(this.options.createUrl, true);
        },

        /**
         * Make sure at lease one item is selected
         * @private
         * @return {Boolean}
         */
        _itemsSelected: function() {
            return $(this.options.wishlistFormSelector).find('input.checkbox:checked').length ? true : false;
        }
    });

    // Extension for mage.wishlist - Copy to Wishlist
    $.widget('mage.multipleWishlist', $.mage.multipleWishlist, {
        _create: function() {
            this._super();
            this.copyWishlistJson = this.element.find('[data-wishlist-copy]').data('wishlist-copy');
            this.element.on('click', '[data-wishlist-copy-selected]', $.proxy(this._copySelectedTo, this));
            this.element.on('click', '[data-wishlist-copy-item]', $.proxy(this._copyItemTo, this));
        },

        /**
         * Copy one wishlist item to a different wishlist
         * @private
         * @param e - Item in wishlist got clicked
         */
        _copyItemTo: function(e) {
            var json = $(e.currentTarget).data('wishlist-copy-item'),
                tmplJson = {
                    qty: this._getQty($(e.currentTarget)),
                    itemId: json['itemId'],
                    url: this.copyWishlistJson.copyItemUrl
                };
            if (json['new']) {
                this._copyItemToNew(tmplJson);
            } else {
                tmplJson.wishlistId = json['wishlistId'];
                $(mageTemplate(this.options.formTmplSelector, {
                    data: tmplJson
                })).appendTo('body');
                $(this.options.formTmplId).submit();
            }
        },

        /**
         * Copy selected wishlist items to a different wishlist
         * @private
         * @param e - copy to wishlist button
         */
        _copySelectedTo: function(e) {
            var json = $(e.currentTarget).data('wishlist-copy-selected'),
                wishlistId = json['wishlistId'];
            if (!this._itemsSelected()) {
                alert({
                    content: $.mage.__('Please select items to copy.')
                });
                return;
            }
            if (json['new']) {
                this._copySelectedToNew();
            } else {
                var url = this.copyWishlistJson.copySelectedUrl.replace("%wishlist_id%", wishlistId);
                $(this.options.wishlistFormSelector).attr('action', url).submit();
            }
        },

        /**
         * Copy selected wishlist items to a new wishlist
         * @private
         */
        _copySelectedToNew: function() {
            this._moveSelectedToNew(this.copyWishlistJson.copySelectedUrl);
        },

        /**
         * Copy one wishlist item to a new wishlist
         * @private
         * @param tmplJson - a closure variable holds itemId, qty, and url
         */
        _copyItemToNew: function(tmplJson) {
            this._moveItemToNew(tmplJson);
        }
    });

    // Extension for mage.wishlist - Delete Wishlist
    $.widget('mage.multipleWishlist', $.mage.multipleWishlist, {
        options: {
            deleteMsg: $.mage.__('Are you sure you want to delete your wish list? This action can\'t be undone.')
        },

        _create: function() {
            this._super();
            this.element.on('click', '[data-wishlist-delete]', $.proxy(this._deleteWishlist, this));
        },

        /**
         * Delete wishlist and redirect to first wishlist
         * @private
         * @param e - "Delete Wish List" button
         */
        _deleteWishlist: function(e)  {
            var json;

            e.preventDefault();
            confirm({
                content: this.options.deleteMsg,
                actions: {
                    confirm: function(){
                        json = $(e.currentTarget).data('wishlist-delete');
                        $.ajax({
                            url: json['deleteUrl'].replace('%item%', json['wishlistId']),
                            type: 'post',
                            cache: false,
                            success: function() {
                                window.location.href = json['redirectUrl'];
                            }
                        });
                    }
                }
            });
        }
    });

    // Extension for mage.wishlist - Edit Wishlist
    $.widget('mage.multipleWishlist', $.mage.multipleWishlist, {
        options: {
            editTmplSelector: '#popup-tmpl',
            editTmplData: {
                btnCloseClass: 'close',
                popupWishlistBlockId: 'edit-wishlist-block',
                popupWishlistFormId: 'edit-wishlist-form',
                isEdit: true
            }
        },

        _create: function() {
            this._super();
            this.element.on('click', '[data-wishlist-edit]', $.proxy(this._editWishlist, this));
        },

        /**
         * Edit wishlist
         * @private
         * @param e - Edit wishlist button
         */
        _editWishlist: function(e)  {
            var json = $(e.currentTarget).data('wishlist-edit');
            this.options.editTmplData.url = json['url'];
            this.options.editTmplData.name = json['name'];
            this.options.editTmplData.isPublic = json['isPublic'];
            this.editTmpl ? this.editTmpl.show() : this._initEditTmpl();
            return false;
        },

        /**
         * Initialized jQuery template for edit wishlist popup block, attach to dom and validation widget to form
         * @private
         */
        _initEditTmpl: function() {
            this.editTmpl = $(mageTemplate(this.options.editTmplSelector, {
                data: this.options.editTmplData
            }));
            this.editTmpl.on('click', '.' + this.options.editTmplData.btnCloseClass, $.proxy(function() {
                this.editTmpl.hide();
            }, this)).appendTo('body');
            $('#' + this.options.editTmplData.popupWishlistFormId).validation();
        }
    });

    // Extension for mage.wishlist - Add Wishlist
    $.widget('mage.multipleWishlist', $.mage.multipleWishlist, {
        options: {
            wishlistLink: '.link-wishlist',
            splitBtnTmpl: '#split-btn-tmpl'
        },

        _create: function() {
            this._super();
            this.element.on('click', '[data-post-new-wishlist]', $.proxy(function(e) {
                var data = $(e.currentTarget).data('post-new-wishlist');
                $.extend(true, data, $(e.currentTarget).data('post'));
                this._addToNew(data);
            }, this));
            this._buildWishlistDropdown();
        },

        /**
         * Add product to new wishlist
         * @private
         * @param data
         */
        _addToNew: function(data) {
            this._callback = $.proxy(function(wishlistId) {
                data.data.wishlist_id = wishlistId;
                $.mage.dataPost().postData(data);
            }, this);
            this._showCreateWishlist(this.options.createUrl, true);
        },

        /**
         * Build "Add to Wish List" dropdown list
         * @private
         */
        _buildWishlistDropdown: function() {
            if (this.options.wishlists && this.options.wishlists.length > 0) {
                $(this.options.wishlistLink).each($.proxy(function(index, e) {
                    var element = $(e),
                        buttonName = element.text().trim(),
                        generalParams = element.data('post'),
                        tmplData = {wishlists: [], generalParams: generalParams, buttonName: buttonName};
                    for (var i = 0; i < this.options.wishlists.length; i++) {
                        var currentData = $.extend({}, generalParams.data, {wishlist_id: this.options.wishlists[i].id}),
                            currentParams = {action: generalParams.action, data: currentData};
                        tmplData.wishlists.push({
                            name: this.options.wishlists[i].name,
                            params: currentParams
                        });
                    }
                    if (this.options.canCreate) {
                        tmplData.wishlists.push({
                            newClass: 'new',
                            name: 'Create New Wish List',
                            params: generalParams
                        });
                    }
                    $(mageTemplate(this.options.splitBtnTmpl, {
                        data: tmplData
                    })).prependTo(element.parent());
                    element.parent().trigger('contentUpdated');
                    element.remove();
                }, this));
            }
        }
    });

    // Extension for mage.wishlist - Add Wishlist item to Gift Registry
    $.widget('mage.multipleWishlist', $.mage.multipleWishlist, {
        _create: function() {
            this._super();
            var _this = this;
            this.element.on('click', '[data-wishlist-to-giftregistry]', function() {
                var json = $(this).data('wishlist-to-giftregistry'),
                    tmplJson = {
                        item: json['itemId'],
                        entity: json['entity'],
                        url: json['url']
                    };
                $(mageTemplate(_this.options.formTmplSelector, {
                    data: tmplJson
                })).appendTo('body');
                $(_this.options.formTmplId).submit();
            });
        }
    });

    return $.mage.multipleWishlist;
});