require.config({
    baseUrl: '/wp-content/plugins/mecato/inc/js/',
    waitSeconds : 15,
    shim: {
        /*jquery: {
            exports: ['jQuery', '$']
        },*/
        validations: {
            deps: ['backbone'],
            exports: 'Backbone'
        },
        jqueryui :{
            deps:['jquery']
        },
        bootstrap :{
            deps:['jquery']
        },
       /* simpleMenu: {
            deps: ['modernizr', 'jquery'],
            exports: 'simpleMenu'
        }*/
    },
    paths: {
        jquery: [
            '/wp-includes/js/jquery/jquery.js?ver=1.11.3'
        ],
        jqueryui: [
            '/wp-includes/js/jquery/ui/core.min.js?ver=1.11.4',
            'jquery-ui-1.10.3.custom.min'
         ],
        /*jqueryui: [
            'http://code.jquery.com/ui/1.11.4/jquery-ui.min',
            'jquery-ui-1.10.3.custom.min'
        ],*/
        handlebars: [
            'https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/3.0.3/handlebars.amd.min',
            'handlebars.min'
        ],
        bootstrap : [
            '/wp-content/themes/dazzling/inc/js/bootstrap.min.js?ver=4.4'
        ],

        jqueryvalidate: 'jquery.validate.min',
        underscore: 'underscore-min',
        backbone : 'backbone',
        stickit: 'backbone.stickit.min',
        validations:'backbone-validation.min',
        //handlebars: 'handlebars.min',
        //accounting: 'accounting.min',
        dropZone : 'lib/dropzone.amd',


        modernizr: 'modernizr.custom.min',


        maps: 'http://maps.google.com/maps/api/js?sensor=false',
        handlebarsh: 'handlebars.helpers',
        //Basic Tuils
        router: 'mecato/router',
        baseView : 'mecato/views/baseView'
        //configuration: 'tuils/configuration',
        //resources: 'tuils/resources',
        //storage: 'tuils/storage',
        //util: 'tuils/utilities',
    }

});


jQuery(function() {
    require(['mecato/app'], function (MecatoApp) {
        window.$ = jQuery;
        MecatoApp.init();
    });
});

