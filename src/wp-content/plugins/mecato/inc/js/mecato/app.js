/**
 * Created by Beto on 12/19/2015.
 */
define(['underscore', 'backbone', 'router', 'validations'],
    function (_, Backbone, MecatoRouter, Val) {
        var MecatoApp = {
            router: undefined,

            init: function () {
                MecatoApp.router = new MecatoRouter();
                Backbone.history.start({ pushState: true });
                Backbone.Validation.configure({ labelFormatter: 'label' });
            }
        }

        Backbone.Validation = Val;
        _.extend(Backbone.Validation.messages, {
            required: 'Campo {0} es obligatorio',
            acceptance: '{0} deben ser aceptados',
            min: 'Campo {0} debe ser mayor o igual a {1}',
            max: 'Campo {0} debe ser menor o igual a {1}',
            range: '{0} de ser entre {1} y {2}',
            length: '{0} debe tener {1} caracteres',
            minLength: '{0} debe tener por lo menos {1} caracteres',
            maxLength: '{0} debe tener máximo {1} caracteres',
            rangeLength: '{0} debe estar entre {1} y {2} caracteres',
            oneOf: '{0} debe ser uno de: {1}',
            equalTo: '{0} debe ser igual a {1}',
            digits: '{0} solo puede tener números',
            number: '{0} debe ser un número',
            email: '{0} debe ser un correo válido',
            url: '{0} debe ser una url',
            inlinePattern: '{0} no es válido'
        });

        return MecatoApp;
    });


