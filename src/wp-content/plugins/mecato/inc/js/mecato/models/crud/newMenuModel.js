/**
 * Created by Beto on 12/21/2015.
 */
define(['underscore', 'backbone'],
    function (_, Backbone) {

        'use strict'

        var NewMenuModel = Backbone.Model.extend({
            baseUrl: "/api/restaurant/menu",
            url: "/api/restaurant/menu",
            idAttribute :'Id',
            initialize : function(){

            },
            validation: {
                name :{
                    required:true
                },
                description :{
                    required:true
                },
                type : {
                    required:true
                }
            },
            labels: {
                name: 'Nombre',
                description :'Descripción',
                price : 'Precio',
                type :'Tipo de Plato'
            }
        });

        return NewMenuModel;
    });



