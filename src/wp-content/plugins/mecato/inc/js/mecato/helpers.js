/**
 * Created by Beto on 12/21/2015.
 */
define(['jquery', 'underscore'],
    function ($, _, TuilsConfiguration) {
        var Helpers = {
            onlyNumbers: function (evt) {
                var charCode = (evt.which) ? evt.which : event.keyCode
                if (charCode > 31 && (charCode < 48 || charCode > 57))
                    return false;
                return true;
            },
            toStringWithSeparator: function (array, separator) {
                var toString;
                _.each(array, function (element, index) {
                    toString = toString ? toString + separator + element : element;
                });
                return toString ? toString : "";
            },
            isValidSize: function (obj) {
                if (obj.files[0].size > TuilsConfiguration.maxFileUploadSize) {
                    obj.files[0] = null;
                    obj.value = "";
                    return false;
                }
                else
                    return true;
            },
            isValidExtension: function (obj, typeFile) {
                obj = $(obj);

                var validExtensions;
                if (typeFile == 'image')
                    validExtensions = /(jpg|JPG|jpeg|JPEG|gif|GIF|png|PNG)/

                if (!validExtensions.test(obj.val())) {
                    obj.val("");
                    return false;
                }
                else {
                    return true;
                }
            },
            //tomado de http://stackoverflow.com/questions/5999118/add-or-update-query-string-parameter
            updateQueryStringParameter: function (uri, key, value) {
                var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
                var separator = uri.indexOf('?') !== -1 ? "&" : "?";
                if (uri.match(re)) {
                    return uri.replace(re, '$1' + key + "=" + value + '$2');
                }
                else {
                    return uri + separator + key + "=" + value;
                }
            }

        };

        return Helpers;
    });