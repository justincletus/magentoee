/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'jquery',
        'underscore',
        "mage/template"
    ],
    function ($, _, mageTemplate) {
        'use strict';
        return {
            build: function (response) {
                var tmpl;
                var formTmpl =
                    '<form action="<%= data.action %>" method="POST" hidden enctype="application/x-www-form-urlencoded">' +
                        '<% _.each(data.fields, function(val, key){ %>' +
                        '<input value="<%= val %>" name="<%= key %>" type="hidden">' +
                        '<% }); %>' +
                        '</form>';

                var inputs = {};
                for (var index in response.fields) {
                    inputs[response.fields[index]] = response.values[index]
                }

                var hiddenFormTmpl = mageTemplate(formTmpl);
                tmpl = hiddenFormTmpl({
                    data: {
                        action: response.action,
                        fields: inputs
                    }
                });
                return $(tmpl).appendTo($('[data-container="body"]'));
            }
        };
    }
);
