/**
 * Created by Beto on 1/10/2016.
 */
define(['jquery', 'underscore', 'backbone'],
    function ($, _, Backbone) {
        var ConfirmMessageView = Backbone.View.extend({

            //tagName : 'div',

            message: undefined,

            title : 'Aviso',

            elBody : undefined,

            elTitle : undefined,

            autoclose: true,

            height: 150,

            afterClose: function () { },

            duration:3000,

            initialize: function (args) {
                if (args && args.autoclose)
                    this.autoclose = args.autoclose;

                if (args && args.duration)
                    this.duration = args.duration;

                this.elTitle = this.$el.find('.modal-title');
                this.elBody = this.$el.find('.modal-body');

                this.render();
            },
            loadControls: function () {
                var that = this;
                /*this.$el.dialog({
                    autoOpen : false,
                    modal: true,
                    height: that.height,
                    hide: {
                        effect: 'fade',
                        duration: 200
                    },
                    buttons: {
                        'Aceptar': function () {
                            $(this).dialog('close');
                        }
                    },
                    open: function (event, ui) {
                        if (that.autoclose)
                        {
                            setTimeout(function () {
                                that.$el.dialog('close');
                            }, that.duration);
                        }
                    },
                    beforeClose: function () {
                        $('body').removeClass('body-noscroll');
                    },
                    close: function () {
                        that.afterClose();
                    }

                });*/

            },
            show: function (args) {
                /*if (!this.$el.hasClass('ui-dialog-content'))
                {
                    this.loadControls();
                }*/

                var message = '';
                if (typeof (args) == 'string')
                    message = args;
                if (args.message)
                    message = args.message;
                if (args.title)
                    this.title = args.title;

                if (args.autoclose != undefined)
                    this.autoclose = args.autoclose;

                if (args.duration)
                    this.duration = args.duration;

                if (args.height)
                {
                    this.height = args.height;
                    this.$el.dialog("option", "height", this.height);
                }


                if (args && args.afterClose)
                    this.afterClose = args.afterClose;


                this.elTitle.html(this.title);
                this.elBody.html(message);

                //this.$el.html(message)

                //$('body').addClass('body-noscroll');
                //this.$el.dialog('open');

                this.$el.modal('show');

            },
            render: function () {
                //this.loadControls();
                return this;
            }
        });
        return ConfirmMessageView;
    });