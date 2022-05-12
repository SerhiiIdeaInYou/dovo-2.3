define([
    'jquery',
    'jquery/ui',
], function ($) {

    $.widget('ds.divider', {
        _create: function()
        {
            this._initPlugin();
        },
        _initPlugin: function() {
            var _self = this;
            console.log(this.element);
            console.log(this.options);
        }
    });

    return $.ds.divider;

});