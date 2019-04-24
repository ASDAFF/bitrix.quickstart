var formAjax = {
    fn: function (ans, curForm, waitElement, arParams) {
        BX.closeWait(waitElement);
        curForm.find('input:not([type="submit"]):not([type="button"]), textarea, select').css({'border': '1px solid #CCC'});
        curForm.find('.it-error').empty();

        var errors = ans.responseJSON && ans.responseJSON.errors ? ans.responseJSON.errors : false;

        if (errors)
        {
            for (var inputName in errors)
            {
                curForm.find('[name="' + inputName + '"]').css({border: '1px solid red'})
                    .closest('.it-block').find('.it-error').html(errors[inputName]);
            }
        }
        else
        {
            curForm.find('input:not([type="submit"]):not([type="button"]), textarea').val('');
            curForm.find('input[type="checkbox"]').prop('checked', false);
            this.executeFunctionByName(arParams.FORM_SUCCESS_FUNCTION, window, arParams.FORM_SUCCESS_FUNCTION_CORRECT_JSON != 'Y')
        }
    },
    init: function (arParams) {
        var _this = this;

        $('.' + arParams['FORM_CLASS'].replace(/\s+/g, '.')).off('submit').on('submit', function (e) {

            e.preventDefault();

            var curForm = $(this),
                formData = new FormData(curForm[0]),
                waitElement = curForm.find('[type="submit"]').get(0),
                fz152El = $(curForm.find('.checkbox-152-fz'));

            if(fz152El && !fz152El.is(':checked'))
            {
                var i = 0,
                    interval = setInterval(function() {
                        fz152El.closest('.it-block').css({'border': (i % 2 === 0 ? '1px solid transparent' : '1px solid red')})
                        if(++i === 7)
                        {
                            clearInterval(interval);
                            return ;
                        }
                    }, 500);
                return false;
            }

            BX.showWait(waitElement);

            formData.append('arParams', JSON.stringify(arParams));

            $.ajax({
                method: curForm.attr('method'),
                url: curForm.attr('action'),
                dataType: 'json',
                data: formData,
                async: false,
                cache: false,
                processData: false,
                contentType: false,
                complete: function(ans) {
                    _this.fn(ans, curForm, waitElement, arParams);
                }
            });
            return false;
        })
    },
    executeFunctionByName: function(functionName, context, needFixJson /*, args */) {

        functionName = BX.util.htmlspecialcharsback(functionName);

        var args = functionName.substring(functionName.indexOf('(') + 1);
        args = args.substr(0, args.length - 1);

        if(needFixJson)
        {
            args = args.replace(/([{,\s^]+)(\b[a-z0-9]*?\b)(?=:)/gi, function (v, v1, v2) {
                return v1 + '"' + v2 + '"';
            });
        }

        try {
            args = [JSON.parse(args)];
        } catch(e) {
            args = [];
        }

        functionName = functionName.substring(0, functionName.indexOf('('));

        var namespaces = functionName.split('.');
        var func = namespaces.pop();
        for(var i = 0; i < namespaces.length; ++i)
            context = context[namespaces[i]];

        return context[func].apply(context, args);
    }
};