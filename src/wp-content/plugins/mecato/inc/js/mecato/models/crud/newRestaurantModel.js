/**
 * Created by Beto on 12/21/2015.
 */
define(['underscore', 'backbone'],
    function (_, Backbone) {

        'use strict'

        var NewRestaurantModel = Backbone.Model.extend({
            baseUrl: "/api/products",
            url: "/api/products",
            idAttribute :'Id',
            initialize : function(){

            },
            validation: {
                name :{
                    required:true
                },
                address :{
                    required:true
                },
                lat : {
                    required:true
                },
                lon : {
                    required:true
                }
            },
            labels: {
                name: 'Nombre',
                address : 'Direcci�n',
                phone :'Tel�fono',
                schedule : 'Horario',
                lat : 'Latitud',
                lon : 'Longitud',
                description :'Descripci�n'
            }
        });

        return NewRestaurantModel;
    });



