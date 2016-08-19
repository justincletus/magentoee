/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
define([
    "jquery",
    "jquery/ui"
], function($){
    "use strict";
    
    $.widget('mage.ticker', {
        options: {
            secondsInDay: 86400,
            secondsInHour: 3600,
            secondsInMinute: 60,
            msInSecond: 1000
        },

        _create: function() {
            this.start = new Date();
            setInterval($.proxy(function(){
                var seconds = this._getEstimate(),
                    daySec = Math.floor(seconds / this.options.secondsInDay) * this.options.secondsInDay,
                    hourSec = Math.floor(seconds / this.options.secondsInHour) * this.options.secondsInHour,
                    minuteSec =  Math.floor(seconds / this.options.secondsInMinute) * this.options.secondsInMinute;
                this.element.find('[data-container="days"]').html(this._formatNumber(Math.floor(daySec / this.options.secondsInDay)));
                this.element.find('[data-container="hour"]').html(this._formatNumber(Math.floor((hourSec - daySec) / this.options.secondsInHour)));
                this.element.find('[data-container="minute"]').html(this._formatNumber(Math.floor((minuteSec - hourSec) / this.options.secondsInMinute)));
                this.element.find('[data-container="second"]').html(this._formatNumber(seconds - minuteSec));
                if (daySec > 0) {
                    this.element.find('[data-container="days"]').parent().css("display", "inline-block");
                    this.element.find('[data-container="hour"]').parent().css("display", "inline-block");
                    this.element.find('[data-container="minute"]').parent().css("display", "inline-block");
                    this.element.find('[data-container="second"]').parent().hide();
                } else {
                    this.element.find('[data-container="days"]').parent().hide();
                    this.element.find('[data-container="hour"]').parent().css("display", "inline-block");
                    this.element.find('[data-container="minute"]').parent().css("display", "inline-block");
                    this.element.find('[data-container="second"]').parent().css("display", "inline-block");
                }
            }, this), this.options.msInSecond);
        },

        /**
         * get estimated remaining seconds
         * @returns {number}
         * @private
         */
        _getEstimate: function () {
            var now = new Date(),
                result = this.options.secondsToClose - (now.getTime() - this.start.getTime()) / this.options.msInSecond;
            return result < 0 ? 0 : Math.round(result);
        },

        /**
         * format number, prepend 0 for single digit number
         * @param number
         * @returns {string}
         * @private
         */
        _formatNumber: function (number) {
            return number < 10 ? '0' + number.toString() : number.toString();
        }
    });

    return $.mage.ticker;
});