/**
 * Created by Beto on 12/19/2015.
 */
define(['jquery', 'underscore', 'baseView', 'mecato/models/crud/newRestaurantModel', 'dropZone',
        'jquery.weekline.min','async!maps'],
    function($, _, BaseView, NewRestaurantModel, Dropzone){

        var NewRestaurantView = BaseView.extend({

            events:{
                'change .timeHourSchedule': 'updateSchedule',
                'click #btnNewService' : 'save'
            },

            bindings :{
                '#restaurant_name' : 'name',
                '#restaurant_address' : 'address',
                '#restaurant_phone' : 'phone',
                '#restaurant_lat' : 'lat',
                '#restaurant_lon' : 'lon',
                '#restaurant_schedule' : 'schedule'

            },
            id : undefined,
            lat : undefined,
            lon : undefined,
            map: undefined,
            dayLabels : ["Lun", "Mar", "Mie", "Jue", "Vie", "Sab", "Dom"],
            marker : undefined,
            geocoder : undefined,

            initialize : function(args){
                this.id = args.id;
                this.model = new NewRestaurantModel();

                //Actualiza el valor del modelo si está editando
                if(this.id != undefined)
                {
                    this.model.set('Id', this.id);
                    this.loadSchedule();
                    this.loadImages();
                }
                else
                {
                    this.getCurrentLocation();
                }


                this.render();
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
            loadImages : function(){
                Dropzone.autoDiscover = false;
                var that = this;
                this.$('.dropzone').dropzone(
                    {
                        url: '/index.php/wp-json//api/restaurants/'+that.id+'/images',
                        complete: function (a, b, c) {
                            //debugger;
                            /*if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                                that.lista.fetch();
                            }*/
                            console.log(a);
                        },
                        completemultiple : function(){
                            that.$('#messageUploadOk').show();
                        },
                        error : function(a, b, c){
                            that.$('#messageUploadError').show();
                        }
                    }
                );
            },
            loadSchedule: function () {
                var that = this;
                this.weekSelector = this.$('.spanSchedule').weekLine({
                    dayLabels: that.dayLabels,
                    onChange: function () {
                        that.updateSchedule();
                    }
                });

            },
            updateSchedule: function () {

                var days = this.weekSelector.weekLine('getSelected', 'descriptive');
                var hours = this.$('#txtOpenHour').val() + '-' + this.$('#txtCloseHour').val();
                this.$('#restaurant_schedule').val(days+ ' ' + hours);
            },
            updateLocation: function (zoom) {
                var latlng = new google.maps.LatLng(this.lat, this.lon);
                var myOptions = {
                    zoom: zoom ? 15 : 12,
                    center: latlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                this.map = new google.maps.Map(this.$('#map_location')[0], myOptions);

                var that = this;
                // add a click event handler to the map object
                google.maps.event.addListener(this.map, "click", function (event) {
                    // place a marker
                    if(!that.marker)
                    {
                        that.marker = new google.maps.Marker({
                            position: event.latLng,
                            map: that.map
                        });
                    }
                    else
                    {
                        that.marker.setPosition(event.latLng);
                    }
                    that.setAddress(event.latLng);
                    // display the lat/lng in your form's lat/lng fields
                    that.model.set('lat', event.latLng.lat());
                    that.model.set('lon', event.latLng.lng());
                });

                //this.loadCity();
            },
            setSchedule: function () {
                if(!this.model.get('schedule') ||this.model.get('schedule').length == 0)
                    return;

                var scheduleParts = this.model.get('schedule').replace(/,\s/g,',').split(' ');
                var days = scheduleParts[0];
                var openHour = scheduleParts[1].split('-')[0];
                var closeHour = scheduleParts[1].split('-')[1];
                var that = this;


                //Toma varios dias unidos y los convierte a dias separados
                function getDaysByComma(jointDays)
                {
                    //Busca dias unidos
                    var splitDays = jointDays.split('-');
                    if(splitDays.length > 1)
                    {
                        //Toma el primer y ultimo dia para calcular los que están implicitos
                        var firstDay = splitDays[0];
                        var lastDay = splitDays[1];
                        var days = '';
                        var found = false;
                        _.each(that.dayLabels, function(element, index){
                            if(element == firstDay)
                            {
                                days = element;
                                found = true;
                            }
                            else if(found && element == lastDay)
                            {
                                days += ','+element;
                                found = false;
                            }
                            else if(found)
                            {
                                days += ','+element;
                            }
                        });

                        return days;
                    }
                    else
                    {
                        return jointDays;
                    }
                }

                var daysComma = '';
                _.each(days.split(','), function (element, index) {
                    var append = getDaysByComma(element);
                    if (append != '')
                    {
                        if (daysComma.length > 0)
                            daysComma += ',';
                        daysComma += append;
                    }
                });


                this.weekSelector.weekLine('setSelected', daysComma);
                this.model.set('Days', this.weekSelector.weekLine('getSelected', 'descriptive'));
                this.$('#txtOpenHour').val(openHour);
                this.$('#txtCloseHour').val(closeHour);
            },
            setAddress: function (latLng) {
                if(!this.geocoder)
                    this.geocoder = new google.maps.Geocoder();
                var that = this;
                this.geocoder.geocode({ 'latLng': latLng }, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            //that.trigger('set-address', results[0].formatted_address);
                            //Se hace un format para las direcciones de Colombia que son más complejas
                            /*var cityName = '';
                            _.each(results[0].address_components, function (element, index) {
                                if (_.contains(element.types, 'administrative_area_level_1'))
                                {
                                    cityName = element.long_name;
                                    return;
                                }
                            });

                            that.trigger('set-address', { address: results[0].formatted_address.split(' a ')[0], cityName : cityName });*/
                            that.model.set('address', results[0].formatted_address.split(' a ')[0]);
                        } else {
                            alert('No results found');
                        }
                    } else {
                        alert('Geocoder failed due to: ' + status);
                    }
                });
            },
            save : function(){
                var errors = this.validateControls();
                if(this.model.isValid())
                    this.$('form').submit();
                else
                {
                    if(errors.lat)
                        alert('Selecciona la posición en el mapa');
                }
            },
            render : function(){
                this.stickThem(true);
                this.bindValidation();

                if(this.id != undefined)
                    this.setSchedule();
            }
        });

        return NewRestaurantView;
});