/**
 * Created by Beto on 12/19/2015.
 */
define(['jquery', 'underscore', 'baseView', 'mecato/models/crud/newMenuModel', 'dropZone'],
    function($, _, BaseView, NewMenuModel, Dropzone){

        var NewMenuView = BaseView.extend({

            events:{
                'click #btnNewService' : 'save',
                'click .btn[data-radio-name]' : 'changeRadio',
                'click #aMoreTopics' : 'showMoreTopics'
            },

            bindings :{
                '#menu_name' : 'name',
                '#menu_description' : 'description',
                '#menu_price' : 'price',
                '#menu_type' :'type',
                '#menu_topics' :'topics'
            },

            currentDivTopic :0,


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
                var valueId = obj.data('id');
                this.$('.btn[data-radio-name="'+radioName+'"]').removeClass('active');

                if(radioName == 'menu_type')
                    this.model.set('type', valueId);
            },
            showMoreTopics : function(){
                this.currentDivTopic++;
                var divsToShow = this.$('.listTopics[data-div="'+this.currentDivTopic+'"]');
                if(divsToShow.length)
                    divsToShow.show();

                //Oculta el siguiente en caso que no existan más
                if(!this.$('.listTopics[data-div="'+(this.currentDivTopic+1)+'"]').length)
                    this.$('#aMoreTopics').hide();
            },
            updateTopics : function(){
                var topics = '';
                _.each($('.listTopics button.yesTopic.active'), function(element, index){
                    if(topics.length > 0) topics += ',';
                    topics += $(element).data("id");
                });
                this.model.set('topics', topics);
            },
            save : function(){
                this.updateTopics();
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