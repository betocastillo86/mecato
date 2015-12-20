define(['underscore', 'backbone', 'mecato/views/crud/newRestaurantView'],
    function (_, Backbone, NewRestaurantView) {

        var MecatoRouter = Backbone.Router.extend({
            currentView: undefined,

            //el por defecto para las vistas
            defaultEl: "#divMainSection",

            routes: {
                "index.php/crear-restaurante(/)": "newRestaurant"
            },
            newRestaurant : function()
            {
                this.currentView = new NewRestaurantView({el : this.defaultEl });
            }
        });

        return MecatoRouter;
    });

