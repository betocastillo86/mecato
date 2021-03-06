/**
 * Created by Beto on 1/3/2016.
 */
define(['jquery', 'underscore', 'baseView', 'mecato/views/search/searchRestaurantMapView', 'mecato/views/search/searchRestaurantFilterView', 'mecato/views/search/searchRestaurantFilterListView'],
    function ($, _, BaseView, SearchRestaurantMapView, SearchRestaurantFilterView, SearchRestaurantFilterListView) {

        var SearchRestaurantView = BaseView.extend({

            events :{
                'click #btnShowFilter' : 'showRespFilter',
                'click #btnShowFilterList' : 'showRespFilterList'
            },
            map: undefined,
            filter: undefined,
            filterList: undefined,
            preselectedFilter: undefined,
            urlValues : undefined,

            initialize: function (args) {
                this.loadControls(args);
            },
            loadControls: function (args) {
                this.preselectedFilter = {
                    cityId: args.preselectedFilter.cityId,
                    menuType: args.preselectedFilter.menuType,
                    text: args.preselectedFilter.text,
                    lat: args.preselectedFilter.lat,
                    lon: args.preselectedFilter.lon,
                    zoom: args.preselectedFilter.zoom
                };

                this.map = new SearchRestaurantMapView({ el: '#divMap', lat :args.preselectedFilter.lat, lon: args.preselectedFilter.lon, zoom:args.preselectedFilter.zoom  });
                this.map.on('set-city', this.cityLoaded, this);
                this.map.on('list-filtered', this.listFiltered, this);
                this.map.on('selected', this.restaurantSelected, this);
                this.urlValues = new Object();
                this.showHelp();
            },
            //Cuando el mapa carga puede cargar ahora el filtro
            cityLoaded: function (location) {
                this.loadFilter(location);
            },
            showHelp : function()
            {
                if(this.$('#hidShowMenuHelp').length)
                    this.showMenuHelp();
                if(this.$('#hidShowSearchHelp').length)
                    this.showSearchHelp();
            },
            showMenuHelp : function()
            {
                this.alert({title : 'Lee esto antes de empezar', message :this.$('#templateMenuHelp').html()});
            },
            showSearchHelp : function()
            {
                this.alert({title : 'Lee esto antes de empezar', message :this.$('#templateSearchHelp').html()});
            },
            loadFilter: function (location) {
                this.filter = new SearchRestaurantFilterView({ el: '#divFilter', location: location, preselectedFilter : this.preselectedFilter });
                this.filter.on('list-loaded', this.loadMap, this);
            },
            showRespFilter : function(){
                this.filter.showResp();
            },
            showRespFilterList : function(){
                this.filterList.showResp();
            },
            //evento disparado cuando se actualiza la lista de oficinas de acuerdo al mapa actual
            listFiltered: function (restaurants) {
                if (!this.filterList)
                {
                    this.filterList = new SearchRestaurantFilterListView({ el: '#divFilteredList' });
                    this.filterList.on('selected', this.restaurantSelectedFromList, this);
                    this.filterList.on('show-nearest', this.map.showNearest, this.map);
                }

                this.filterList.showRestaurants({ list: restaurants.restaurants, total : restaurants.totalMarks });

                //Actualiza la posicion en el mapa y la navegacion
                this.urlValues['lat'] = restaurants.lat;
                this.urlValues['lon'] = restaurants.lon;
                this.urlValues['zoom'] = restaurants.zoom;
                Backbone.history.navigate('/buscar-restaurantes/'
                    + this.urlValues.cityId + '/'
                    + this.urlValues.text + '/'
                    + this.urlValues.menuType + '/'
                    + this.urlValues.lat + '/'
                    + this.urlValues.lon + '/'
                    + this.urlValues.zoom
                );
            },
            restaurantSelected: function (restaurant) {
                this.filterList.selectRestaurant(restaurant);
            },
            restaurantSelectedFromList: function (restaurant) {
                this.map.selectRestaurant(restaurant);
            },
            loadMap: function (response) {
                this.urlValues['cityId'] = response.filter.cityId;
                this.urlValues['menuType'] = response.filter.menuType;
                this.urlValues['text'] = response.filter.text;
                this.map.showRestaurants({ list: response.list.toJSON(), city: response.city });
            },
        });

        return SearchRestaurantView;
    });

