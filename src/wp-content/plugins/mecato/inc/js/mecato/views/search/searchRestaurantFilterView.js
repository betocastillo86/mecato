/**
 * Created by Beto on 1/3/2016.
 */
define(['jquery', 'underscore', 'baseView', 'handlebars', 'mecato/models/search/searchRestaurantModel', 'mecato/collections/search/searchRestaurantCollection'],
    function ($, _, BaseView, Handlebars, SearchRestaurantModel, SearchRestaurantCollection) {

        var SearchRestaurantFilterView = BaseView.extend({
            events: {
                'click a[data-radio-name="menu_type"]': 'filterByMenuType',
                'click #divSelectedFilter .tagit-close' : 'closeSelectedItem',
                'click #closeFilter' : 'close'
            },
            bindings: {
                '#ddlCity' : {
                    observe : 'cityId',
                    onSet : function(value, ctx){
                        //Al cambiar de ciudad limpia el texto pero no lanza evento de cambio
                        ctx.view.model.set({'text' : undefined}, {silent :true});
                        ctx.view.$('#txtFilter').val('');
                        return value;
                    }
                },
                '#txtFilter' :{
                    observe : 'text',
                    events :['change']
                }

            },
            //Ubicacion principal del mapa
            mapLocation: undefined,

            preselectedFilter : undefined,

            templateSelected: undefined,

            templateCount : undefined,

            listCategories : [],

            initialize: function (args) {
                this.preselectedFilter = args.preselectedFilter;

                //Carga el filtro
                this.model = new SearchRestaurantModel();
                this.model.on('change', this.searchRestaurants, this);

                //La colección de direcciones que vienen en el filtro
                this.collection = new SearchRestaurantCollection();
                this.collection.on('sync', this.officesLoaded, this);

                this.mapLocation = args.location;
                this.loadControls(args);

                //Carga las caregorias de los productos para posteriormente cargarlas como opciones
                //TuilsStorage.loadProductCategories(this.loadOptionCategories, this);
                //TuilsStorage.loadServiceCategories(this.loadOptionCategories, this);

                this.render();
            },
            loadControls: function (args) {

                //Si habia un filtro previamente seleccionado lo carga
                if (this.preselectedFilter.cityId)
                    this.loadPrefilter();
                else
                //La ubicación principal de la ciudad debe venir por defecto
                    this.setCity(this.mapLocation);

                this.templateCount = Handlebars.compile($('#templateRestCount').html());
                this.templateSelected = Handlebars.compile($('#templateSelectedItem').html());
            },
            loadPrefilter: function () {
                //Asigna las propiedades al filtro
                this.model.set({
                    cityId : this.preselectedFilter.cityId,
                    menuType: this.preselectedFilter.menuType,
                    text: this.preselectedFilter.text
                });
                //Selecciona el subtipo
                if (this.preselectedFilter.menuType)
                    this.$('a[data-radio-name="menu_type"][data-id="' + this.preselectedFilter.menuType + '"]').addClass('active');

            },
            selectFilterText: function (args) {
                this.$('#txtFilter').hide();
                this.$('#divSelectedFilter').html(this.templateSelected(args));
            },
            //Cuando el mapa carga selecciona la posición
            setCity: function (location) {

                var selectedCity = this.$("#ddlCity option").filter(function () {
                    return this.text == location.cityName;
                });

                if (selectedCity.length)
                    this.model.set('cityId', selectedCity.val());
            },
            filterByMenuType: function (obj) {
                obj = $(obj.currentTarget);
                var subTypeSelected = obj.attr('data-id');

                if (obj.hasClass('activePush'))
                {
                    //Si el botón se está desactivando invierte el tipo de que se desea filtrar
                    obj.removeClass('activePush');
                    subTypeSelected = subTypeSelected == window.menuTypes.vegan ? window.menuTypes.vegetarian : window.menuTypes.vegan;
                }
                else
                    obj.addClass('activePush');

                //Si ya se habia seleccionado algún subtipo para filtrar, se manda a null xq significa que no se va a filtrar incluyentemente
                if (this.model.get('menuType'))
                    this.model.set('menuType', undefined);
                else
                    this.model.set('menuType', subTypeSelected);
            },
            showResp : function(){
                this.$('#divFilterOptions').show();
            },
            closeSelectedItem: function () {
                this.$('#divSelectedFilter').empty();
                this.$('#txtFilter').show().val('');
                this.model.set({ CategoryId : undefined, VendorId : undefined })
            },
            searchRestaurants: function () {
                this.showLoadingAll(this.collection);
                this.collection.searchRestaurants(this.model.toJSON());
            },
            close : function(){
                this.$('#divFilterOptions').hide();
            },
            officesLoaded: function () {



                this.$('#divVendorResult').html(this.templateCount(this.collection.length))
                    .removeClass('alert-success')
                    .removeClass('alert-danger')
                    .addClass(this.collection.length ? 'alert-success' : 'alert-danger');

                this.trigger('list-loaded', { list: this.collection, city: this.model.get('cityId'), filter : this.model.toJSON() });
            },
            render: function () {
                this.stickThem();
            }
        });

        return SearchRestaurantFilterView;
    });