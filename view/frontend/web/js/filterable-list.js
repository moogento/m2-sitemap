define([
    'jquery',
    'uiComponent',
    'ko',
],
function($, Component, ko) {
        'use strict';

        return Component.extend({

            findText: ko.observable(),

            initialize: function () {
                this._super();
                this.getActions();
                return this;
            },

            getActions: function () {
                let self = this;
                self.findInSitemap = function (inputText) {
                    let filter, sitemap, list;
                    filter = inputText.toUpperCase();
                    sitemap = $('#sitemap');
                    list = sitemap.find('a.cat-name');
                    list.each(function(index) {
                        if (!($(this).text().toUpperCase().indexOf(filter) > -1)) {
                            $(this).addClass('not-found');
                            $(this).parent().find('.prod_count').addClass('not-found');
                        } else {
                            $(this).removeClass('not-found');
                            $(this).parent().find('.prod_count').removeClass('not-found');
                        }
                    });
                };
            }
        });
})
