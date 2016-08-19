/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(['ko'], function (ko) {
    return {
        code: ko.observable(false),
        amount: ko.observable(false),
        isValid: ko.observable(false),
        isChecked: ko.observable(false)
    }
});


