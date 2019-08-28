var FormValidator = {
    formElement: null,
    successClass: 'validated',
    errorClass: 'not-validated',
    errorElement: '.item-error',
    errorMessages: {
        required: 'Введите {name}',
        email: 'Неверный формат E-mail',
        length: 'Поле {name} должно содержать от {min} до {max} символов',
        phone: 'Неверный формат телефона'
    },
    fields: {},
    errors: {},
    attributes: {},
    additionalParams: {},

    __parent: null,

    validators: {
        required: function(value) {
            return $.trim(value) !== '';
        }.bind(this.__parent),
        length: function(value) {
            value = $.trim(value);
            if(!__parent.additionalParams)
                return !!value;
            return !(
                __parent.additionalParams.hasOwnProperty('min') && value.length < __parent.additionalParams.min ||
                __parent.additionalParams.hasOwnProperty('max') && value.length > __parent.additionalParams.max
            );
        }
        .bind(this.__parent),
        email: function(value) {
            return /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(value);
        }
        .bind(this.__parent),
        phone: function(value) {
            return /^\s*?(?:(?:\+?\s*?7)|(?:\s*?8\s*?))?(?:\s*?-?\s*?)?(?:\(\d{3}\)|\d{3})(?:\s*?-?\s*?)?\d{3}(?:\s*?-?\s*?)?\d{2}(?:\s*?-?\s*?)?\d{2}$/.test(value);
        }.bind(this.__parent)
    },

    //must be called first
    init: function() {
        __parent = $.extend(true, {}, this);
        //some else...
        return __parent;
    },
    //set form element for validate
    setForm: function(formEl) {
        this.formElement = $(formEl);
        this.fields = this.formElement.find('input, textarea, select').not('[type="submit"], [type="button"]');
        return this;
    },
    //set custom error messages
    setErrorMessage: function(type, message) {
        this.errorMessages[type] = message;
        return this;
    },
    //set custom error class for element
    setErrorClass: function(className) {
        this.errorClass = className;
        return this;
    },
    //set custom success class for element
    setSuccessClass: function(className) {
        this.successClass = className;
        return this;
    },
    //set custom error element (id or className of element)
    setErrorElement: function(el) {
        this.errorElement = el;
        return this;
    },
    //set validation for specified events (default is change only)
    setValidation: function(events) {
        events = events || 'change';
        var _this = this;
        //check field on specified events
        _this.fields.on(events, function() {
            _this.validateField($(this));
        });
        //check all fields on submit form
        _this.formElement.on('submit', function(e) {
            //check fields
            _this.validateAll();
            //check errors for exists
            var errors = _this.getErrors(),
                hasError = false;

            for(var i in errors)
            {
                if($.trim(errors[i]) != '') {
                    hasError = true;
                    break;
                }
            }
            //cancel submit form if has errors
            if(hasError) {
                e.preventDefault();
                return false;
            }
            return true;
        });
    },
    //validate field for specified validators
    validateField: function(el) {
        el = $(el);
        var validators = [],
            name = el.attr('name') || el.attr('type'),
            title = null,
            attr = null,
            replace = {},
            _this = this,
            rules = _this.getElementRules(el);

        //get replace value for {name}
        replace['name'] = _this.getElementTitle(el);

        //merge with other params (min, max, message, etc..)
        replace = $.extend(replace, rules);
        //empty errors for field
        _this.errors[name] = '';

        //search validators for element
        if(attr = el.data('validation'))
        {
            //validate field by each validator (only first error)
            validators = attr.split(/\s+/) || [];
            for(var i in validators)
            {
                //validator not found
                if(!_this.validators.hasOwnProperty([validators[i]]) || !_this.errorMessages.hasOwnProperty([validators[i]]))
                    continue;

                //field isn't valid
                if(!_this.validators[validators[i]](el.val()))
                {
                    var errorText = '';
                    //set error text: validator message, total message (global scope) or default message from FormValidator
                    //validator message
                    if(rules.hasOwnProperty([validators[i]]) && rules[validators[i]].hasOwnProperty('message'))
                        errorText = rules[validators[i]]['message'];
                    //total message
                    if(!errorText && rules.hasOwnProperty('message'))
                        errorText = rules['message'];
                    //default message
                    if(!errorText)
                        errorText = _this.errorMessages[validators[i]];

                    //replace params with values ( e.g. {min} => 3, {max} => 10, etc..)
                    _this.errors[name] = errorText.replace(/\{([a-z]+)\}/gi, function(m, attr) {
                        if(replace[attr] != 'undefined')
                            return replace[attr];
                        return attr;
                    });
                    //show error text & add error class for element
                    el.removeClass(_this.successClass).addClass(_this.errorClass).parent().find(_this.errorElement).text(_this.errors[name]);
                    break;
                }
                //hide error text & remove error class for element
                el.removeClass(_this.errorClass).addClass(_this.successClass).parent().find(_this.errorElement).empty();
            }
        }
    },
    //validate all fields
    validateAll: function() {
        var _this = this;
        //set validate for each form field
        _this.fields.each(function(index, el) {
            _this.validateField(el);
        });
    },

    //get element title
    getElementTitle: function(el) {
        var title = '';
        if(!(title = el.data('validation-title'))) {
            if (el.attr('name'))
                title = el.attr('name');
            else
                title = el.attr('type');
        }
        return title;
    },
    //search additional validation rules ( e.g. {min:3, max:10, message: 'some text'} )
    getElementRules: function(el) {
        var rules = {};
        if((attr = el.data('validation-rules'))) {
            var json = JSON.parse(attr.replace(/'/g, '"').replace(/\b([a-z]+?)(?=:)/gi, '"$1"'));
            if(json)
                rules = $.extend(this.additionalParams, json)
        }
        return rules;
    },
    //has error ?
    hasErrors: function() {
        for(var i in this.errors)
            if(this.errors[i] != '')
                return true;
        return false;
    },
    //get all errors
    getErrors: function () {
        return this.errors;
    },
    //get error for specific field
    getError: function (type) {
        return this.errors.hasOwnProperty(type) ? this.errors[type] : false;
    }
};