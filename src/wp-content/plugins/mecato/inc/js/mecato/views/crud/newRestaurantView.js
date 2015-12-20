/**
 * Created by Beto on 12/19/2015.
 */
define(['jquery', 'underscore', 'baseView', 'async!maps'],
    function($, _, BaseView){

        var NewRestaurantView = BaseView.extend({
            lat : undefined,
            lon : undefined,
            map: undefined,
            initialize : function(){
                this.getCurrentLocation();
            },
            getCurrentLocation: function () {
                if (navigator.geolocation) {
                    var that = this;
                    navigator.geolocation.getCurrentPosition(function (position) {
                        that.lat = position.coords.latitude;
                        that.lon = position.coords.longitude;
                        that.updateLocation(true);
                    });
                }
                else {
                    //Si no tiene geolocalización lo ubica en la posición por defecto
                    this.updateLocation();
                }
            },
            updateLocation: function (zoom) {
                var latlng = new google.maps.LatLng(this.lat, this.lon);
                var myOptions = {
                    zoom: zoom ? 15 : 12,
                    center: latlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                this.map = new google.maps.Map(this.$('#map_location')[0], myOptions);
                //this.loadCity();
            },
        });

        return NewRestaurantView;
});