/**
 * GiftRegistry client side validation rules
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global parts:true*/
define(["jquery","mage/validation"], function($){

    $.validator.addMethod('attribute-code', function(v, element){
        var resultFlag = true,
            select = $($('#' + $(element).prop('id').sub('_code','_type')));
        $.each(select.find('option'), function(i, option) {
            parts = $(option).val().split(':');
            if (parts[1] !== undefined && parts[1] == v) {
                resultFlag = false;
            }
        });
        return resultFlag;
    }, 'Please use a different input type for this code.');

    $.validator.addMethod('required-option-select-rows', function(v, elm) {
        var optionContainerElm = $(elm).closest('fieldset');
        return !!$(optionContainerElm).find('tr:not(.no-display) .select-option-code').length;
    }, 'Please add rows to option.');

});