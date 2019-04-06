/**
 * Created by Tuning-Soft on 16.02.2014
 */
$(function(){

    // значение по умолчанию
    var defaults = { color:'#FB4D4D' };

    // актуальные настройки, глобальные
    var options;

    $.fn.validateMainFeedback = function(params){
        // при многократном вызове функции настройки будут сохранятся, и замещаться при необходимости
        options = $.extend({}, defaults, options, params);
        //console.log(this); // jQuery
        //console.log(this.length); // число элементов

        var formObj = this;
        var error = false;
        var ts_field = '[class*="ts-field-"]';
        var ts_field_error = '<span class="ts-field-error"></span>';
        var ts_field_saccess = '<span class="ts-field-saccess"></span>';
        var input_required = 'input.required';

        //Проверка отправки формы
        $(formObj).find(':submit').click(function(e){

            //input[type="text"]
            $(formObj).find(input_required).each(function(){

                if( $(this).val() == '')
                {
                    $(this).next(ts_field).remove();
                    $(this).after(ts_field_error);
                    error = true;
                }
                else
                {
                    $(this).next(ts_field).remove();
                    $(this).after(ts_field_saccess);
                    error = false;
                }


                if(error)
                    e.preventDefault();
            });
            //Проверка при изменении полей
            $(formObj).find('input.required').on('keyup change', function(e){
                if($(this).val() != '')
                {
                    $(this).next(ts_field).remove();
                    $(this).after(ts_field_saccess);
                    error = false;
                }
                else
                {
                    $(this).next(ts_field).remove();
                    $(this).after(ts_field_error);
                    error = true;
                }

                if(error)
                    e.preventDefault();
            });
            //\\input[type="text"]

            //textarea
            $(formObj).find('textarea.required').each(function(){
                if($(this).val() == '')
                {
                    //css('border-color', options.color)
                    $(this).parent().find(ts_field).remove();
                    $(this).after(ts_field_error);
                    error = true;
                }
                else
                {
                    $(this).parent().find(ts_field).remove();
                    $(this).after(ts_field_saccess);
                    error = false;
                }

                if(error)
                    e.preventDefault();

            });
            //Проверка при изменении полей
            $(formObj).find('textarea.required').on('keyup click change',function(e){
                if($(this).val() != '')
                {
                    $(this).parent().find(ts_field).remove();
                    $(this).after(ts_field_saccess);
                    error = false;
                }
                else
                {
                    $(this).parent().find(ts_field).remove();
                    $(this).after(ts_field_error);
                    error = true;
                }

                if(error)
                    e.preventDefault();
            });
            //\\textarea

            //select
            $(formObj).find('select.required').each(function(){
                if($(this).find('option:selected').length == 0)
                {
                    //css('border-color', options.color)
                    $(this).parent().find(ts_field).remove();
                    $(this).after(ts_field_error);
                    error = true;
                }
                else
                {
                    $(this).parent().find(ts_field).remove();
                    $(this).after(ts_field_saccess);
                    error = false;
                }

                if(error)
                    e.preventDefault();

            });
            //Проверка при изменении полей
            $(formObj).find('select.required').change(function(e){
                if($(this).find('option:selected').length)
                {
                    $(this).parent().find(ts_field).remove();
                    $(this).after(ts_field_saccess);
                    error = false;
                }
                else
                {
                    $(this).parent().find(ts_field).remove();
                    $(this).after(ts_field_error);
                    error = true;
                }

                if(error)
                    e.preventDefault();
            });
            //\\select


            ////////////////////////////////////////////////////////////
            //                          v1.2.9                       //
            ///////////////////////////////////////////////////////////

            //input[type="checkbox"]
            $(formObj).find('.option-qroup.required').each(function(){

                if( !$(this).find('input:checked').length)
                {
                    $(this).parent().find(ts_field).remove();
                    $(this).after(ts_field_error);
                    error = true;
                }
                else
                {
                    $(this).parent().find(ts_field).remove();
                    $(this).after(ts_field_saccess);
                    error = false;
                }


                if(error)
                    e.preventDefault();
            });
            //Проверка при изменении полей
            $(formObj).find('.option-qroup.required').on('keyup change', function(e){
                if($(this).find('input:checked').length)
                {
                    $(this).parent().find(ts_field).remove();
                    $(this).after(ts_field_saccess);
                    error = false;
                }
                else
                {
                    $(this).parent().find(ts_field).remove();
                    $(this).after(ts_field_error);
                    error = true;
                }

                if(error)
                    e.preventDefault();
            });
            //\\input[type="checkbox"]


        });

        //$(this).click(function(){
          //  $(this).css('color', options.color);
        //});

        return this;
    };

});