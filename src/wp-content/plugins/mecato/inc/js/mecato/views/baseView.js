/**
 * Created by Beto on 12/19/2015.
 */
define(['underscore', 'backbone'],
    function (_, Backbone) {

        var BaseView = Backbone.View.extend({

            viewConfirm : undefined,

            _isMobile: undefined,

            minSizeDesktopWith: 1024,

            minSizeMobileWith: 400,

            //prebind: actualiza los valores del modelo de acuerdo a los controles
            stickThem: function (preBind) {

                if (preBind && this.bindings != undefined)
                {
                    var that = this;
                    _.each(this.bindings, function (element, index) {
                        //Si es un objeto busca la propiedad en el campo observe

                        var selector;
                        var field;

                        if (_.isObject(element)) {
                            var bind = true;
                            //Si se ha puesto la propiedad de preBind valida que este en true para cargarlo, sino no lo hace
                            if (element['preBind'] != undefined)
                                bind = element['preBind'];

                            if (!bind)
                                return;

                            field = element['observe'];
                            selector = element['controlToMark'] ? element['controlToMark'] : index;
                        }
                        else {
                            field = element;
                            selector = index;
                        }

                        //Actualiza el valor del modelo dependiendo de los valores existentes en el formulario
                        that.model.set(field, that.$(selector).val());
                    });
                }


                this.stickit();
                this.basicValidations();
            },
            basicValidations : function()
            {
                //agrega las caracteristicas de tipos de datos a los combos
                this.$("input[data-val='int']").on("keypress", TuilsUtil.onlyNumbers);
                this.$("input[data-val='none']").on("keypress", function () { return false; });
            },
            //Muestra el cargando en toda la pantalla
            showLoadingAll: function (model) {
                model = model ? model : this.model;
                model.once("sync", this.removeLoading, this);
                model.once("error", this.removeLoading, this);
                model.once("unauthorized", this.removeLoading, this);

                displayAjaxLoading(true);
            },
            handleResize : function(){
                var that = this;
                jQuery(window).resize(function () {
                    // get browser width
                    if (!that.isMinSize())
                        that.trigger("window-resized-max");
                    else
                        that.trigger("window-resized-min");
                });
            },
            removeLoading : function(){
                this.$el.find("#divLoadingback").remove();
                this.$el.find('.loadingBack').removeClass('loadingBack');
                displayAjaxLoading(false);
            },
            //Muestra un mesaje de alerta ya sea con un Resource o con el mensaje directamente
            alert: function (args) {
                if (!this.viewConfirm)
                    this.viewConfirm = new ConfirmMessageView();

                this.viewConfirm.show(args);
            },
            validateControls: function (model, goToFocus) {
                //Formatea los mensajes de respuesta contra los label

                this.removeErrors();

                if (!model)
                    model = this.model;

                var errors = model.validate();

                //Si notiene bindings no valida los campos
                if (this.bindings) {
                    var that = this;

                    //invierte los bindings para obtener todos los campos y objetos del formulario
                    var fieldsToMark = new Object();
                    _.each(that.bindings, function (element, index) {
                        //Si es un objeto busca la propiedad en el campo observe
                        if (_.isObject(element)) {
                            fieldsToMark[element['observe']] = element['controlToMark'] ? element['controlToMark'] : index;
                        }
                        else {
                            fieldsToMark[element] = index;
                        }
                    });

                    this.markErrorsOnForm(errors, fieldsToMark);
                }

                if (errors && goToFocus !== false)
                {
                    this.scrollFocusObject('.input-validation-error:first', -50);
                }

                return errors;
            },

            validateImageSize: function (fileContent, minWidth, minHeight, callbackSuccess, callbackError, ctx) {
                var reader = new FileReader();
                reader.onload = function (event) {
                    var img = new Image();
                    img.onload = function () {
                        var errorMessage = 'Tu imagen es muy pequeña y no se verá bien. El tamaño mínimo recomendado para cargar esta imagen es de ' + minWidth + 'x' + minHeight;
                        if (minWidth && minWidth > img.width)
                            callbackError.call(ctx, { codeError: 'minWidth', message: errorMessage });
                        else if (minHeight && minHeight > img.height)
                            callbackError.call(ctx, { codeError: 'minWidth', message: errorMessage });
                        else
                            callbackSuccess.call(ctx);
                    }
                    img.src = event.target.result;
                }
                reader.readAsDataURL(fileContent);
            },
            markErrorsOnForm: function (errors, fieldsToMark) {
                var that = this;
                _.each(errors, function (errorField, index) {
                    //recorre los errores y marca solo los que tienen objeto DOM
                    var domObj = that.$(fieldsToMark[index]);
                    if (domObj)
                        domObj.addClass("input-validation-error");
                    //busca el mensaje, si existe lo marca
                    var domMessage = that.$("span[tuils-val-for='" + index + "']");
                    if (domMessage) {
                        domMessage.text(errorField);
                        domMessage.addClass("field-validation-error");
                    }
                });
            },
            bindValidation : function()
            {
                Backbone.Validation.bind(this);
            },
            //Con el fin de evitar muchos clicks inhabilita el boton unos segundos
            disableButtonForSeconds: function (obj, seconds) {
                seconds = !seconds ? 4 : seconds;
                obj.attr("disabled", 'disabled');
                setTimeout(function () {
                    obj.removeAttr("disabled");
                }, seconds*1000);
            },
            removeErrors: function () {
                this.$el.find(".input-validation-error").removeClass("input-validation-error");
                this.$el.find(".field-validation-error").text("").removeClass("input-validation-error");
            },
            //Mueve el cursor y la vista del usuario a una posición de un objeto
            scrollFocusObject: function (selector, addPixels) {
                //Valida que el selector exista
                var obj = $(selector);
                if (!obj.length)
                    return;
                if (addPixels == undefined)
                    addPixels = 0;

                var position = 0;
                if (obj.offset() != undefined)
                    position = obj.offset().top;
                $('html, body').animate({
                    scrollTop: position + addPixels
                }, 500);
            },
            //Realiza track de algunas acciones de google analytics
            trackGAEvent: function (category, action, label, value) {
                var ga = window.ga;
                //Valida que exista el metodo
                if (ga != undefined)
                {
                    if (!label)
                        label = document.location.pathname;

                    console.log('Evento trackeado CAT:' + category + ' - ACTION:' + action + ' - LABEL:' + label);
                    ga('send', 'event', category, action, label);
                }
            },
            isMobile: function ()
            {
                if (this._isMobile === undefined)
                {
                    var check = false;
                    (function (a) { if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = true })(navigator.userAgent || navigator.vendor || window.opera);
                    this._isMobile = check;
                }

                return this._isMobile;
            },
            //Valida que sea un tamaño valido y la extensión
            isValidFileUpload: function (file, target, type) {

                if (file) {
                    if (TuilsUtil.isValidSize(target)) {
                        if (TuilsUtil.isValidExtension(target, type)) {
                            return true;
                        }
                        else {
                            this.alert("La extensión del archivo no es valida");
                            return false;
                        }
                    }
                    else {
                        this.alert("El tamaño excede el limite");
                        return false;
                    }
                }
            },
            isMinSize: function () {
                var currentWidth = window.innerWidth || document.documentElement.clientWidth;
                return currentWidth <= this.minSizeDesktopWith;
            },
            //Es un tamaño mucho más pequeño para hacer validaciones de tamaño más estrictas
            isMinSizeMobile: function () {
                var currentWidth = window.innerWidth || document.documentElement.clientWidth;
                return currentWidth <= this.minSizeMobileWith;
            }
        });

        return BaseView;
    });




