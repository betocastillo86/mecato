/**
 * Created by Beto on 1/13/2016.
 */
define(['handlebars'], function (Handlebars) {
    Handlebars.registerHelper('toLowerCase', function (str) {
        return str.toLowerCase();
    });

    Handlebars.registerHelper('debugger', function (str) {
        debugger;
    });

    Handlebars.registerHelper('random', function () {
        return Math.random();
    });

    Handlebars.registerHelper('get', function (str) {
        debugger;
        return str;
    });

    Handlebars.registerHelper('replaceEmpty', function (str, options) {
        if (!str || str == '')
            str = options.hash['replace'];
        return str;
    });

    Handlebars.registerHelper('stars', function (value) {
        var ret = '<div class="rating no-margin">';
        ret += '<div style="width:' + parseInt(value) * 20 + '%">';
        ret += '</div></div>';
        return new Handlebars.SafeString(ret);
    });
});