; /* /bitrix/js/api.feedback/cssmodal/jquery.cssmodal.min.js?143261098017939*/
; /* /bitrix/js/api.feedback/formstyler/jquery.formstyler.min.js?143261098016178*/
; /* /bitrix/js/api.feedback/autosize/jquery.autosize.min.js?14326109802111*/
; /* /bitrix/js/api.feedback/placeholder/jquery.placeholder.min.js?14326109807392*/
; /* /bitrix/js/api.feedback/validation/jquery.validation.min.js?143261098063722*/

; /* Start:"a:4:{s:4:"full";s:75:"/bitrix/js/api.feedback/validation/jquery.validation.min.js?143261098063722";s:6:"source";s:59:"/bitrix/js/api.feedback/validation/jquery.validation.min.js";s:3:"min";s:0:"";s:3:"map";s:0:"";}"*/
/**
 * jQuery Form Validation
 * Copyright (C) 2015 RunningCoder.org
 * Licensed under the MIT license
 *
 * @author Tom Bertrand
 * @version 1.5.2 (2015-02-18)
 * @link http://www.runningcoder.org/jqueryvalidation/
 *
 * @note
 * Remove debug code: //\s?\{debug\}[\s\S]*?\{/debug\}
 */
;(function (window, document, $, undefined) {

    window.Validation = {
        form: [],
        labels: {},
        hasScrolled: false
    };

    /**
     * Fail-safe preventExtensions function for older browsers
     */
    if (typeof Object.preventExtensions !== "function") {
        Object.preventExtensions = function (obj) {
            return obj;
        };
    }

    // Not using strict to avoid throwing a window error on bad config extend.
    // console.debug is used instead to debug Validation
    //"use strict";

    // =================================================================================================================
    /**
     * @private
     * RegExp rules
     */
    var _rules = {
        NOTEMPTY: /\S/,
        INTEGER: /^\d+$/,
        NUMERIC: /^\d+(?:[,\s]\d{3})*(?:\.\d+)?$/,
        MIXED: /^[\w\s-]+$/,
        NAME: /^['a-zãàáäâẽèéëêìíïîõòóöôùúüûñç\s-]+$/i,
        NOSPACE: /^(?!\s)\S*$/,
        TRIM: /^[^\s].*[^\s]$/,
        DATE: /^\d{4}-\d{2}-\d{2}(\s\d{2}:\d{2}(:\d{2})?)?$/,
        EMAIL: /^([^@]+?)@(([a-z0-9]-*)*[a-z0-9]+\.)+([a-z0-9]+)$/i,
        URL: /^(https?:\/\/)?((([a-z0-9]-*)*[a-z0-9]+\.?)*([a-z0-9]+))(\/[\w?=\.-]*)*$/,
        PHONE: /^(\()?\d{3}(\))?(-|\s)?\d{3}(-|\s)\d{4}$/,
        OPTIONAL: /\S/,
        COMPARISON: /^\s*([LV])\s*([<>]=?|==|!=)\s*([^<>=!]+?)\s*$/
    },

    /**
     * @private
     * Error messages
     */
    _messages = {
        'default': '$ contain error(s).',
        'NOTEMPTY': '$ must not be empty.',
        'INTEGER': '$ must be an integer.',
        'NUMERIC': '$ must be numeric.',
        'MIXED': '$ must be letters or numbers (no special characters).',
        'NAME': '$ must not contain special characters.',
        'NOSPACE': '$ must not contain spaces.',
        'TRIM': '$ must not start or end with space character.',
        'DATE': '$ is not a valid with format YYYY-MM-DD.',
        'EMAIL': '$ is not valid.',
        'URL': '$ is not valid.',
        'PHONE': '$ is not a valid phone number.',
        '<': '$ must be less than % characters.',
        '<=': '$ must be less or equal to % characters.',
        '>': '$ must be greater than % characters.',
        '>=': '$ must be greater or equal to % characters.',
        '==': '$ must be equal to %',
        '!=': '$ must be different than %'
    },

    /**
     * @private
     * HTML5 data attributes
     */
    _data = {
        validation: 'data-validation',
        validationMessage: 'data-validation-message',
        regex: 'data-validation-regex',
        regexReverse: 'data-validation-regex-reverse',
        regexMessage: 'data-validation-regex-message',
        group: 'data-validation-group',
        label: 'data-validation-label',
        errorList: 'data-error-list'
    },

    /**
     * @private
     * Default options
     *
     * @link http://www.runningcoder.org/jqueryvalidation/documentation/
     */
    _options = {
        submit: {
            settings: {
                form: null,
                display: "inline",
                insertion: "append",
                allErrors: false,
                trigger: "click",
                button: "[type='submit']",
                errorClass: "error",
                errorListClass: "error-list",
                inputContainer: null,
                clear: "focusin",
                scrollToError: false
            },
            callback: {
                onInit: null,
                onValidate: null,
                onError: null,
                onBeforeSubmit: null,
                onSubmit: null,
                onAfterSubmit: null
            }
        },
        dynamic: {
            settings: {
                trigger: null,
                delay: 300
            },
            callback: {
                onSuccess: null,
                onError: null,
                onComplete: null
            }
        },
        rules: {},
        messages: {},
        labels: {},
        debug: false
    },

    /**
     * @private
     * Limit the supported options on matching keys
     */
    _supported = {
        submit: {
            settings: {
                display: ["inline", "block"],
                insertion: ["append", "prepend"], //"before", "insertBefore", "after", "insertAfter"
                allErrors: [true, false],
                clear: ["focusin", "keypress", false],
                trigger: [
                    "click", "dblclick", "focusout",
                    "hover", "mousedown", "mouseenter",
                    "mouseleave", "mousemove", "mouseout",
                    "mouseover", "mouseup", "toggle"
                ]
            }
        },
        dynamic: {
            settings: {
                trigger: ["focusout", "keydown", "keypress", "keyup"]
            }
        },
        debug: [true, false]
    };

    // =================================================================================================================

    /**
     * @constructor
     * Validation Class
     *
     * @param {object} node jQuery form object
     * @param {object} options User defined options
     */
    var Validation = function (node, options) {

        var errors = [],
            messages = {},
            delegateSuffix = ".vd", // validation.delegate
            resetSuffix = ".vr";    // validation.resetError

        window.Validation.hasScrolled = false;

        /**
         * Extends user-defined "options.message" into the default Validation "_message".
         */
        function extendRules() {
            options.rules = $.extend(
                true,
                {},
                _rules,
                options.rules
            );
        }

        /**
         * Extends user-defined "options.message" into the default Validation "_message".
         */
        function extendMessages() {
            options.messages = $.extend(
                true,
                {},
                _messages,
                options.messages
            );
        }

        /**
         * Extends user-defined "options" into the default Validation "_options".
         * Notes:
         *  - preventExtensions prevents from modifying the Validation "_options" object structure
         *  - filter through the "_supported" to delete unsupported "options"
         */
        function extendOptions() {

            if (!(options instanceof Object)) {
                options = {};
            }

            var tpmOptions = Object.preventExtensions($.extend(true, {}, _options));

            for (var method in options) {

                if (!options.hasOwnProperty(method) || method === "debug") {
                    continue;
                }

                if (~["labels", "messages", "rules"].indexOf(method) && options[method] instanceof Object) {
                    tpmOptions[method] = options[method];
                    continue;
                }

                if (!_options[method] || !(options[method] instanceof Object)) {

                    // {debug}
                    options.debug && window.Debug.log({
                        'node': node,
                        'function': 'extendOptions()',
                        'arguments': '{' + method + ': ' + JSON.stringify(options[method]) + '}',
                        'message': 'WARNING - ' + method + ' - invalid option'
                    });
                    // {/debug}

                    continue;
                }

                for (var type in options[method]) {
                    if (!options[method].hasOwnProperty(type)) {
                        continue;
                    }

                    if (!_options[method][type] || !(options[method][type] instanceof Object)) {

                        // {debug}
                        options.debug && window.Debug.log({
                            'node': node,
                            'function': 'extendOptions()',
                            'arguments': '{' + type + ': ' + JSON.stringify(options[method][type]) + '}',
                            'message': 'WARNING - ' + type + ' - invalid option'
                        });
                        // {/debug}

                        continue;
                    }

                    for (var option in options[method][type]) {
                        if (!options[method][type].hasOwnProperty(option)) {
                            continue;
                        }

                        if (_supported[method] &&
                            _supported[method][type] &&
                            _supported[method][type][option] &&
                            $.inArray(options[method][type][option], _supported[method][type][option]) === -1) {

                            // {debug}
                            options.debug && window.Debug.log({
                                'node': node,
                                'function': 'extendOptions()',
                                'arguments': '{' + option + ': ' + JSON.stringify(options[method][type][option]) + '}',
                                'message': 'WARNING - ' + option.toString() + ': ' + JSON.stringify(options[method][type][option]) + ' - unsupported option'
                            });
                            // {/debug}

                            delete options[method][type][option];
                        }

                    }
                    if (tpmOptions[method] && tpmOptions[method][type]) {
                        tpmOptions[method][type] = $.extend(Object.preventExtensions(tpmOptions[method][type]), options[method][type]);
                    }
                }
            }

            // {debug}
            if (options.debug && $.inArray(options.debug, _supported.debug !== -1)) {
                tpmOptions.debug = options.debug;
            }
            // {/debug}

            // @TODO Would there be a better fix to solve event conflict?
            if (tpmOptions.dynamic.settings.trigger) {
                if (tpmOptions.dynamic.settings.trigger === "keypress" && tpmOptions.submit.settings.clear === "keypress") {
                    tpmOptions.dynamic.settings.trigger = "keydown";
                }
            }

            options = tpmOptions;

        }

        /**
         * Delegates the dynamic validation on data-validation and data-validation-regex attributes based on trigger.
         *
         * @returns {boolean} false if the option is not set
         */
        function delegateDynamicValidation() {

            if (!options.dynamic.settings.trigger) {
                return false;
            }

            // {debug}
            options.debug && window.Debug.log({
                'node': node,
                'function': 'delegateDynamicValidation()',
                'arguments': JSON.stringify(options),
                'message': 'OK - Dynamic Validation activated on ' + node.length + ' form(s)'
            });
            // {/debug}

            if (!node.find('[' + _data.validation + '],[' + _data.regex + ']')[0]) {

                // {debug}
                options.debug && window.Debug.log({
                    'node': node,
                    'function': 'delegateDynamicValidation()',
                    'arguments': 'node.find([' + _data.validation + '],[' + _data.regex + '])',
                    'message': 'ERROR - [' + _data.validation + '] not found'
                });
                // {/debug}

                return false;
            }

            var event = options.dynamic.settings.trigger + delegateSuffix;
            if (options.dynamic.settings.trigger !== "focusout") {
                event += " change" + delegateSuffix + " paste" + delegateSuffix;
            }

            $.each(
                node.find('[' + _data.validation + '],[' + _data.regex + ']'),
                function (index, input) {

                    $(input).unbind(event).on(event, function (e) {

                        if ($(this).is(':disabled')) {
                            return false;
                        }

                        //e.preventDefault();

                        var input = this,
                            keyCode = e.keyCode || null;

                        _typeWatch(function () {

                            if (!validateInput(input)) {

                                displayOneError(input.name);
                                _executeCallback(options.dynamic.callback.onError, [node, input, keyCode, errors[input.name]]);

                            } else {

                                _executeCallback(options.dynamic.callback.onSuccess, [node, input, keyCode]);

                            }

                            _executeCallback(options.dynamic.callback.onComplete, [node, input, keyCode]);

                        }, options.dynamic.settings.delay);

                    });
                }
            );
        }

        /**
         * Delegates the submit validation on data-validation and data-validation-regex attributes based on trigger.
         * Note: Disable the form submit function so the callbacks are not by-passed
         */
        function delegateValidation() {

            _executeCallback(options.submit.callback.onInit, [node]);

            var event = options.submit.settings.trigger + '.vd';

            // {debug}
            options.debug && window.Debug.log({
                'node': node,
                'function': 'delegateValidation()',
                'arguments': JSON.stringify(options),
                'message': 'OK - Validation activated on ' + node.length + ' form(s)'
            });
            // {/debug}

            if (!node.find(options.submit.settings.button)[0]) {

                // {debug}
                options.debug && window.Debug.log({
                    'node': node,
                    'function': 'delegateValidation()',
                    'arguments': '{button: ' + options.submit.settings.button + '}',
                    'message': 'ERROR - node.find("' + options.submit.settings.button + '") not found'
                });
                // {/debug}

                return false;

            }

            //node.on("submit", false);
            node.find(options.submit.settings.button).off('.vd').on(event, function (e) {

                //e.preventDefault();

                resetErrors();

                _executeCallback(options.submit.callback.onValidate, [node]);

                if (!validateForm()) {

                    e.preventDefault();

                    // OnError function receives the "errors" object as the last "extraParam"
                    _executeCallback(options.submit.callback.onError, [node, errors]);

                    displayErrors();

                    return false;

                } else {

                    _executeCallback(options.submit.callback.onBeforeSubmit, [node]);

                    //(options.submit.callback.onSubmit) ? _executeCallback(options.submit.callback.onSubmit, [node]) : submitForm();

                    (options.submit.callback.onSubmit) ? _executeCallback(options.submit.callback.onSubmit, [node]) :  '';

                    _executeCallback(options.submit.callback.onAfterSubmit, [node]);

                }

                // {debug}
                options.debug && window.Debug.print();
                // {/debug}

                //return false;

            });

        }

        /**
         * For every "data-validation" & "data-pattern" attributes that are not disabled inside the jQuery "node" object
         * the "validateInput" function will be called.
         *
         * @returns {boolean} true if no error(s) were found (valid form)
         */
        function validateForm() {

            var isValid = true;

            $.each(
                node.find('[' + _data.validation + ']:not([disabled],[readonly]),[' + _data.regex + ']:not([disabled],[readonly])'),
                function (index, input) {
                    if (!validateInput(input)) {
                        isValid = false;
                    }
                }
            );

            return isValid;

        }

        /**
         * Prepare the information from the data attributes
         * and call the "validateRule" function.
         *
         * @param {object} input Reference of the input element
         *
         * @returns {boolean} true if no error(s) were found (valid input)
         */
        function validateInput(input) {

            var inputName = $(input).attr('name');

            if (!inputName) {

                // {debug}
                options.debug && window.Debug.log({
                    'node': node,
                    'function': 'validateInput()',
                    'arguments': '$(input).attr("name")',
                    'message': 'ERROR - Missing input [name]'
                });
                // {/debug}

                return false;
            }

            var value = _getInputValue(input),

                matches = inputName.replace(/]$/, '').split(/]\[|[[\]]/g),
                inputShortName = window.Validation.labels[inputName] ||
                    options.labels[inputName] ||
                    $(input).attr(_data.label) ||
                    matches[matches.length - 1],

                validationArray = $(input).attr(_data.validation),
                validationMessage = $(input).attr(_data.validationMessage),
                validationRegex = $(input).attr(_data.regex),
                validationRegexReverse = !($(input).attr(_data.regexReverse) === undefined),
                validationRegexMessage = $(input).attr(_data.regexMessage),

                validateOnce = false;

            if (validationArray) {
                validationArray = _api._splitValidation(validationArray);
            }

            // Validates the "data-validation"
            if (validationArray instanceof Array && validationArray.length > 0) {

                // "OPTIONAL" input will not be validated if it's empty
                if (value === '' && ~validationArray.indexOf('OPTIONAL')) {
                    return true;
                }

                $.each(validationArray, function (i, rule) {

                    if (validateOnce === true) {
                        return true;
                    }

                    try {

                        validateRule(value, rule);

                    } catch (error) {

                        if (validationMessage || !options.submit.settings.allErrors) {
                            validateOnce = true;
                        }

                        error[0] = validationMessage || error[0];

                        registerError(inputName, error[0].replace('$', inputShortName).replace('%', error[1]));

                    }

                });

            }

            // Validates the "data-validation-regex"
            if (validationRegex) {

                var rule = _buildRegexFromString(validationRegex);

                // Do not block validation if a regexp is bad, only skip it
                if (!(rule instanceof RegExp)) {
                    return true;
                }

                try {

                    validateRule(value, rule, validationRegexReverse);

                } catch (error) {

                    error[0] = validationRegexMessage || error[0];

                    registerError(inputName, error[0].replace('$', inputShortName));

                }

            }

            return !errors[inputName] || errors[inputName] instanceof Array && errors[inputName].length === 0;

        }

        /**
         * Validate an input value against one rule.
         * If a "value-rule" mismatch occurs, an error is thrown to the caller function.
         *
         * @param {string} value
         * @param rule
         * @param {boolean} [reversed]
         *
         * @returns {*} Error if a mismatch occurred.
         */
        function validateRule(value, rule, reversed) {

            // Validate for "data-validation-regex" and "data-validation-regex-reverse"
            if (rule instanceof RegExp) {
                var isValid = rule.test(value);

                if (reversed) {
                    isValid = !isValid;
                }

                if (!isValid) {
                    throw [options.messages['default'], ''];
                }
                return;
            }

            if (options.rules[rule]) {
                if (!options.rules[rule].test(value)) {
                    throw [options.messages[rule], ''];
                }
                return;
            }

            // Validate for comparison "data-validation"
            var comparison = rule.match(options.rules.COMPARISON);

            if (!comparison || comparison.length !== 4) {

                // {debug}
                options.debug && window.Debug.log({
                    'node': node,
                    'function': 'validateRule()',
                    'arguments': 'value: ' + value + ' rule: ' + rule,
                    'message': 'WARNING - Invalid comparison'
                });
                // {/debug}

                return;
            }

            var type = comparison[1],
                operator = comparison[2],
                compared = comparison[3],
                comparedValue;

            switch (type) {

                // Compare input "Length"
                case "L":

                    // Only numeric value for "L" are allowed
                    if (isNaN(compared)) {

                        // {debug}
                        options.debug && window.Debug.log({
                            'node': node,
                            'function': 'validateRule()',
                            'arguments': 'compare: ' + compared + ' rule: ' + rule,
                            'message': 'WARNING - Invalid rule, "L" compare must be numeric'
                        });
                        // {/debug}

                        return false;

                    } else {

                        if (!value || eval(value.length + operator + parseFloat(compared)) === false) {
                            throw [options.messages[operator], compared];
                        }

                    }

                    break;

                // Compare input "Value"
                case "V":
                default:

                    // Compare Field values
                    if (isNaN(compared)) {

                        comparedValue = node.find('[name="' + compared + '"]').val();
                        if (!comparedValue) {

                            // {debug}
                            options.debug && window.Debug.log({
                                'node': node,
                                'function': 'validateRule()',
                                'arguments': 'compare: ' + compared + ' rule: ' + rule,
                                'message': 'WARNING - Unable to find compared field [name="' + compared + '"]'
                            });
                            // {/debug}

                            return false;
                        }

                        if (!value || !eval('"' + encodeURIComponent(value) + '"' + operator + '"' + encodeURIComponent(comparedValue) + '"')) {
                            throw [options.messages[operator].replace(' characters', ''), compared];
                        }

                    } else {
                        // Compare numeric value
                        if (!value || isNaN(value) || !eval(value + operator + parseFloat(compared))) {
                            throw [options.messages[operator].replace(' characters', ''), compared];
                        }

                    }
                    break;

            }

        }

        /**
         * Register an error into the global "error" variable.
         *
         * @param {string} inputName Input where the error occurred
         * @param {string} error Description of the error to be displayed
         */
        function registerError(inputName, error) {

            if (!errors[inputName]) {
                errors[inputName] = [];
            }

            error = error.capitalize();

            var hasError = false;
            for (var i = 0; i < errors[inputName].length; i++) {
                if (errors[inputName][i] === error) {
                    hasError = true;
                    break;
                }
            }

            if (!hasError) {
                errors[inputName].push(error);
            }

        }

        /**
         * Display a single error based on "inputName" key inside the "errors" global array.
         * The input, the label and the "inputContainer" will be given the "errorClass" and other
         * settings will be considered.
         *
         * @param {string} inputName Key used for search into "errors"
         *
         * @returns {boolean} false if an unwanted behavior occurs
         */
        function displayOneError(inputName) {

            var input,
                inputId,
                errorContainer,
                label,
                html = '<div class="' + options.submit.settings.errorListClass + '" ' + _data.errorList + '><ul></ul></div>',
                group,
                groupInput;

            if (!errors.hasOwnProperty(inputName)) {
                return false;
            }

            input = node.find('[name="' + inputName + '"]');

            label = null;

            if (!input[0]) {

                // {debug}
                options.debug && window.Debug.log({
                    'node': node,
                    'function': 'displayOneError()',
                    'arguments': '[name="' + inputName + '"]',
                    'message': 'ERROR - Unable to find input by name "' + inputName + '"'
                });
                // {/debug}

                return false;
            }

            group = input.attr(_data.group);

            if (group) {

                //!!!console.log(group);

                groupInput = node.find('[name="' + inputName + '"]');
                label = node.find('[id="' + group + '"]');

                //!!!console.log(groupInput,'groupInput');
                //!!!console.log(label,'label');

                if (label[0]) {
                    label.addClass(options.submit.settings.errorClass);
                    errorContainer = label;
                }

                //node.find('[' + _data.group + '="' + group + '"]').addClass(options.submit.settings.errorClass)

            } else {

                input.addClass(options.submit.settings.errorClass);

                if (options.submit.settings.inputContainer) {
                    input.parentsUntil(node, options.submit.settings.inputContainer).addClass(options.submit.settings.errorClass);
                }

                inputId = input.attr('id');

                if (inputId) {
                    label = node.find('label[for="' + inputId + '"]')[0];
                }

                if (!label) {
                    label = input.parentsUntil(node, 'label')[0];
                }

                if (label) {
                    label = $(label);
                    label.addClass(options.submit.settings.errorClass);
                }
            }

            if (options.submit.settings.display === 'inline') {
                errorContainer = errorContainer || input.parent();
            } else if (options.submit.settings.display === 'block') {
                errorContainer = node;
            }

            // Prevent double error list if the previous one has not been cleared.
            if (options.submit.settings.display === 'inline' && errorContainer.find('[' + _data.errorList + ']')[0]) {
                return false;
            }

            if (options.submit.settings.display === "inline" ||
                (options.submit.settings.display === "block" && !errorContainer.find('[' + _data.errorList + ']')[0])
            ) {
                if (options.submit.settings.insertion === 'append') {
                    errorContainer.append(html);
                } else if (options.submit.settings.insertion === 'prepend') {
                    errorContainer.prepend(html);
                }
            }

            for (var i = 0; i < errors[inputName].length; i++) {
                errorContainer.find('ul').append('<li>' + errors[inputName][i] + '</li>');
            }

            if (options.submit.settings.clear || options.dynamic.settings.trigger) {

                if (group && groupInput) {
                    input = groupInput;
                }

                var event = "coucou" + resetSuffix;
                if (options.submit.settings.clear) {
                    event += " " + options.submit.settings.clear + resetSuffix;
                }
                if (options.dynamic.settings.trigger) {
                    event += " " + options.dynamic.settings.trigger + resetSuffix;
                    if (options.dynamic.settings.trigger !== "focusout") {
                        event += " change" + resetSuffix + " paste" + resetSuffix;
                    }
                }

                input.unbind(event).on(event, function (a, b, c, d, e) {

                    return function () {
                        if (e) {
                            if ($(c).hasClass(options.submit.settings.errorClass)) {
                                resetOneError(a, b, c, d, e);
                            }
                        } else if ($(b).hasClass(options.submit.settings.errorClass)) {
                            resetOneError(a, b, c, d);
                        }
                    };

                }(inputName, input, label, errorContainer, group));
            }

            if (options.submit.settings.scrollToError && !window.Validation.hasScrolled) {

                window.Validation.hasScrolled = true;

                var offset = parseFloat(options.submit.settings.scrollToError.offset) || 0,
                    duration = parseFloat(options.submit.settings.scrollToError.duration) || 500,
                    handle = (options.submit.settings.display === 'block') ? errorContainer : input;

                $('html, body').animate({
                    scrollTop: handle.offset().top + offset
                }, duration);

            }

        }

        /**
         * Display all of the errors
         */
        function displayErrors() {

            for (var inputName in errors) {
                if (!errors.hasOwnProperty(inputName)) continue;
                displayOneError(inputName);
            }

        }

        /**
         * Remove an input error.
         *
         * @param {string} inputName Key reference to delete the error from "errors" global variable
         * @param {object} input jQuery object of the input
         * @param {object} label jQuery object of the input's label
         * @param {object} container jQuery object of the "errorList"
         * @param {string} [group] Name of the group if any (ex: used on input radio)
         */
        function resetOneError(inputName, input, label, container, group) {

            delete errors[inputName];

            if (container) {

                //window.Validation.hasScrolled = false;

                if (options.submit.settings.inputContainer) {
                    (group ? label : input).parentsUntil(node, options.submit.settings.inputContainer).removeClass(options.submit.settings.errorClass);
                }

                label && label.removeClass(options.submit.settings.errorClass);

                input.removeClass(options.submit.settings.errorClass);

                if (options.submit.settings.display === 'inline') {
                    container.find('[' + _data.errorList + ']').remove();
                }

            } else {

                if (!input) {
                    input = node.find('[name="' + inputName + '"]');

                    if (!input[0]) {

                        // {debug}
                        options.debug && window.Debug.log({
                            'node': node,
                            'function': 'resetOneError()',
                            'arguments': '[name="' + inputName + '"]',
                            'message': 'ERROR - Unable to find input by name "' + inputName + '"'
                        });
                        // {/debug}

                        return false;
                    }
                }

                input.trigger('coucou' + resetSuffix);
            }

        }

        /**
         * Remove all of the input error(s) display.
         */
        function resetErrors() {

            errors = [];
            window.Validation.hasScrolled = false;

            node.find('[' + _data.errorList + ']').remove();
            node.find('.' + options.submit.settings.errorClass).removeClass(options.submit.settings.errorClass);

        }

        /**
         * Submits the form once it succeeded the validation process.
         * Note:
         * - This function will be overridden if "options.submit.settings.onSubmit" is defined
         * - The node can't be submitted by jQuery since it has been disabled, use the form native submit function instead
         */
        function submitForm() {

            node[0].submit()

        }

        /**
         * Destroy the Validation instance
         *
         * @returns {boolean}
         */
        function destroy() {

            resetErrors();
            node.find('[' + _data.validation + '],[' + _data.regex + ']').off(delegateSuffix + ' ' + resetSuffix);

            node.find(options.submit.settings.button).off(delegateSuffix).on('click' + delegateSuffix, function () {
                $(this).closest('form')[0].submit();
            });

            //delete window.Validation.form[node.selector];

            return true;

        }

        /**
         * @private
         * Helper to get the value of an regular, radio or chackbox input
         *
         * @param input
         *
         * @returns {string} value
         */
        var _getInputValue = function (input) {

            var value;

            // Get the value or state of the input based on its type
            switch ($(input).attr('type')) {
                case 'checkbox':
                    value = ($(input).is(':checked')) ? 1 : '';
                    break;
                case 'radio':
                    value = node.find('input[name="' + $(input).attr('name') + '"]:checked').val() || '';
                    break;
                default:
                    value = $(input).val();
                    break;
            }

            return value;

        };

        /**
         * @private
         * Execute function once the timer is reached.
         * If the function is recalled before the timer ends, the first call will be canceled.
         */
        var _typeWatch = (function () {
            var timer = 0;
            return function (callback, ms) {
                clearTimeout(timer);
                timer = setTimeout(callback, ms);
            };
        })();

        /**
         * @private
         * Executes an anonymous function or a string reached from the window scope.
         *
         * @example
         * Note: These examples works with every callbacks (onInit, onError, onSubmit, onBeforeSubmit & onAfterSubmit)
         *
         * // An anonymous function inside the "onInit" option
         * onInit: function() { console.log(':D'); };
         *
         * * // myFunction() located on window.coucou scope
         * onInit: 'window.coucou.myFunction'
         *
         * // myFunction(a,b) located on window.coucou scope passing 2 parameters
         * onInit: ['window.coucou.myFunction', [':D', ':)']];
         *
         * // Anonymous function to execute a local function
         * onInit: function () { myFunction(':D'); }
         *
         * @param {string|array} callback The function to be called
         * @param {array} [extraParams] In some cases the function can be called with Extra parameters (onError)
         *
         * @returns {boolean}
         */
        var _executeCallback = function (callback, extraParams) {

            if (!callback) {
                return false;
            }

            var _callback;

            if (typeof callback === "function") {

                _callback = callback;

            } else if (typeof callback === "string" || callback instanceof Array) {

                _callback = window;

                if (typeof callback === "string") {
                    callback = [callback, []];
                }

                var _exploded = callback[0].split('.'),
                    _params = callback[1],
                    _isValid = true,
                    _splitIndex = 0;

                while (_splitIndex < _exploded.length) {

                    if (typeof _callback !== 'undefined') {
                        _callback = _callback[_exploded[_splitIndex++]];
                    } else {
                        _isValid = false;
                        break;
                    }
                }

                if (!_isValid || typeof _callback !== "function") {

                    // {debug}
                    options.debug && window.Debug.log({
                        'node': node,
                        'function': '_executeCallback()',
                        'arguments': JSON.stringify(callback),
                        'message': 'WARNING - Invalid callback function"'
                    });
                    // {/debug}

                    return false;
                }

            }

            _callback.apply(this, $.merge(_params || [], (extraParams) ? extraParams : []));
            return true;

        };

        /**
         * @private
         * Constructs Validation
         */
        this.__construct = function () {

            extendOptions();
            extendRules();
            extendMessages();

            delegateDynamicValidation();
            delegateValidation();

            // {debug}
            options.debug && window.Debug.print();
            // {/debug}

        }();

        return {

            /**
             * @public
             * Register error
             *
             * @param inputName
             * @param error
             */
            registerError: registerError,

            /**
             * @public
             * Display one error
             *
             * @param inputName
             */
            displayOneError: displayOneError,

            /**
             * @public
             * Display all errors
             */
            displayErrors: displayErrors,

            /**
             * @public
             * Remove one error
             */
            resetOneError: resetOneError,

            /**
             * @public
             * Remove all errors
             */
            resetErrors: resetErrors,

            /**
             * @public
             * Destroy the Validation instance
             */
            destroy: destroy

        };

    };

    // =================================================================================================================

    /**
     * @public
     * jQuery public function to implement the Validation on the selected node(s).
     *
     * @param {object} options To configure the Validation class.
     *
     * @return {object} Modified DOM element
     */
    $.fn.validate = $.validate = function (options) {

        return _api.validate(this, options);

    };

    /**
     * @public
     * jQuery public function to add one or multiple "data-validation" argument.
     *
     * @param {string|array} validation Arguments to add in the node's data-validation
     *
     * @return {object} Modified DOM element
     */
    $.fn.addValidation = function (validation) {

        return _api.addValidation(this, validation);

    };

    /**
     * @public
     * jQuery public function to remove one or multiple "data-validation" argument.
     *
     * @param {string|array} validation Arguments to remove in the node's data-validation
     *
     * @return {object} Modified DOM element
     */
    $.fn.removeValidation = function (validation) {

        return _api.removeValidation(this, validation);

    };

    /**
     * @public
     * jQuery public function to add one or multiple errors.
     *
     * @param {object} error Object of errors where the keys are the input names
     * @example
     * $('form#myForm').addError({
     *     'username': 'Invalid username, please choose another one.'
     * });
     *
     * @return {object} Modified DOM element
     */
    $.fn.addError = function (error) {

        return _api.addError(this, error);

    };

    /**
     * @public
     * jQuery public function to remove one or multiple errors.
     *
     * @param {array} error Array of errors where the keys are the input names
     * @example
     * $('form#myForm').removeError([
     *     'username'
     * ]);
     *
     * @return {object} Modified DOM element
     */
    $.fn.removeError = function (error) {

        return _api.removeError(this, error);

    };

    /**
     * @public
     * jQuery public function to add a validation rule.
     *
     * @example
     * $.alterValidationRules({
     *     rule: 'FILENAME',
     *     regex: /^[^\\/:\*\?<>\|\"\']*$/,
     *     message: '$ has an invalid filename.'
     * })
     *
     * @param {Object|Array} name
     */
    $.fn.alterValidationRules = $.alterValidationRules = function (rules) {

        if (!(rules instanceof Array)) {
            rules = [rules];
        }

        for (var i = 0; i < rules.length; i++) {
            _api.alterValidationRules(rules[i]);
        }

    };

    // =================================================================================================================

    /**
     * @private
     * API to handles "addValidation" and "removeValidation" on attribute "data-validation".
     * Note: Contains fail-safe operations to unify the validation parameter.
     *
     * @example
     * $.addValidation('NOTEMPTY, L>=6')
     * $.addValidation('[notempty, v>=6]')
     * $.removeValidation(['OPTIONAL', 'V>=6'])
     *
     * @returns {object} Updated DOM object
     */
    var _api = {

        /**
         * @private
         * This function unifies the data through the validation process.
         * String, Uppercase and spaceless.
         *
         * @param {string|array} validation
         *
         * @returns {string}
         */
        _formatValidation: function (validation) {

            validation = validation.toString().replace(/\s/g, '');

            if (validation.charAt(0) === "[" && validation.charAt(validation.length - 1) === "]") {
                validation = validation.replace(/^\[|\]$/g, '');
            }

            return validation;

        },

        /**
         * @private
         * Splits the validation into an array, Uppercase the rules if they are not comparisons
         *
         * @param {string|array} validation
         *
         * @returns {array} Formatted validation keys
         */
        _splitValidation: function (validation) {

            var validationArray = this._formatValidation(validation).split(','),
                oneValidation;

            for (var i = 0; i < validationArray.length; i++) {
                oneValidation = validationArray[i];
                if (/^[a-z]+$/i.test(oneValidation)) {
                    validationArray[i] = oneValidation.toUpperCase();
                }
            }

            return validationArray;
        },

        /**
         * @private
         * Joins the validation array to create the "data-validation" value
         *
         * @param {array} validation
         *
         * @returns {string}
         */
        _joinValidation: function (validation) {

            return '[' + validation.join(', ') + ']';

        },

        /**
         * API method to attach the submit event type on the specified node.
         * Note: Clears the previous event regardless to avoid double submits or unwanted behaviors.
         *
         * @param {object} node jQuery object(s)
         * @param {object} options To configure the Validation class.
         *
         * @returns {*}
         */
        validate: function (node, options) {

            if (typeof node === "function") {

                if (!options.submit.settings.form) {

                    // {debug}
                    window.Debug.log({
                        'node': node,
                        'function': '$.validate()',
                        'arguments': '',
                        'message': 'Undefined property "options.submit.settings.form - Validation dropped'
                    });

                    window.Debug.print();
                    // {/debug}

                    return;
                }

                node = $(options.submit.settings.form);

                if (!node[0] || node[0].nodeName.toLowerCase() !== "form") {

                    // {debug}
                    window.Debug.log({
                        'function': '$.validate()',
                        'arguments': JSON.stringify(options.submit.settings.form),
                        'message': 'Unable to find jQuery form element - Validation dropped'
                    });

                    window.Debug.print();
                    // {/debug}

                    return;
                }

            } else if (typeof node[0] === 'undefined') {

                // {debug}
                window.Debug.log({
                    'node': node,
                    'function': '$.validate()',
                    'arguments': '$("' + node.selector + '").validate()',
                    'message': 'Unable to find jQuery form element - Validation dropped'
                });

                window.Debug.print();
                // {/debug}

                return;
            }

            if (options === "destroy") {

                if (!window.Validation.form[node.selector]) {

                    // {debug}
                    window.Debug.log({
                        'node': node,
                        'function': '$.validate("destroy")',
                        'arguments': '',
                        'message': 'Unable to destroy "' + node.selector + '", perhaps it\'s already destroyed?'
                    });

                    window.Debug.print();
                    // {/debug}

                    return;
                }

                window.Validation.form[node.selector].destroy();

                return;

            }

            return node.each(function () {
                window.Validation.form[node.selector] = new Validation($(this), options);
            });

        },

        /**
         * API method to handle the addition of "data-validation" arguments.
         * Note: ONLY the predefined validation arguments are allowed to be added
         * inside the "data-validation" attribute (see configuration).
         *
         * @param {object} node jQuery objects
         * @param {string|array} validation arguments to add in the node(s) "data-validation"
         *
         * @returns {*}
         */
        addValidation: function (node, validation) {

            var self = this;

            validation = self._splitValidation(validation);

            if (!validation) {
                return false;
            }

            return node.each(function () {

                var $this = $(this),
                    validationData = $this.attr(_data.validation),
                    validationArray = (validationData && validationData.length) ? self._splitValidation(validationData) : [],
                    oneValidation;

                for (var i = 0; i < validation.length; i++) {

                    oneValidation = self._formatValidation(validation[i]);

                    if ($.inArray(oneValidation, validationArray) === -1) {
                        validationArray.push(oneValidation);
                    }
                }

                if (validationArray.length) {
                    $this.attr(_data.validation, self._joinValidation(validationArray));
                }

            });

        },

        /**
         * API method to handle the removal of "data-validation" arguments.
         *
         * @param {object} node jQuery objects
         * @param {string|array} validation arguments to remove in the node(s) "data-validation"
         *
         * @returns {*}
         */
        removeValidation: function (node, validation) {

            var self = this;

            validation = self._splitValidation(validation);
            if (!validation) {
                return false;
            }

            return node.each(function () {

                var $this = $(this),
                    validationData = $this.attr(_data.validation),
                    validationArray = (validationData && validationData.length) ? self._splitValidation(validationData) : [],
                    oneValidation,
                    validationIndex;

                if (!validationArray.length) {
                    $this.removeAttr(_data.validation);
                    return true;
                }

                for (var i = 0; i < validation.length; i++) {
                    oneValidation = self._formatValidation(validation[i]);
                    validationIndex = $.inArray(oneValidation, validationArray);
                    if (validationIndex !== -1) {
                        validationArray.splice(validationIndex, 1);
                    }

                }

                if (!validationArray.length) {
                    $this.removeAttr(_data.validation);
                    return true;
                }

                $this.attr(_data.validation, self._joinValidation(validationArray));

            });

        },

        /**
         * API method to manually trigger a form error.
         * Note: The same form jQuery selector MUST be used to recuperate the Validation configuration.
         *
         * @example
         * $('#form-signup_v3').addError({
         *     'inputName': 'my error message',
         *     'inputName2': [
         *         'first error message',
         *         'second error message'
         *     ]
         * })
         *
         * @param {object} node jQuery object
         * @param {object} error Object of errors to add on the node
         *
         * @returns {*}
         */
        addError: function (node, error) {

            if (!window.Validation.form[node.selector]) {

                // {debug}
                window.Debug.log({
                    'node': node,
                    'function': '$.addError()',
                    'arguments': 'window.Validation.form[' + JSON.stringify(node.selector) + ']',
                    'message': 'ERROR - Invalid node selector'
                });

                window.Debug.print();
                // {/debug}

                return false;
            }

            if (typeof error !== "object" || Object.prototype.toString.call(error) !== "[object Object]") {

                // {debug}
                window.Debug.log({
                    'node': node,
                    'function': '$.addError()',
                    'arguments': 'window.Validation.form[' + JSON.stringify(node.selector) + ']',
                    'message': 'ERROR - Invalid argument, must be type object'
                });

                window.Debug.print();
                // {/debug}

                return false;
            }

            var input,
                onlyOnce = true;
            for (var inputName in error) {

                if (!error.hasOwnProperty(inputName)) {
                    continue;
                }

                if (!(error[inputName] instanceof Array)) {
                    error[inputName] = [error[inputName]];
                }

                input = $(node.selector).find('[name="' + inputName + '"]');
                if (!input[0]) {

                    // {debug}
                    window.Debug.log({
                        'node': node,
                        'function': '$.addError()',
                        'arguments': JSON.stringify(inputName),
                        'message': 'ERROR - Unable to find ' + '$(' + node.selector + ').find("[name="' + inputName + '"]")'
                    });

                    window.Debug.print();
                    // {/debug}

                    continue;
                }

                if (onlyOnce) {
                    window.Validation.hasScrolled = false;
                    onlyOnce = false;
                }

                window.Validation.form[node.selector].resetOneError(inputName, input);

                for (var i = 0; i < error[inputName].length; i++) {

                    if (typeof error[inputName][i] !== "string") {

                        // {debug}
                        window.Debug.log({
                            'node': node,
                            'function': '$.addError()',
                            'arguments': JSON.stringify(error[inputName][i]),
                            'message': 'ERROR - Invalid error object property - Accepted format: {"inputName": "errorString"} or {"inputName": ["errorString", "errorString"]}'
                        });

                        window.Debug.print();
                        // {/debug}

                        continue;
                    }

                    window.Validation.form[node.selector].registerError(inputName, error[inputName][i]);

                }

                window.Validation.form[node.selector].displayOneError(inputName);

            }

        },

        /**
         * API method to manually remove a form error.
         * Note: The same form jQuery selector MUST be used to recuperate the Validation configuration.
         *
         * @example
         * $('#form-signin_v2').removeError([
         *     'signin_v2[username]',
         *     'signin_v2[password]'
         * ])
         *
         * @param {object} node jQuery object
         * @param {object} inputName Object of errors to remove on the node
         *
         * @returns {*}
         */
        removeError: function (node, inputName) {

            if (!window.Validation.form[node.selector]) {

                // {debug}
                window.Debug.log({
                    'node': node,
                    'function': '$.removeError()',
                    'arguments': 'window.Validation.form[' + JSON.stringify(node.selector) + ']',
                    'message': 'ERROR - Invalid node selector'
                });

                window.Debug.print();
                // {/debug}

                return false;
            }

            if (!inputName) {
                window.Validation.form[node.selector].resetErrors();
                return false;
            }

            if (typeof inputName === "object" && Object.prototype.toString.call(inputName) !== "[object Array]") {

                // {debug}
                window.Debug.log({
                    'node': node,
                    'function': '$.removeError()',
                    'arguments': JSON.stringify(inputName),
                    'message': 'ERROR - Invalid inputName, must be type String or Array'
                });

                window.Debug.print();
                // {/debug}

                return false;
            }

            if (!(inputName instanceof Array)) {
                inputName = [inputName];
            }

            var input;
            for (var i = 0; i < inputName.length; i++) {

                input = $(node.selector).find('[name="' + inputName[i] + '"]');
                if (!input[0]) {

                    // {debug}
                    window.Debug.log({
                        'node': node,
                        'function': '$.removeError()',
                        'arguments': JSON.stringify(inputName[i]),
                        'message': 'ERROR - Unable to find ' + '$(' + node.selector + ').find("[name="' + inputName[i] + '"]")'
                    });

                    window.Debug.print();
                    // {/debug}

                    continue;
                }

                window.Validation.form[node.selector].resetOneError(inputName[i], input);

            }

        },

        /**
         * API method to add a validation rule.
         *
         * @example
         * $.alterValidationRules({
         *     rule: 'FILENAME',
         *     regex: /^[^\\/:\*\?<>\|\"\']*$/,
         *     message: '$ has an invalid filename.'
         * })
         *
         * @param {object} ruleObj
         */
        alterValidationRules: function (ruleObj) {

            if (!ruleObj.rule || (!ruleObj.regex && !ruleObj.message)) {
                // {debug}
                window.Debug.log({
                    'function': '$.alterValidationRules()',
                    'message': 'ERROR - Missing one or multiple parameter(s) {rule, regex, message}'
                });
                window.Debug.print();
                // {/debug}
                return false;
            }

            ruleObj.rule = ruleObj.rule.toUpperCase();

            if (ruleObj.regex) {

                var regex = _buildRegexFromString(ruleObj.regex);

                if (!(regex instanceof RegExp)) {
                    // {debug}
                    window.Debug.log({
                        'function': '$.alterValidationRules(rule)',
                        'arguments': regex.toString(),
                        'message': 'ERROR - Invalid rule'
                    });
                    window.Debug.print();
                    // {/debug}
                    return false;
                }

                _rules[ruleObj.rule] = regex;
            }

            if (ruleObj.message) {
                _messages[ruleObj.rule] = ruleObj.message;
            }

            return true;
        }

    };

    /**
     * @private
     * Converts string into a regex
     *
     * @param {String|Object} regex
     * @returns {Object|Boolean} rule
     */
    function _buildRegexFromString(regex) {

        if (!regex || (typeof regex !== "string" && !(regex instanceof RegExp))) {
            _regexDebug();
            return false;
        }

        if (typeof regex !== 'string') {
            regex = regex.toString();
        }

        var separator = regex.charAt(0),
            index = regex.length - 1,
            pattern,
            modifier,
            rule;

        while (index > 0) {
            if (/[gimsxeU]/.test(regex.charAt(index))) {
                index--;
            } else {
                break;
            }
        }

        if (regex.charAt(index) !== separator) {
            separator = null;
        }

        if (separator && index !== regex.length - 1) {
            modifier = regex.substr(index + 1, regex.length - 1);
        }

        if (separator) {
            pattern = regex.substr(1, index - 1);
        } else {
            pattern = regex;
        }

        try {
            rule = new RegExp(pattern, modifier);
        } catch (error) {
            _regexDebug();
            return false;
        }

        return rule;

        function _regexDebug() {
            // {debug}
            window.Debug.log({
                'function': '_buildRegexFromString()',
                'arguments': '{pattern: {' + (pattern || '') + '}, modifier: {' + (modifier || '') + '}',
                'message': 'WARNING - Invalid regex given: ' + regex
            });
            window.Debug.print();
            // {/debug}
        }

    }

    // {debug}
    window.Debug = {

        table: {},
        log: function (debugObject) {

            if (!debugObject.message || typeof debugObject.message !== "string") {
                return false;
            }

            this.table[debugObject.message] = $.extend(
                Object.preventExtensions(
                    {
                        'node': '',
                        'function': '',
                        'arguments': ''
                    }
                ), debugObject
            );

        },
        print: function () {

            if ($.isEmptyObject(this.table)) {
                return false;
            }

            if (console.group !== undefined || console.table !== undefined) {

                console.groupCollapsed('--- jQuery Form Validation Debug ---');

                if (console.table) {
                    console.table(this.table);
                } else {
                    $.each(this.table, function (index, data) {
                        console.log(data['Name'] + ': ' + data['Execution Time'] + 'ms');
                    });
                }

                console.groupEnd();

            } else {
                console.log('Debug is not available on your current browser, try the most recent version of Chrome or Firefox.');
            }

            this.table = {};

        }

    };
    // {/debug}

    String.prototype.capitalize = function () {
        return this.charAt(0).toUpperCase() + this.slice(1);
    };

    if (!Array.prototype.indexOf) {
        Array.prototype.indexOf = function (elt /*, from*/) {
            var len = this.length >>> 0;

            var from = Number(arguments[1]) || 0;
            from = (from < 0)
                ? Math.ceil(from)
                : Math.floor(from);
            if (from < 0)
                from += len;

            for (; from < len; from++) {
                if (from in this &&
                    this[from] === elt)
                    return from;
            }
            return -1;
        };
    }

    // {debug}
    if (!JSON && !JSON.stringify) {
        JSON.stringify = function (obj) {
            var t = typeof (obj);
            if (t !== "object" || obj === null) {
                // simple data type
                if (t === "string") {
                    obj = '"' + obj + '"';
                }
                return String(obj);
            }
            else {
                var n, v, json = [], arr = (obj && obj.constructor === Array);
                for (n in obj) {
                    if (true) {
                        v = obj[n];
                        t = typeof(v);
                        if (t === "string") {
                            v = '"' + v + '"';
                        }
                        else if (t === "object" && v !== null) {
                            v = JSON.stringify(v);
                        }
                        json.push((arr ? "" : '"' + n + '": ') + String(v));
                    }
                }
                return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
            }
        };
    }
    // {/debug}

}(window, document, window.jQuery));
/* End */
;
; /* Start:"a:4:{s:4:"full";s:70:"/bitrix/js/api.feedback/autosize/jquery.autosize.min.js?14326109802111";s:6:"source";s:55:"/bitrix/js/api.feedback/autosize/jquery.autosize.min.js";s:3:"min";s:0:"";s:3:"map";s:0:"";}"*/
/*!
	Autosize 2.0.0
	license: MIT
	http://www.jacklmoore.com/autosize
*/
!function(e,t){"use strict";"function"==typeof define&&define.amd?define([],t):"object"==typeof exports?module.exports=t():e.autosize=t()}(this,function(){function e(e){function t(){var t=window.getComputedStyle(e,null);"vertical"===t.resize?e.style.resize="none":"both"===t.resize&&(e.style.resize="horizontal"),e.style.wordWrap="break-word";var i=e.style.width;e.style.width="0px",e.offsetWidth,e.style.width=i,n="none"!==t.maxHeight?parseFloat(t.maxHeight):!1,r="content-box"===t.boxSizing?-(parseFloat(t.paddingTop)+parseFloat(t.paddingBottom)):parseFloat(t.borderTopWidth)+parseFloat(t.borderBottomWidth),o()}function o(){var t=e.style.height,o=document.documentElement.scrollTop,i=document.body.scrollTop;e.style.height="auto";var s=e.scrollHeight+r;if(n!==!1&&s>n?(s=n,"scroll"!==e.style.overflowY&&(e.style.overflowY="scroll")):"hidden"!==e.style.overflowY&&(e.style.overflowY="hidden"),e.style.height=s+"px",document.documentElement.scrollTop=o,document.body.scrollTop=i,t!==e.style.height){var d=document.createEvent("Event");d.initEvent("autosize.resized",!0,!1),e.dispatchEvent(d)}}if(e&&e.nodeName&&"TEXTAREA"===e.nodeName&&!e.hasAttribute("data-autosize-on")){var n,r;"onpropertychange"in e&&"oninput"in e&&e.addEventListener("keyup",o),window.addEventListener("resize",o),e.addEventListener("input",o),e.addEventListener("autosize.update",o),e.addEventListener("autosize.destroy",function(t){window.removeEventListener("resize",o),e.removeEventListener("input",o),e.removeEventListener("keyup",o),e.removeEventListener("autosize.destroy"),Object.keys(t).forEach(function(o){e.style[o]=t[o]}),e.removeAttribute("data-autosize-on")}.bind(e,{height:e.style.height,overflow:e.style.overflow,overflowY:e.style.overflowY,wordWrap:e.style.wordWrap,resize:e.style.resize})),e.setAttribute("data-autosize-on",!0),e.style.overflow="hidden",e.style.overflowY="hidden",t()}}return"function"!=typeof window.getComputedStyle?function(e){return e}:function(t){return t&&t.length?Array.prototype.forEach.call(t,e):t&&t.nodeName&&e(t),t}});
/* End */
;
; /* Start:"a:4:{s:4:"full";s:76:"/bitrix/js/api.feedback/placeholder/jquery.placeholder.min.js?14326109807392";s:6:"source";s:61:"/bitrix/js/api.feedback/placeholder/jquery.placeholder.min.js";s:3:"min";s:0:"";s:3:"map";s:0:"";}"*/
/*! http://mths.be/placeholder v2.1.1 by @mathias */
(function(factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        factory(require('jquery'));
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function($) {

    // Opera Mini v7 doesn't support placeholder although its DOM seems to indicate so
    var isOperaMini = Object.prototype.toString.call(window.operamini) == '[object OperaMini]';
    var isInputSupported = 'placeholder' in document.createElement('input') && !isOperaMini;
    var isTextareaSupported = 'placeholder' in document.createElement('textarea') && !isOperaMini;
    var valHooks = $.valHooks;
    var propHooks = $.propHooks;
    var hooks;
    var placeholder;

    if (isInputSupported && isTextareaSupported) {

        placeholder = $.fn.placeholder = function() {
            return this;
        };

        placeholder.input = placeholder.textarea = true;

    } else {

        var settings = {};

        placeholder = $.fn.placeholder = function(options) {

            var defaults = {customClass: 'placeholder'};
            settings = $.extend({}, defaults, options);

            var $this = this;
            $this
                .filter((isInputSupported ? 'textarea' : ':input') + '[placeholder]')
                .not('.'+settings.customClass)
                .bind({
                    'focus.placeholder': clearPlaceholder,
                    'blur.placeholder': setPlaceholder
                })
                .data('placeholder-enabled', true)
                .trigger('blur.placeholder');
            return $this;
        };

        placeholder.input = isInputSupported;
        placeholder.textarea = isTextareaSupported;

        hooks = {
            'get': function(element) {
                var $element = $(element);

                var $passwordInput = $element.data('placeholder-password');
                if ($passwordInput) {
                    return $passwordInput[0].value;
                }

                return $element.data('placeholder-enabled') && $element.hasClass(settings.customClass) ? '' : element.value;
            },
            'set': function(element, value) {
                var $element = $(element);

                var $passwordInput = $element.data('placeholder-password');
                if ($passwordInput) {
                    return $passwordInput[0].value = value;
                }

                if (!$element.data('placeholder-enabled')) {
                    return element.value = value;
                }
                if (value === '') {
                    element.value = value;
                    // Issue #56: Setting the placeholder causes problems if the element continues to have focus.
                    if (element != safeActiveElement()) {
                        // We can't use `triggerHandler` here because of dummy text/password inputs :(
                        setPlaceholder.call(element);
                    }
                } else if ($element.hasClass(settings.customClass)) {
                    clearPlaceholder.call(element, true, value) || (element.value = value);
                } else {
                    element.value = value;
                }
                // `set` can not return `undefined`; see http://jsapi.info/jquery/1.7.1/val#L2363
                return $element;
            }
        };

        if (!isInputSupported) {
            valHooks.input = hooks;
            propHooks.value = hooks;
        }
        if (!isTextareaSupported) {
            valHooks.textarea = hooks;
            propHooks.value = hooks;
        }

        $(function() {
            // Look for forms
            $(document).delegate('form', 'submit.placeholder', function() {
                // Clear the placeholder values so they don't get submitted
                var $inputs = $('.'+settings.customClass, this).each(clearPlaceholder);
                setTimeout(function() {
                    $inputs.each(setPlaceholder);
                }, 10);
            });
        });

        // Clear placeholder values upon page reload
        $(window).bind('beforeunload.placeholder', function() {
            $('.'+settings.customClass).each(function() {
                this.value = '';
            });
        });

    }

    function args(elem) {
        // Return an object of element attributes
        var newAttrs = {};
        var rinlinejQuery = /^jQuery\d+$/;
        $.each(elem.attributes, function(i, attr) {
            if (attr.specified && !rinlinejQuery.test(attr.name)) {
                newAttrs[attr.name] = attr.value;
            }
        });
        return newAttrs;
    }

    function clearPlaceholder(event, value) {
        var input = this;
        var $input = $(input);
        if (input.value == $input.attr('placeholder') && $input.hasClass(settings.customClass)) {
            if ($input.data('placeholder-password')) {
                $input = $input.hide().nextAll('input[type="password"]:first').show().attr('id', $input.removeAttr('id').data('placeholder-id'));
                // If `clearPlaceholder` was called from `$.valHooks.input.set`
                if (event === true) {
                    return $input[0].value = value;
                }
                $input.focus();
            } else {
                input.value = '';
                $input.removeClass(settings.customClass);
                input == safeActiveElement() && input.select();
            }
        }
    }

    function setPlaceholder() {
        var $replacement;
        var input = this;
        var $input = $(input);
        var id = this.id;
        if (input.value === '') {
            if (input.type === 'password') {
                if (!$input.data('placeholder-textinput')) {
                    try {
                        $replacement = $input.clone().attr({ 'type': 'text' });
                    } catch(e) {
                        $replacement = $('<input>').attr($.extend(args(this), { 'type': 'text' }));
                    }
                    $replacement
                        .removeAttr('name')
                        .data({
                            'placeholder-password': $input,
                            'placeholder-id': id
                        })
                        .bind('focus.placeholder', clearPlaceholder);
                    $input
                        .data({
                            'placeholder-textinput': $replacement,
                            'placeholder-id': id
                        })
                        .before($replacement);
                }
                $input = $input.removeAttr('id').hide().prevAll('input[type="text"]:first').attr('id', id).show();
                // Note: `$input[0] != input` now!
            }
            $input.addClass(settings.customClass);
            $input[0].value = $input.attr('placeholder');
        } else {
            $input.removeClass(settings.customClass);
        }
    }

    function safeActiveElement() {
        // Avoid IE9 `document.activeElement` of death
        // https://github.com/mathiasbynens/jquery-placeholder/pull/99
        try {
            return document.activeElement;
        } catch (exception) {}
    }

}));

