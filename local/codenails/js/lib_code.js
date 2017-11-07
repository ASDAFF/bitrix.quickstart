/**
 * Created by ASDAFF on 08.11.2017.
 */

// Функция проверки полей формы
function checkInput(){
    $('form').find('.rfield').each(function(){
        if($(this).val() != ''){
            // Если поле не пустое удаляем класс-указание
            $(this).removeClass('empty_field');
        } else {
            // Если поле пустое добавляем класс-указание
            $(this).addClass('empty_field');
        }
    });
}

// Функция подсветки незаполненных полей
function lightEmpty(){
    $('form').find('.empty_field').css({'border-color':'#d8512d'});
    // Через полсекунды удаляем подсветку
    setTimeout(function(){
        $('form').find('.empty_field').removeAttr('style');
    },500);
}

// Проверка E-Mail по маске
function validEmail(mail) {
    var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;

    if (pattern.test(mail)) {
        return true;
    } else {
        return false;
    }
}

//Проверка значения поля на наличие одного или более символов
function isNotEmpty(elem) {
    var str = elem.value;
    var re = /.+/;
    if (!str.match(re)) {
        elem.style.border = '1px solid #fe5a2b';
        alert ("Заполните поле");
        return false;
    } else {
        elem.style.border = '1px solid #2cb771';
        return true;
    }
}

// Проверка в режиме реального времени
setInterval(function(){
    // Запускаем функцию проверки полей на заполненность
    checkInput();
    // Считаем к-во незаполненных полей
    var sizeEmpty = $('form').find('.empty_field').size();
    // Вешаем условие-тригер на кнопку отправки формы
    if(sizeEmpty > 0){
        if($('form').find('.btn_submit').hasClass('disabled')){
            return false
        } else {
            $('form').find('.btn_submit').addClass('disabled')
        }
    } else {
        $('form').find('.btn_submit').removeClass('disabled')
    }
},500);

// Событие клика по кнопке отправить
$('form').find('.btn_submit').click(function(){
    if($(this).hasClass('disabled')){
        // подсвечиваем незаполненные поля и форму не отправляем, если есть незаполненные поля
        lightEmpty();
        return false
    } else {
        // Все хорошо, все заполнено, отправляем форму
        $('form').submit();
    }
});

/* с плагином jquery.validate.js
 * http://jqueryvalidation.org/
 * "Плавное исчезновение подсказки" */
$('.contact-form input[type="tel"]').bind('focus', function () {
    if ($(this).hasClass('error')) {
        $(this).removeClass('error');
        $(this).parents(".text-frame").find(".error").fadeOut('slow');
    }
});

