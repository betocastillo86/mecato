/**
 * Created by Beto on 1/11/2016.
 */
define(['jquery', 'underscore', 'baseView', 'handlebars'
        , 'handlebarsh'],
    function ($, _, BaseView, Handlebars) {

        var SearchRestaurantFilterListView = BaseView.extend({
            events: {
                'click .office-box' : 'select',
                'click #btnShowNearest' : 'showNearest'
            },
            bindings: {

            },
            templateList: undefined,
            selectedRestaurant : undefined,
            //Número total de restaurantes que hay de acuerdo al filtro
            //NO son el numero de restaurantes que se están mostrando
            totalRestaurants : 0,
            initialize: function (args) {
                this.loadControls();
            },
            loadControls: function (args) {
                this.templateList = Handlebars.compile($('#templateFilteredList').html());
            },
            showRestaurants: function (collection) {
                this.totalRestaurants = collection.total;
                this.collection = collection.list;
                this.$el.html(this.templateList({list : this.collection, total : this.totalRestaurants }));
                this.markSelectedRestaurant();
            },
            selectRestaurant: function (restaurant) {
                this.selectedRestaurant = restaurant;
                this.markSelectedRestaurant();
            },
            markSelectedRestaurant: function () {
                this.$('.office-box').removeClass('active').css('border', '');
                if (this.selectedRestaurant) {
                    this.$('.office-box[data-id="' + this.selectedRestaurant.id + '"]').addClass('active');
                }
            },
            showNearest : function(){
                this.trigger('show-nearest');
            },
            select: function (obj) {
                //Lanza el evento que la sede ha sido seleccionada ddesde la lista
                var id = parseInt(obj.currentTarget.attributes['data-id'].value);
                this.selectedRestaurant = _.findWhere(this.collection, { id: id });
                this.trigger('selected', this.selectedRestaurant);
                this.markSelectedRestaurant();
            },
            render: function () {
                return this;
            }
        });

        return SearchRestaurantFilterListView;
    });