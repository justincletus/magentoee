/**
 * AdminGws client side validation rules
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    "jquery",
    "mage/backend/validation"
], function($){

    $.validator.addMethod('validate-one-gws-store', function(element) {
        if ($('#gws_is_all').val() == 1) {
            return true; // not touching valid intentionally
        }
        return $('.validate-one-gws-store:checked').length;
    }, 'Please select one of the options.');

    $.widget('mage.validation', $.mage.validation, {
        options: {
            errorPlacement: function(error, element) {
                if (element.is('[name="gws_store_groups[]"]')) {
                    error.insertAfter($('#gws_container').last());
                } else {
                    error.insertAfter(element);
                }
            }
        }
    });
});