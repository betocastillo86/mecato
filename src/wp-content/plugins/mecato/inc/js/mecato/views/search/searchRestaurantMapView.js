/**
 * Created by Beto on 1/3/2016.
 */
define(['jquery', 'underscore', 'baseView',  'handlebars'],
    function ($, _, BaseView,  Handlebars) {

        var SearchRestaurantMapView = BaseView.extend({

            lat: 4.57262365310281,
            lon: -74.0970325469971,

            //Usado para localizar las direcciones
            geocoder: undefined,

            templateInfo: undefined,

            infoWindow : undefined,

            marks: [],

            currentCity : 0,

            initialize: function (args) {
                this.loadEmptyMap(args);
                this.templateInfo = Handlebars.compile($('#templateMapInfoRest').html());
                this.infoWindow = new google.maps.InfoWindow({ content: '' });
                /*var that = this;
                google.maps.event.addListener(this.infoWindow, 'closeclick', function () {
                    that.trigger('selected', undefined);
                });*/
            },

            loadEmptyMap: function (args) {
                if (args.lat && args.lon && args.zoom) {
                    this.lat = parseFloat(args.lat);
                    this.lon = parseFloat(args.lon);
                    this.updateLocation(parseInt(args.zoom));
                }
                else {
                    //Si no viene previamente seleccionada la posición del mapa carga la actual
                    this.getCurrentLocation();
                }

            },

            getCurrentLocation: function () {
                if (navigator.geolocation) {
                    var that = this;
                    navigator.geolocation.getCurrentPosition(function (position) {
                        that.lat = position.coords.latitude;
                        that.lon = position.coords.longitude;
                        that.updateLocation(15);
                    });
                }
                else {
                    //Si no tiene geolocalización lo ubica en la posición por defecto
                    this.updateLocation(12);
                }
            },
            updateLocation: function (zoom) {
                var latlng = new google.maps.LatLng(this.lat, this.lon);
                var myOptions = {
                    zoom: zoom,
                    center: latlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                this.map = new google.maps.Map(this.$el[0], myOptions);
                this.loadCity();

                var that = this;
                google.maps.event.addListener(this.map, 'idle', function () {
                    that.updateFilterList();
                });
            },
            loadCity: function () {
                var that = this;
                var latlng = new google.maps.LatLng(this.lat, this.lon);

                if (!this.geocoder)
                    this.geocoder = new google.maps.Geocoder();

                this.geocoder.geocode({ 'latLng': latlng }, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            //Busca de acuerdo a la geolocalización actual, cual es la ciudad a la que pertenece
                            var cityName = '';
                            _.each(results[0].address_components, function (element, index) {
                                if (_.contains(element.types, 'administrative_area_level_1')) {
                                    cityName = element.long_name;
                                    return;
                                }
                            });

                            that.trigger('set-city', { cityName: cityName });
                        } else {
                            alert('Map:No results found');
                        }
                    } else {
                        alert('Map:Geocoder failed due to: ' + status);
                    }
                });
            },
            placeMarker: function (restaurant, id) {

                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(restaurant.lat, restaurant.lon),
                    map: this.map,
                    icon: '/wp-content/plugins/mecato/inc/img/menuIcons/' + (restaurant.numVegan > 0 ? 'vegano' : 'vegetariano') + 'Map.png'
                });

                marker.restaurant = restaurant;
                marker.restaurantId = restaurant.id;
                //Agrega el evento del clic
                var that = this;
                google.maps.event.addListener(marker, 'click', function () {
                    that.infoWindow.setContent(that.templateInfo(this.restaurant));
                    that.infoWindow.open(that.map, this);
                    that.trigger('selected', this.restaurant);
                });

                return marker;
                //this.markersArray.push(marker);
            },
            updateFilterList: function () {
                var that = this;

                var restaurantsOnList = [];
                var currentBounds = this.map.getBounds();
                _.each(this.marks, function (element, index) {
                    //Si ningun punto se ha mostrado hasta ahora sigue validando hasta cambiar la bandera
                    if (currentBounds.contains(element.position))
                    {
                        restaurantsOnList.push(element.restaurant);
                    }
                });
                this.trigger('list-filtered', { restaurants: restaurantsOnList, totalMarks : this.marks.length, lat: this.map.getCenter().lat(), lon: this.map.getCenter().lng(), zoom: this.map.getZoom() });
            },
            showRestaurants: function (response) {

                var restaurants = response.list;

                //Limpia las marcas antes de cargarlas de nuevo
                this.clearMarks();
                var that = this;

                //Varibale que controla si se está mostrando o no algún punto
                var isShowingPoint = false;
                var currentBounds = this.map.getBounds();

                _.each(restaurants, function (element, index) {
                    // var location = new google.maps.LatLng(element.lat, element.lon);
                    var mark = that.placeMarker(element);
                    that.marks.push(mark);
                });


                if (!that.marks.length)
                    this.alert('No hay resultados que coincidan con tu busqueda');
                else
                {
                    //Si hay resultados y la ciudad cambió se debe mover hacia la ciudad
                    if(this.currentCity && this.currentCity != response.city)
                        this.map.setCenter(that.marks[0].position);
                }

                //Actualiza la ciudad
                this.currentCity = response.city;
                this.updateFilterList();
            },
            selectRestaurant: function (restaurant) {
                var mark = _.findWhere(this.marks, { restaurantId: restaurant.id });
                if (mark) {
                    this.infoWindow.setContent(this.templateInfo(restaurant));
                    this.infoWindow.open(this.map, mark);
                }
            },
            showNearest : function(){
                this.map.setCenter(this.marks[0].position);
            },
            clearMarks: function () {
                _.each(this.marks, function (element, index) {
                    element.setMap(null);
                });
                this.marks = [];
            }

        });

        return SearchRestaurantMapView;
    });
