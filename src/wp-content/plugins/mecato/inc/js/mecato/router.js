define(['underscore', 'backbone', 'mecato/views/crud/newRestaurantView', 'mecato/views/crud/newMenuView', 'mecato/views/restaurant/restaurantDetailView',
        'mecato/views/restaurant/menuDetailView', 'mecato/views/search/searchRestaurantView',
    , 'jqueryui'],
    function (_, Backbone, NewRestaurantView, NewMenuView, RestaurantDetailView,
              MenuDetailView, SearchRestaurantView) {

        var MecatoRouter = Backbone.Router.extend({
            currentView: undefined,

            //el por defecto para las vistas
            defaultEl: "#divMainSection",

            routes: {
                "index.php/crear-restaurante(/)(?id=:id)": "newRestaurant",
                "index.php/actualizar-restaurante(/)(?id=:id)": "newRestaurant",
                "index.php/crear-plato(/)(?id=:id)": "newMenu",
                "index.php/actualizar-plato(/)(?id=:id)": "newMenu",
                'index.php/restaurante/:restaurantName(/)' :'restaurantDetail',
                'index.php/plato/:menuName(/)' :'menuDetail',
                'index.php/buscar-restaurantes(/:cityId)(/:text)(/:menuType)(/:lat)(/:lon)(/:zoom)/' :'search'
            },
            newRestaurant : function(id)
            {
                this.currentView = new NewRestaurantView({el : this.defaultEl, id : id != undefined ? parseInt(id) : undefined });
            },
            newMenu : function(id)
            {
                this.currentView = new NewMenuView({el : this.defaultEl, id : id != undefined ? parseInt(id) : undefined });
            },
            restaurantDetail : function(){
                this.currentView = new RestaurantDetailView({el : this.defaultEl});
            },
            menuDetail : function(){
                this.currentView = new MenuDetailView({el : this.defaultEl});
            },
            search : function(cityId,text,menuType,lat,lon,zoom){
                this.currentView = new SearchRestaurantView({el : this.defaultEl,
                    preselectedFilter :{
                    cityId :cityId == 'undefined' ? undefined : cityId,
                    text : text == 'undefined' ? undefined : text,
                    menuType : menuType== 'undefined' ? undefined : menuType,
                    lat :lat== 'undefined' ? undefined : lat,
                    lon :lon== 'undefined' ? undefined : lon,
                    zoom:zoom== 'undefined' ? undefined : zoom
                }});
            }
        });

        return MecatoRouter;
    });

