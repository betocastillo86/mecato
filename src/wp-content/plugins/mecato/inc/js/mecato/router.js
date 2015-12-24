define(['underscore', 'backbone', 'mecato/views/crud/newRestaurantView'],
    function (_, Backbone, NewRestaurantView) {

        var MecatoRouter = Backbone.Router.extend({
            currentView: undefined,

            //el por defecto para las vistas
            defaultEl: "#divMainSection",

            routes: {
                "index.php/crear-restaurante(/)(?id=:id)": "newRestaurant",
                "index.php/actualizar-restaurante(/)(?id=:id)": "newRestaurant"
            },
            newRestaurant : function(id)
            {
                this.currentView = new NewRestaurantView({el : this.defaultEl, id : id != undefined ? parseInt(id) : undefined });
            }
        });

        return MecatoRouter;
    });

