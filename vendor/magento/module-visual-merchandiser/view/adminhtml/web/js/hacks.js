/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true expr:true*/
/*global catalog_category_productsJsObject:true*/
define([
    'jquery',
    'mage/adminhtml/grid'
], function ($) {
    'use strict';

    var oldScrollParent = $.fn.scrollParent;

    // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
    /**
     * Ignore this function
     */
    catalog_category_productsJsObject.checkboxCheckCallback = function () {};

    /**
     * Ignore this function
     */
    catalog_category_productsJsObject.initRowCallback = function () {};
    // jscs:enable requireCamelCaseOrUpperCaseIdentifiers

    $('#merchandiser-app tbody.ui-sortable > * .input-text').each(function (i, elem) {
        elem.stopObserving('keyup');
    });

    /**
     * Override the original scrollParent
     * @returns {*}
     */
    $.fn.scrollParent = function () {
        return this.parent('.ui-sortable').length > 0 ? $(document) : oldScrollParent.bind(this)();
    };
});
