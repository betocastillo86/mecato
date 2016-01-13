define(['underscore', 'backbone'], function (_, Backbone) {
    var SearchRestaurantModel = Backbone.Model.extend({

        baseUrl: "/api/vendors/addresses",

        url: "/api/vendors/addresses",

        validation: {
            text: {
                required:false,
            },
            menuType: {
                required: false
            },
            cityId: {
                required:true
            }
        }

    });

    return SearchRestaurantModel;
});