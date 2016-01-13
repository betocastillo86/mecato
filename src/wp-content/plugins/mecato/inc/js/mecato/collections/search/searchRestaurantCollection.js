/**
 * Created by Beto on 1/5/2016.
 */
define(['underscore', 'backbone'], function (_, Backbone) {
    var SearchRestaurantCollection = Backbone.Collection.extend({

        baseUrl: "/index.php/wp-json/api/restaurants",

        url: "/index.php/wp-json/api/restaurants",

        searchRestaurants : function(filter)
        {
            this.fetch({data : $.param(filter)});
        }
    });

    return SearchRestaurantCollection;
});