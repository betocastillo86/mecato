/**
 * Created by Beto on 1/5/2016.
 */
define(['underscore', 'backbone'], function (_, Backbone) {
    var ResultSearchRestaurantModel = Backbone.Model.extend({

        baseUrl: "/api/vendors/addresses",

        url: "/api/vendors/addresses",

    });

    return ResultSearchRestaurantModel;
});