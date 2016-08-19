/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint jquery:true*/
define([
	"jquery",
	"jquery/ui",
	"mage/validation"
], function($){
    "use strict";
    
    $.widget("mage.validation", $.mage.validation, {
        options: {
            ignore: 'form form input, form form select, form form textarea'
        }
    });

});