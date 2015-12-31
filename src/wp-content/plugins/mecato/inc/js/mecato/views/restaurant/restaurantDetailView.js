/**
 * Created by Beto on 12/29/2015.
 */
/**
 * Created by Beto on 12/19/2015.
 */
define(['jquery', 'underscore', 'baseView',
        'async!maps'],
    function($, _, BaseView){

        var RestaurantDetailView = BaseView.extend({

            events:{
            },

            bindings :{
            },
            marker : undefined,
            map : undefined,

            initialize : function(args){
                this.updateLocation();
                this.render();
            },
            updateLocation: function (zoom) {
                var latlng = new google.maps.LatLng(this.$('#rest_lat').val(), this.$('#rest_lon').val());
                var myOptions = {
                    zoom: 15,
                    center: latlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                this.map = new google.maps.Map(this.$('#divMap')[0], myOptions);

                this.marker = new google.maps.Marker({
                    position: latlng,
                    map: this.map
                });
            },
            render : function(){
                return this;
            }
        });

        return RestaurantDetailView;
    });