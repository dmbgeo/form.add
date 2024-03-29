if (typeof window.DmbgeoForm != 'function') {
    window.DmbgeoForm = function (params) {

        this.iblock_element = params.iblock_element || '';
        this.url = params.url
        this.params = null;
        this.element = $(params.element);
        this.sendForm('INIT');
    }

    window.DmbgeoForm.prototype = {

        sendForm: async function (ACTION = 'SEND', merge = {}) {

            if (this.iblock_element) {
                merge['IBLOCK_ELEMENT'] = this.iblock_element;
            }
            console.log(this);
            merge['ACTION'] = ACTION;
            if (ACTION == 'SEND') {
                let result = await window.ajaxJson(this.url, window.getFormParam($('#' + this.params.VIEW['FORM_ID']), merge));
                let old_form_id = this.params.VIEW['FORM_ID'];

                this.params = result.data;
                if (this.params['RESULT_ID']) {
                    if ($("#" + old_form_id).length > 0) {
                        $("#" + old_form_id)[0].reset();
                    }

                    $("#" + old_form_id).trigger("DmbgeoFormSuccess", [this]);
                    if (this.params['PARAMS']['IS_POPUP_RESULT'] == 'Y') {
                        this.setPopupBody(result.html, this.params.VIEW['POPUP_RESULT_ID']);
                        console.log('dda');
                        if (this.params['PARAMS']['IS_POPUP_FORM'] != 'Y' || (this.params['PARAMS']['IS_POPUP_FORM'] == 'Y' && this.params['PARAMS']['POPUP_RESULT_ID'] != this.params['PARAMS']['POPUP_ID'])) {
                            this.popup(this.params.VIEW['POPUP_RESULT_ID'], false);
                        }
        
                    }
                    else {
                        this.setContainerBody(result.html);
                    }
                    if (this.params['PARAMS']['IS_POPUP_FORM'] == 'N')
                        this.sendForm('INIT');


                }
                else {
                    if (this.params['PARAMS']['IS_POPUP_FORM'] == 'Y') {
                        this.element.attr('id', this.params['VIEW']['CONTAINER_ID']);
                        this.setPopupBody(result.html, this.params.VIEW['POPUP_ID'],);
                    }
                    else {
                        this.setContainerBody(result.html);
                    }
                    this.initForm();
                }
            }
            if (ACTION == 'INIT') {
                let result = await window.ajaxJson(this.url, merge, 'POST');
                if (result) {
                    this.params = result.data;
                    if (this.params['PARAMS']['IS_POPUP_FORM'] == 'Y') {
                        this.setPopupBody(result.html, this.params.VIEW['POPUP_ID']);
                        this.popup(this.params.VIEW['POPUP_ID']);
                    }
                    else {
                        this.setContainerBody(result.html);
                    }

                    this.initForm();

                }
            }


        },


        setContainerBody: function (body) {
            let self = this;
            self.element.html(body);
            $("#" + this.params.VIEW['FORM_ID']).trigger("DmbgeoFormRender", [this]);

        },
        setPopupBody: function (body, popup_id) {
            let self = this;
            $('#' + popup_id + ' [data-modal-body]').html(body);
            $("#" + this.params.VIEW['FORM_ID']).trigger("DmbgeoFormRender", [this]);
        },

        popup: function (popup_id, modal = false) {

            if (this.params['PARAMS']['POPUP_CORE'] == 'bootstrap') {

                $('.modal').modal('hide');
                $('#' + popup_id).modal('show');
            }
            if (this.params['PARAMS']['POPUP_CORE'] == 'fancybox') {
                $.fancybox.close();
                $.fancybox.open({
                    src: '#' + popup_id,
                    modal: modal,
                    touch: false

                });
            }


            $("#" + this.params.VIEW['FORM_ID']).trigger("DmbgeoFormPopupShow", [this]);
        },


        initForm: function () {
            this.initValidates();
        },
        initValidates: function () {
            let self = this;

            for (let key in self.params.VALIDATION.RULES) {
                let rule = self.params.VALIDATION.RULES[key];
                for (let keyParam in rule) {

                    if (isJson(rule[keyParam])) {
                        self.params.VALIDATION.RULES[key][keyParam] = isJson(rule[keyParam]);

                    }


                }
            }

            this.validateParams = {
                // ignore: [],
                errorClass: 'bfilter__tooltip',
                errorElement: "div",
                rules: self.params.VALIDATION.RULES,
                messages: self.params.VALIDATION.MESSAGES,
                // submitHandler: 
                highlight: function (element, errorClass, validClass) {
                    $(element).parent().addClass('valid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).parent().removeClass('valid');
                },
                submitHandler: function (form) {
                    self.sendForm('SEND');
                }
            };

            $("#" + this.params.VIEW['FORM_ID']).trigger("DmbgeoFormValidate", [this]);

            this.validate = $('#' + this.params.VIEW['FORM_ID']).validate(this.validateParams);




        },
    };


    $(document).ready(function () {
        jQuery.validator.addMethod("remote_form", function (value, element, param) {
            if (this.optional(element)) {
                return "dependency-mismatch";
            }

            var previous = this.previousValue(element),
                validator, data;

            if (!this.settings.messages[element.name]) {
                this.settings.messages[element.name] = {};
            }
            previous.originalMessage = this.settings.messages[element.name].remote;
            this.settings.messages[element.name].remote = previous.message;

            param = typeof param === "string" && { url: param } || param;

            if (previous.old === value) {
                return previous.valid;
            }

            previous.old = value;
            validator = this;
            this.startRequest(element);

            $.ajax($.extend(true, {
                url: param,
                mode: "abort",
                port: "validate" + element.name,
                dataType: "json",
                data: window.getFormParam($(element).closest('form')),
                context: validator.currentForm,
                success: function (response) {

                    var valid = response === true || response === "true",
                        errors, message, submitted;

                    validator.settings.messages[element.name].remote = previous.originalMessage;
                    if (valid) {
                        submitted = validator.formSubmitted;
                        validator.prepareElement(element);
                        validator.formSubmitted = submitted;
                        validator.successList.push(element);
                        delete validator.invalid[element.name];
                        validator.showErrors();
                    }

                    previous.valid = valid;
                    validator.stopRequest(element, valid);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.error(xhr.status);
                    console.error(thrownError);
                }
            }, param));
            return "pending";
        }, 'Неправильно');

        $(document).on('click', '[data-dmbgeo-form]', function (e) {
            e.preventDefault();
            new window.DmbgeoForm({ 'url': $(this).data('dmbgeo-form'), 'iblock_element': $(this).data('iblock-element') || '', 'element': this });
        });


        $('[data-dmbgeo-form-init]').each(function () {
            new window.DmbgeoForm({ 'url': $(this).data('dmbgeo-form-init'), 'iblock_element': $(this).data('iblock-element') || '', 'element': this })
        });
    });
}

if (typeof window.ajax != 'function') {
    window.ajax = async function (url, params = {}, method = "POST", headers = {}) {
        let data = new FormData;
        data.append('AJAX_MODE', 'Y');

        if (typeof params == 'object') {
            for (let key in params) {
                if (typeof params[key] == 'object') {
                    for (let key2 in params[key]) {
                        data.append(key, params[key][key2]);
                    }
                }
                else {
                    data.append(key, params[key]);
                }
            }

        }

        let response = await fetch(url, {
            method: method,
            headers: headers,
            body: data
        });


        if (response.ok) {
            return await response.text();
        } else {
            console.error("Ошибка HTTP: " + response.status);
            return null;
        }
    }
}
if (typeof window.ajaxJson != 'function') {
    window.ajaxJson = async function (url, params = {}, method = "POST", headers = {}) {
        let data = new FormData;
        data.append('AJAX_MODE', 'Y');
        if (typeof params == 'object') {
            for (let key in params) {
                if (typeof params[key] == 'object') {
                    for (let key2 in params[key]) {
                        data.append(key, params[key][key2]);
                    }
                }
                else {
                    data.append(key, params[key]);
                }
            }

        }

        let response = await fetch(url, {
            method: method,
            headers: headers,
            body: data
        });


        if (response.ok) {
            return await response.json();
        } else {
            console.error("Ошибка HTTP: " + response.status);
            return null;
        }
    }
}
if (typeof window.getFormParam != 'function') {
    window.getFormParam = function (form, merge = {}) {
        let formData = {};
        if (form) {
            let formArray = form.serializeArray();
            formArray.forEach(element => {
                if (formData.hasOwnProperty(element.name)) {
                    if (typeof formData[element.name] == 'string') {
                        let value = formData[element.name];
                        formData[element.name] = [];
                        formData[element.name].push(value);
                    }

                    formData[element.name].push(element.value);

                }
                else {
                    formData[element.name] = element.value;
                }
            });

        }
        for (let key in merge) {
            formData[key] = merge[key];
        }

        return formData;
    }
}
if (typeof trim != 'function') {
    function trim(value) { return $.trim(value); };
}
if (typeof isJson != 'function') {
    function isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return JSON.parse(str);
    }
}

if (typeof getParameterByName != 'function') {
    function getParameterByName(name) {
        var name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
        var results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }
}