/* End */
;
; /* Start:"a:4:{s:4:"full";s:71:"/bitrix/js/api.feedback/cssmodal/jquery.cssmodal.min.js?143261098017939";s:6:"source";s:55:"/bitrix/js/api.feedback/cssmodal/jquery.cssmodal.min.js";s:3:"min";s:0:"";s:3:"map";s:0:"";}"*/
/*!
 * CSS Modal
 * http://drublic.github.com/css-modal
 *
 * @author Hans Christian Reinl - @drublic
 * @version 1.3.0
 */

(function (global, $) {

    'use strict';

    /*
     * Storage for functions and attributes
     */
    var modal = {

        activeElement: undefined, // Store for currently active element
        lastActive: undefined, // Store for last active elemet
        stackedElements: [], // Store for stacked elements

        // All elements that can get focus, can be tabbed in a modal
        tabbableElements: 'a[href], area[href], input:not([disabled]),' +
        'select:not([disabled]), textarea:not([disabled]),' +
        'button:not([disabled]), iframe, object, embed, *[tabindex],' +
        '*[contenteditable]',

        /*
         * Polyfill addEventListener for IE8 (only very basic)
         * @param event {string} event type
         * @param element {Node} node to fire event on
         * @param callback {function} gets fired if event is triggered
         */
        on: function (event, elements, callback) {
            var i = 0;

            if (typeof event !== 'string') {
                throw new Error('Type error: `event` has to be a string');
            }

            if (typeof callback !== 'function') {
                throw new Error('Type error: `callback` has to be a function');
            }

            if (!elements) {
                return;
            }

            // Make elements an array and attach event listeners
            if (!elements.length) {
                elements = [elements];
            }

            for (; i < elements.length; i++) {

                // If jQuery is supported
                if ($) {
                    $(elements[i]).on(event, callback);

                    // Default way to support events
                } else if ('addEventListener' in elements[i]) {
                    elements[i].addEventListener(event, callback, false);
                }

            }
        },

        /*
         * Convenience function to trigger event
         * @param event {string} event type
         * @param modal {string} id of modal that the event is triggered on
         */
        trigger: function (event, modal) {
            var eventTrigger;
            var eventParams = {
                detail: {
                    'modal': modal
                }
            };

            // Use jQuery to fire the event if it is included
            if ($) {
                $(document).trigger(event, eventParams);

                // Use createEvent if supported (that's mostly the case)
            } else if (document.createEvent) {
                eventTrigger = document.createEvent('CustomEvent');

                eventTrigger.initCustomEvent(event, false, false, {
                    'modal': modal
                });

                document.dispatchEvent(eventTrigger);

                // Use CustomEvents if supported
            } else {
                eventTrigger = new CustomEvent(event, eventParams);

                document.dispatchEvent(eventTrigger);
            }
        },

        /*
         * Convenience function to add a class to an element
         * @param element {Node} element to add class to
         * @param className {string}
         */
        addClass: function (element, className) {
            if (element && !element.className.match(className)) {
                element.className += ' ' + className;
            }
        },

        /*
         * Convenience function to remove a class from an element
         * @param element {Node} element to remove class off
         * @param className {string}
         */
        removeClass: function (element, className) {
            element.className = element.className.replace(className, '').replace('  ', ' ');
        },

        /**
         * Convenience function to check if an element has a class
         * @param  {Node}    element   Element to check classname on
         * @param  {string}  className Class name to check for
         * @return {Boolean}           true, if class is available on modal
         */
        hasClass: function (element, className) {
            return !!element.className.match(className);
        },

        /*
         * Focus modal
         */
        setFocus: function () {
            if (modal.activeElement) {

                // Set element with last focus
                modal.lastActive = document.activeElement;

                // New focussing
                modal.activeElement.focus();

                // Add handler to keep the focus
                modal.keepFocus(modal.activeElement);
            }
        },

        /*
         * Unfocus
         */
        removeFocus: function () {
            if (modal.lastActive) {
                modal.lastActive.focus();
            }
        },

        /*
         * Keep focus inside the modal
         * @param element {node} element to keep focus in
         */
        keepFocus: function (element) {
            var allTabbableElements = [];

            // Don't keep the focus if the browser is unable to support
            // CSS3 selectors
            try {
                allTabbableElements = element.querySelectorAll(modal.tabbableElements);
            } catch (ex) {
                return;
            }

            var firstTabbableElement = modal.getFirstElementVisible(allTabbableElements);
            var lastTabbableElement = modal.getLastElementVisible(allTabbableElements);

            var focusHandler = function (event) {
                var keyCode = event.which || event.keyCode;

                // TAB pressed
                if (keyCode !== 9) {
                    return;
                }

                // Polyfill to prevent the default behavior of events
                event.preventDefault = event.preventDefault || function () {
                    event.returnValue = false;
                };

                // Move focus to first element that can be tabbed if Shift isn't used
                if (event.target === lastTabbableElement && !event.shiftKey) {
                    event.preventDefault();
                    firstTabbableElement.focus();

                    // Move focus to last element that can be tabbed if Shift is used
                } else if (event.target === firstTabbableElement && event.shiftKey) {
                    event.preventDefault();
                    lastTabbableElement.focus();
                }
            };

            modal.on('keydown', element, focusHandler);
        },

        /*
         * Return the first visible element of a nodeList
         *
         * @param nodeList The nodelist to parse
         * @return {Node|null} Returns a specific node or null if no element found
         */
        getFirstElementVisible: function (nodeList) {
            var nodeListLength = nodeList.length;

            // If the first item is not visible
            if (!modal.isElementVisible(nodeList[0])) {
                for (var i = 1; i < nodeListLength - 1; i++) {

                    // Iterate elements in the NodeList, return the first visible
                    if (modal.isElementVisible(nodeList[i])) {
                        return nodeList[i];
                    }
                }
            } else {
                return nodeList[0];
            }

            return null;
        },

        /*
         * Return the last visible element of a nodeList
         *
         * @param nodeList The nodelist to parse
         * @return {Node|null} Returns a specific node or null if no element found
         */
        getLastElementVisible: function (nodeList) {
            var nodeListLength = nodeList.length;
            var lastTabbableElement = nodeList[nodeListLength - 1];

            // If the last item is not visible
            if (!modal.isElementVisible(lastTabbableElement)) {
                for (var i = nodeListLength - 1; i >= 0; i--) {

                    // Iterate elements in the NodeList, return the first visible
                    if (modal.isElementVisible(nodeList[i])) {
                        return nodeList[i];
                    }
                }
            } else {
                return lastTabbableElement;
            }

            return null;
        },

        /*
         * Convenience function to check if an element is visible
         *
         * Test idea taken from jQuery 1.3.2 source code
         *
         * @param element {Node} element to test
         * @return {boolean} is the element visible or not
         */
        isElementVisible: function (element) {
            return !(element.offsetWidth === 0 && element.offsetHeight === 0);
        },

        /*
         * Mark modal as active
         * @param element {Node} element to set active
         */
        setActive: function (element) {
            modal.addClass(element, 'is-active');
            modal.activeElement = element;

            // Update aria-hidden
            modal.activeElement.setAttribute('aria-hidden', 'false');

            // Set the focus to the modal
            modal.setFocus(element.id);

            // Fire an event
            modal.trigger('cssmodal:show', modal.activeElement);
        },

        /*
         * Unset previous active modal
         * @param isStacked          {boolean} `true` if element is stacked above another
         * @param shouldNotBeStacked {boolean} `true` if next element should be stacked
         */
        unsetActive: function (isStacked, shouldNotBeStacked) {
            modal.removeClass(document.documentElement, 'has-overlay');

            if (modal.activeElement) {
                modal.removeClass(modal.activeElement, 'is-active');

                // Fire an event
                modal.trigger('cssmodal:hide', modal.activeElement);

                // Update aria-hidden
                modal.activeElement.setAttribute('aria-hidden', 'true');

                // Unfocus
                modal.removeFocus();

                // Make modal stacked if needed
                if (isStacked && !shouldNotBeStacked) {
                    modal.stackModal(modal.activeElement);
                }

                // If there are any stacked elements
                if (!isStacked && modal.stackedElements.length > 0) {
                    modal.unstackModal();
                }

                // Reset active element
                modal.activeElement = null;
            }
        },

        /*
         * Stackable modal
         * @param stackableModal {node} element to be stacked
         */
        stackModal: function (stackableModal) {
            modal.addClass(stackableModal, 'is-stacked');

            // Set modal as stacked
            modal.stackedElements.push(modal.activeElement);
        },

        /*
         * Reactivate stacked modal
         */
        unstackModal: function () {
            var stackedCount = modal.stackedElements.length;
            var lastStacked = modal.stackedElements[stackedCount - 1];

            modal.removeClass(lastStacked, 'is-stacked');

            // Set hash to modal, activates the modal automatically
            global.location.hash = lastStacked.id;

            // Remove modal from stackedElements array
            modal.stackedElements.splice(stackedCount - 1, 1);
        },

        /*
         * When displaying modal, prevent background from scrolling
         * @param  {Object} event The incoming hashChange event
         * @return {void}
         */
        mainHandler: function (event, noHash) {
            var hash = global.location.hash.replace('#', '');
            var index = 0;
            var tmp = [];
            var modalElement;
            var modalChild;

            // JS-only: no hash present
            if (noHash) {
                hash = event.currentTarget.getAttribute('href').replace('#', '');
            }

            modalElement = document.getElementById(hash);

            // Check if the hash contains an index
            if (hash.indexOf('/') !== -1) {
                tmp = hash.split('/');
                index = tmp.pop();
                hash = tmp.join('/');

                // Remove the index from the hash...
                modalElement = document.getElementById(hash);

                // ... and store the index as a number on the element to
                // make it accessible for plugins
                if (!modalElement) {
                    throw new Error('ReferenceError: element "' + hash + '" does not exist!');
                }

                modalElement.index = (1 * index);
            }

            // If the hash element exists
            if (modalElement) {

                // Polyfill to prevent the default behavior of events
                try {
                    event.preventDefault();
                } catch (ex) {
                    event.returnValue = false;
                }

                // Get first element in selected element
                modalChild = modalElement.children[0];

                // When we deal with a modal and body-class `has-overlay` is not set
                if (modalChild && modalChild.className.match(/modal-inner/)) {

                    // Make previous element stackable if it is not the same modal
                    modal.unsetActive(
                        !modal.hasClass(modalElement, 'is-active'),
                        (modalElement.getAttribute('data-stackable') === 'false')
                    );

                    // Set an html class to prevent scrolling
                    modal.addClass(document.documentElement, 'has-overlay');

                    // Set scroll position for modal
                    modal._currentScrollPositionY = global.scrollY;
                    modal._currentScrollPositionX = global.scrollX;

                    // Mark the active element
                    modal.setActive(modalElement);
                    modal.activeElement._noHash = noHash;
                }
            } else {

                // If activeElement is already defined, delete it
                modal.unsetActive();
            }

            return true;
        },

        /**
         * Inject iframes
         */
        injectIframes: function () {
            var iframes = document.querySelectorAll('[data-iframe-src]');
            var iframe;
            var i = 0;

            for (; i < iframes.length; i++) {
                iframe = document.createElement('iframe');

                iframe.src = iframes[i].getAttribute('data-iframe-src');
                iframe.setAttribute('webkitallowfullscreen', true);
                iframe.setAttribute('mozallowfullscreen', true);
                iframe.setAttribute('allowfullscreen', true);

                iframes[i].appendChild(iframe);
            }
        },

        /**
         * Listen to all relevant events
         * @return {void}
         */
        init: function () {

            /*
             * Hide overlay when ESC is pressed
             */
            this.on('keyup', document, function (event) {
                var hash = global.location.hash.replace('#', '');

                // If key ESC is pressed
                if (event.keyCode === 27) {
                    if (modal.activeElement && hash === modal.activeElement.id) {
                        global.location.hash = '!';
                    } else {
                        modal.unsetActive();
                    }

                    if (modal.lastActive) {
                        return false;
                    }

                    // Unfocus
                    modal.removeFocus();
                }
            }, false);

            /**
             * Trigger main handler on click if hash is deactivated
             */
            this.on('click', document.querySelectorAll('[data-cssmodal-nohash]'), function (event) {
                modal.mainHandler(event, true);
            });

            // And close modal without hash
            this.on('click', document.querySelectorAll('.modal-close'), function (event) {
                if (modal.activeElement._noHash){
                    modal.mainHandler(event, true);
                }
            });

            /*
             * Trigger main handler on load and hashchange
             */
            this.on('hashchange', global, modal.mainHandler);
            this.on('load', global, modal.mainHandler);

            /**
             * Prevent scrolling when modal is active
             * @return {void}
             */
            global.onscroll = global.onmousewheel = function () {
                if (document.documentElement.className.match(/has-overlay/)) {
                    global.scrollTo(modal._currentScrollPositionX, modal._currentScrollPositionY);
                }
            };

            /**
             * Inject iframes
             */
            modal.injectIframes();
        }
    };

    /*
     * AMD, module loader, global registration
     */

    // Expose modal for loaders that implement the Node module pattern.
    if (typeof module === 'object' && module && typeof module.exports === 'object') {
        module.exports = modal;

        // Register as an AMD module
    } else if (typeof define === 'function' && define.amd) {
        define('CSSModal', [], function () {

            // We use jQuery if the browser doesn't support CustomEvents
            if (!global.CustomEvent && !$) {
                throw new Error('This browser doesn\'t support CustomEvent - please include jQuery.');
            }

            modal.init();

            return modal;
        });

        // Export CSSModal into global space
    } else if (typeof global === 'object' && typeof global.document === 'object') {
        global.CSSModal = modal;
        modal.init();
    }

}(window, window.jQuery));

