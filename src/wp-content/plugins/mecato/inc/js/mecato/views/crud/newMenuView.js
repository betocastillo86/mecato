/**
 * Created by Beto on 12/19/2015.
 */
define(['jquery', 'underscore', 'baseView', 'mecato/models/crud/newMenuModel', 'dropZone'],
    function($, _, BaseView, NewMenuModel, Dropzone){

        var NewMenuView = BaseView.extend({

            events:{
                'click #btnNewService' : 'save',
                'click .btn[data-radio-name]' : 'changeRadio'
            },

            bindings :{
                '#menu_name' : 'name',
                '#menu_description' : 'description',
                '#menu_price' : 'price',
                '#menu_type' :'type'
            },


            initialize : function(args){
                this.id = args.id;
                this.model = new NewMenuModel();

                //Actualiza el valor del modelo si está editando
                if(this.id != undefined)
                {
                    this.model.set('Id', this.id);
                    this.loadImages();
                }

                this.render();
            },
            loadImages : function(){
                Dropzone.autoDiscover = false;
                var that = this;
                this.$('.dropzone').dropzone(
                    {
                        url: '/index.php/wp-json/api/menus/'+that.id+'/images',
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
            changeRadio: function(obj){
                obj = $(obj.currentTarget);
                var radioName = obj.data('radioName');
                this.$('.btn[data-radio-name="'+radioName+'"]').removeClass('active');
                this.$('input[name="'+radioName+'"]').val(obj.text());
                console.log(this.$('input[name="'+radioName+'"]').val());
            },
            save : function(){
                this.validateControls();
                if(this.model.isValid())
                    this.$('form').submit();
            },
            render : function(){
                this.stickThem(true);
                this.bindValidation();
            }
        });

        return NewMenuView;
    });