/* End */
;
; /* Start:"a:4:{s:4:"full";s:75:"/bitrix/js/api.feedback/formstyler/jquery.formstyler.min.js?143261098016178";s:6:"source";s:59:"/bitrix/js/api.feedback/formstyler/jquery.formstyler.min.js";s:3:"min";s:0:"";s:3:"map";s:0:"";}"*/
/* jQuery Form Styler v1.6.2 | (c) Dimox | https://github.com/Dimox/jQueryFormStyler */
(function(c){"function"===typeof define&&define.amd?define(["jquery"],c):"object"===typeof exports?module.exports=c(require("jquery")):c(jQuery)})(function(c){c.fn.styler=function(z){var d=c.extend({wrapper:"form",idSuffix:"-styler",filePlaceholder:"\u0424\u0430\u0439\u043b \u043d\u0435 \u0432\u044b\u0431\u0440\u0430\u043d",fileBrowse:"\u041e\u0431\u0437\u043e\u0440...",selectPlaceholder:"\u0412\u044b\u0431\u0435\u0440\u0438\u0442\u0435...",selectSearch:!1,selectSearchLimit:10,selectSearchNotFound:"\u0421\u043e\u0432\u043f\u0430\u0434\u0435\u043d\u0438\u0439 \u043d\u0435 \u043d\u0430\u0439\u0434\u0435\u043d\u043e",
    selectSearchPlaceholder:"\u041f\u043e\u0438\u0441\u043a...",selectVisibleOptions:0,singleSelectzIndex:"100",selectSmartPositioning:!0,onSelectOpened:function(){},onSelectClosed:function(){},onFormStyled:function(){}},z);return this.each(function(){function B(){var c="",v="",b="",m="";void 0!==a.attr("id")&&""!==a.attr("id")&&(c=' id="'+a.attr("id")+d.idSuffix+'"');void 0!==a.attr("title")&&""!==a.attr("title")&&(v=' title="'+a.attr("title")+'"');void 0!==a.attr("class")&&""!==a.attr("class")&&(b=
    " "+a.attr("class"));var e=a.data(),g;for(g in e)""!==e[g]&&(m+=" data-"+g+'="'+e[g]+'"');this.id=c+m;this.title=v;this.classes=b}var a=c(this),F=navigator.userAgent.match(/(iPad|iPhone|iPod)/i)&&!navigator.userAgent.match(/(Windows\sPhone)/i)?!0:!1,z=navigator.userAgent.match(/Android/i)&&!navigator.userAgent.match(/(Windows\sPhone)/i)?!0:!1;if(a.is(":checkbox"))a.each(function(){if(1>a.parent("div.jq-checkbox").length){var d=function(){var d=new B,b=c("<div"+d.id+' class="jq-checkbox'+d.classes+
'"'+d.title+'><div class="jq-checkbox__div"></div></div>');a.css({position:"absolute",zIndex:"-1",opacity:0,margin:0,padding:0}).after(b).prependTo(b);b.attr("unselectable","on").css({"-webkit-user-select":"none","-moz-user-select":"none","-ms-user-select":"none","-o-user-select":"none","user-select":"none",display:"inline-block",position:"relative",overflow:"hidden"});a.is(":checked")&&b.addClass("checked");a.is(":disabled")&&b.addClass("disabled");b.on("click.styler",function(){b.is(".disabled")||
(a.is(":checked")?(a.prop("checked",!1),b.removeClass("checked")):(a.prop("checked",!0),b.addClass("checked")),a.change());return!1});a.closest("label").add('label[for="'+a.attr("id")+'"]').click(function(a){c(a.target).is("a")||(b.click(),a.preventDefault())});a.on("change.styler",function(){a.is(":checked")?b.addClass("checked"):b.removeClass("checked")}).on("keydown.styler",function(a){32==a.which&&b.click()}).on("focus.styler",function(){b.is(".disabled")||b.addClass("focused")}).on("blur.styler",
    function(){b.removeClass("focused")})};d();a.on("refresh",function(){a.off(".styler").parent().before(a).remove();d()})}});else if(a.is(":radio"))a.each(function(){if(1>a.parent("div.jq-radio").length){var u=function(){var v=new B,b=c("<div"+v.id+' class="jq-radio'+v.classes+'"'+v.title+'><div class="jq-radio__div"></div></div>');a.css({position:"absolute",zIndex:"-1",opacity:0,margin:0,padding:0}).after(b).prependTo(b);b.attr("unselectable","on").css({"-webkit-user-select":"none","-moz-user-select":"none",
    "-ms-user-select":"none","-o-user-select":"none","user-select":"none",display:"inline-block",position:"relative"});a.is(":checked")&&b.addClass("checked");a.is(":disabled")&&b.addClass("disabled");b.on("click.styler",function(){b.is(".disabled")||(b.closest(d.wrapper).find('input[name="'+a.attr("name")+'"]').prop("checked",!1).parent().removeClass("checked"),a.prop("checked",!0).parent().addClass("checked"),a.change());return!1});a.closest("label").add('label[for="'+a.attr("id")+'"]').click(function(a){c(a.target).is("a")||
(b.click(),a.preventDefault())});a.on("change.styler",function(){a.parent().addClass("checked")}).on("focus.styler",function(){b.is(".disabled")||b.addClass("focused")}).on("blur.styler",function(){b.removeClass("focused")})};u();a.on("refresh",function(){a.off(".styler").parent().before(a).remove();u()})}});else if(a.is(":file"))a.css({position:"absolute",top:0,right:0,width:"100%",height:"100%",opacity:0,margin:0,padding:0}).each(function(){if(1>a.parent("div.jq-file").length){var u=function(){var v=
    new B,b=a.data("placeholder");void 0===b&&(b=d.filePlaceholder);var m=a.data("browse");if(void 0===m||""===m)m=d.fileBrowse;var e=c("<div"+v.id+' class="jq-file'+v.classes+'"'+v.title+' style="display: inline-block; position: relative; overflow: hidden"></div>'),g=c('<div class="jq-file__name">'+b+"</div>").appendTo(e);c('<div class="jq-file__browse">'+m+"</div>").appendTo(e);a.after(e);e.append(a);a.is(":disabled")&&e.addClass("disabled");a.on("change.styler",function(){var c=a.val();if(a.is("[multiple]"))for(var c=
    "",G=a[0].files,p=0;p<G.length;p++)c+=(0<p?", ":"")+G[p].name;g.text(c.replace(/.+[\\\/]/,""));""===c?(g.text(b),e.removeClass("changed")):e.addClass("changed")}).on("focus.styler",function(){e.addClass("focused")}).on("blur.styler",function(){e.removeClass("focused")}).on("click.styler",function(){e.removeClass("focused")})};u();a.on("refresh",function(){a.off(".styler").parent().before(a).remove();u()})}});else if(a.is("select"))a.each(function(){if(1>a.parent("div.jqselect").length){var u=function(){function v(a){a.off("mousewheel DOMMouseScroll").on("mousewheel DOMMouseScroll",
    function(a){var b=null;"mousewheel"==a.type?b=-1*a.originalEvent.wheelDelta:"DOMMouseScroll"==a.type&&(b=40*a.originalEvent.detail);b&&(a.stopPropagation(),a.preventDefault(),c(this).scrollTop(b+c(this).scrollTop()))})}function b(){for(var a=0,c=g.length;a<c;a++){var b="",d="",e=b="",t="",q="",y="";g.eq(a).prop("selected")&&(d="selected sel");g.eq(a).is(":disabled")&&(d="disabled");g.eq(a).is(":selected:disabled")&&(d="selected sel disabled");void 0!==g.eq(a).attr("class")&&(t=" "+g.eq(a).attr("class"),
    y=' data-jqfs-class="'+g.eq(a).attr("class")+'"');var x=g.eq(a).data(),f;for(f in x)""!==x[f]&&(e+=" data-"+f+'="'+x[f]+'"');""!==d+t&&(b=' class="'+d+t+'"');b="<li"+y+e+b+">"+g.eq(a).html()+"</li>";g.eq(a).parent().is("optgroup")&&(void 0!==g.eq(a).parent().attr("class")&&(q=" "+g.eq(a).parent().attr("class")),b="<li"+y+' class="'+d+t+" option"+q+'">'+g.eq(a).html()+"</li>",g.eq(a).is(":first-child")&&(b='<li class="optgroup'+q+'">'+g.eq(a).parent().attr("label")+"</li>"+b));u+=b}}function m(){var e=
    new B,p="",n=a.data("placeholder"),h=a.data("search"),m=a.data("search-limit"),t=a.data("search-not-found"),q=a.data("search-placeholder"),y=a.data("z-index"),x=a.data("smart-positioning");void 0===n&&(n=d.selectPlaceholder);if(void 0===h||""===h)h=d.selectSearch;if(void 0===m||""===m)m=d.selectSearchLimit;if(void 0===t||""===t)t=d.selectSearchNotFound;void 0===q&&(q=d.selectSearchPlaceholder);if(void 0===y||""===y)y=d.singleSelectzIndex;if(void 0===x||""===x)x=d.selectSmartPositioning;var f=c("<div"+
e.id+' class="jq-selectbox jqselect'+e.classes+'" style="display: inline-block; position: relative; z-index:'+y+'"><div class="jq-selectbox__select"'+e.title+' style="position: relative"><div class="jq-selectbox__select-text"></div><div class="jq-selectbox__trigger"><div class="jq-selectbox__trigger-arrow"></div></div></div></div>');a.css({margin:0,padding:0}).after(f).prependTo(f);var H=c("div.jq-selectbox__select",f),w=c("div.jq-selectbox__select-text",f),e=g.filter(":selected");b();h&&(p='<div class="jq-selectbox__search"><input type="search" autocomplete="off" placeholder="'+
q+'"></div><div class="jq-selectbox__not-found">'+t+"</div>");var k=c('<div class="jq-selectbox__dropdown" style="position: absolute">'+p+'<ul style="position: relative; list-style: none; overflow: auto; overflow-x: hidden">'+u+"</ul></div>");f.append(k);var r=c("ul",k),l=c("li",k),A=c("input",k),z=c("div.jq-selectbox__not-found",k).hide();l.length<m&&A.parent().hide();""===a.val()?w.text(n).addClass("placeholder"):w.text(e.text());var C=0,I=0;l.each(function(){var a=c(this);a.css({display:"inline-block"});
    a.innerWidth()>C&&(C=a.innerWidth(),I=a.width());a.css({display:""})});p=f.clone().appendTo("body").width("auto");h=p.find("select").outerWidth();p.remove();h==f.width()&&w.width(I);C>f.width()&&k.width(C);w.is(".placeholder")&&w.width()>C&&w.width(w.width());""===g.first().text()&&""!==a.data("placeholder")&&l.first().hide();a.css({position:"absolute",left:0,top:0,width:"100%",height:"100%",opacity:0});var J=f.outerHeight(),D=A.outerHeight(),E=r.css("max-height"),p=l.filter(".selected");1>p.length&&
l.first().addClass("selected sel");void 0===l.data("li-height")&&l.data("li-height",l.outerHeight());var K=k.css("top");"auto"==k.css("left")&&k.css({left:0});"auto"==k.css("top")&&k.css({top:J});k.hide();p.length&&(g.first().text()!=e.text()&&f.addClass("changed"),f.data("jqfs-class",p.data("jqfs-class")),f.addClass(p.data("jqfs-class")));if(a.is(":disabled"))return f.addClass("disabled"),!1;H.click(function(){c("div.jq-selectbox").filter(".opened").length&&d.onSelectClosed.call(c("div.jq-selectbox").filter(".opened"));
    a.focus();if(!F){var b=c(window),q=l.data("li-height"),e=f.offset().top,p=b.height()-J-(e-b.scrollTop()),h=a.data("visible-options");if(void 0===h||""===h)h=d.selectVisibleOptions;var n=5*q,m=q*h;0<h&&6>h&&(n=m);0===h&&(m="auto");var h=function(){k.height("auto").css({bottom:"auto",top:K});var a=function(){r.css("max-height",Math.floor((p-20-D)/q)*q)};a();r.css("max-height",m);"none"!=E&&r.css("max-height",E);p<k.outerHeight()+20&&a()},w=function(){k.height("auto").css({top:"auto",bottom:K});var a=
        function(){r.css("max-height",Math.floor((e-b.scrollTop()-20-D)/q)*q)};a();r.css("max-height",m);"none"!=E&&r.css("max-height",E);e-b.scrollTop()-20<k.outerHeight()+20&&a()};!0===x||1===x?p>n+D+20?(h(),f.removeClass("dropup").addClass("dropdown")):(w(),f.removeClass("dropdown").addClass("dropup")):(!1===x||0===x)&&p>n+D+20&&(h(),f.removeClass("dropup").addClass("dropdown"));f.offset().left+k.outerWidth()>b.width()&&k.css({left:"auto",right:0});c("div.jqselect").css({zIndex:y-1}).removeClass("opened");
        f.css({zIndex:y});k.is(":hidden")?(c("div.jq-selectbox__dropdown:visible").hide(),k.show(),f.addClass("opened focused"),d.onSelectOpened.call(f)):(k.hide(),f.removeClass("opened dropup dropdown"),c("div.jq-selectbox").filter(".opened").length&&d.onSelectClosed.call(f));A.length&&(A.val("").keyup(),z.hide(),A.keyup(function(){var b=c(this).val();l.each(function(){c(this).html().match(new RegExp(".*?"+b+".*?","i"))?c(this).show():c(this).hide()});""===g.first().text()&&""!==a.data("placeholder")&&l.first().hide();
            1>l.filter(":visible").length?z.show():z.hide()}));l.filter(".selected").length&&(""===a.val()?r.scrollTop(0):(0!==r.innerHeight()/q%2&&(q/=2),r.scrollTop(r.scrollTop()+l.filter(".selected").position().top-r.innerHeight()/2+q)));v(r);return!1}});l.hover(function(){c(this).siblings().removeClass("selected")});l.filter(".selected").text();l.filter(".selected").text();l.filter(":not(.disabled):not(.optgroup)").click(function(){a.focus();var b=c(this),q=b.text();if(!b.is(".selected")){var e=b.index(),
    e=e-b.prevAll(".optgroup").length;b.addClass("selected sel").siblings().removeClass("selected sel");g.prop("selected",!1).eq(e).prop("selected",!0);w.text(q);f.data("jqfs-class")&&f.removeClass(f.data("jqfs-class"));f.data("jqfs-class",b.data("jqfs-class"));f.addClass(b.data("jqfs-class"));a.change()}k.hide();f.removeClass("opened dropup dropdown");d.onSelectClosed.call(f)});k.mouseout(function(){c("li.sel",k).addClass("selected")});a.on("change.styler",function(){w.text(g.filter(":selected").text()).removeClass("placeholder");
    l.removeClass("selected sel").not(".optgroup").eq(a[0].selectedIndex).addClass("selected sel");g.first().text()!=l.filter(".selected").text()?f.addClass("changed"):f.removeClass("changed")}).on("focus.styler",function(){f.addClass("focused");c("div.jqselect").not(".focused").removeClass("opened dropup dropdown").find("div.jq-selectbox__dropdown").hide()}).on("blur.styler",function(){f.removeClass("focused")}).on("keydown.styler keyup.styler",function(c){var b=l.data("li-height");""===a.val()?w.text(n).addClass("placeholder"):
    w.text(g.filter(":selected").text());l.removeClass("selected sel").not(".optgroup").eq(a[0].selectedIndex).addClass("selected sel");if(38==c.which||37==c.which||33==c.which||36==c.which)""===a.val()?r.scrollTop(0):r.scrollTop(r.scrollTop()+l.filter(".selected").position().top);40!=c.which&&39!=c.which&&34!=c.which&&35!=c.which||r.scrollTop(r.scrollTop()+l.filter(".selected").position().top-r.innerHeight()+b);13==c.which&&(c.preventDefault(),k.hide(),f.removeClass("opened dropup dropdown"),d.onSelectClosed.call(f))}).on("keydown.styler",
    function(a){32==a.which&&(a.preventDefault(),H.click())});c(document).on("click",function(a){c(a.target).parents().hasClass("jq-selectbox")||"OPTION"==a.target.nodeName||(c("div.jq-selectbox").filter(".opened").length&&d.onSelectClosed.call(c("div.jq-selectbox").filter(".opened")),A.length&&A.val("").keyup(),k.hide().find("li.sel").addClass("selected"),f.removeClass("focused opened dropup dropdown"))})}function e(){var e=new B,d=c("<div"+e.id+' class="jq-select-multiple jqselect'+e.classes+'"'+e.title+
' style="display: inline-block; position: relative"></div>');a.css({margin:0,padding:0}).after(d);b();d.append("<ul>"+u+"</ul>");var n=c("ul",d).css({position:"relative","overflow-x":"hidden","-webkit-overflow-scrolling":"touch"}),h=c("li",d).attr("unselectable","on"),e=a.attr("size"),m=n.outerHeight(),t=h.outerHeight();void 0!==e&&0<e?n.css({height:t*e}):n.css({height:4*t});m>d.height()&&(n.css("overflowY","scroll"),v(n),h.filter(".selected").length&&n.scrollTop(n.scrollTop()+h.filter(".selected").position().top));
    a.prependTo(d).css({position:"absolute",left:0,top:0,width:"100%",height:"100%",opacity:0});if(a.is(":disabled"))d.addClass("disabled"),g.each(function(){c(this).is(":selected")&&h.eq(c(this).index()).addClass("selected")});else if(h.filter(":not(.disabled):not(.optgroup)").click(function(b){a.focus();var d=c(this);b.ctrlKey||b.metaKey||d.addClass("selected");b.shiftKey||d.addClass("first");b.ctrlKey||b.metaKey||b.shiftKey||d.siblings().removeClass("selected first");if(b.ctrlKey||b.metaKey)d.is(".selected")?
            d.removeClass("selected first"):d.addClass("selected first"),d.siblings().removeClass("first");if(b.shiftKey){var e=!1,f=!1;d.siblings().removeClass("selected").siblings(".first").addClass("selected");d.prevAll().each(function(){c(this).is(".first")&&(e=!0)});d.nextAll().each(function(){c(this).is(".first")&&(f=!0)});e&&d.prevAll().each(function(){if(c(this).is(".selected"))return!1;c(this).not(".disabled, .optgroup").addClass("selected")});f&&d.nextAll().each(function(){if(c(this).is(".selected"))return!1;
            c(this).not(".disabled, .optgroup").addClass("selected")});1==h.filter(".selected").length&&d.addClass("first")}g.prop("selected",!1);h.filter(".selected").each(function(){var a=c(this),b=a.index();a.is(".option")&&(b-=a.prevAll(".optgroup").length);g.eq(b).prop("selected",!0)});a.change()}),g.each(function(a){c(this).data("optionIndex",a)}),a.on("change.styler",function(){h.removeClass("selected");var a=[];g.filter(":selected").each(function(){a.push(c(this).data("optionIndex"))});h.not(".optgroup").filter(function(b){return-1<
            c.inArray(b,a)}).addClass("selected")}).on("focus.styler",function(){d.addClass("focused")}).on("blur.styler",function(){d.removeClass("focused")}),m>d.height())a.on("keydown.styler",function(a){38!=a.which&&37!=a.which&&33!=a.which||n.scrollTop(n.scrollTop()+h.filter(".selected").position().top-t);40!=a.which&&39!=a.which&&34!=a.which||n.scrollTop(n.scrollTop()+h.filter(".selected:last").position().top-n.innerHeight()+2*t)})}var g=c("option",a),u="";a.is("[multiple]")?z||F||e():m()};u();a.on("refresh",
    function(){a.off(".styler").parent().before(a).remove();u()})}});else if(a.is(":reset"))a.on("click",function(){setTimeout(function(){a.closest(d.wrapper).find("input, select").trigger("refresh")},1)})}).promise().done(function(){d.onFormStyled.call()})}});
/* End */
;