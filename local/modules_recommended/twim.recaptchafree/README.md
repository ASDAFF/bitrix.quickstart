# reCaptchaFree
![alt-текст](img_md/logo.jpg "1")

**Описание решения**  
Капча (каптча, CAPTCHA) — система защиты от ботов, которая отделяет хороших пользователей от спамеров. Корпорация Google представила новую систему защиты от спамеров и ботов, которая отличается от того, что мы видели до сих пор - reCaptcha 2.0. Система анализирует, человек поставил отметку или робот, если проверка не пройдена, то предлагается визуальный отбор картинок, прослушивание аудио или классическую капчу  
https://marketplace.1c-bitrix.ru/solutions/twim.recaptchafree/

**Установка**

Требования к серверу: запросы идут  через "file_get_contents", параметры необходимые при этом в php.ini
```
extension=php_openssl.dll
allow_url_include = On
```
Данный модуль заменяет стандартную капчу Битрикс, для инициализацию необходимо или использовать стандартный вызов капчи или инициализировать вручную. Проверка так же осуществляется через стандартную функцию Битрикс
```php
<input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>">
<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="140" height="30" alt="CAPTCHA">
<input type="text" name="captcha_word" size="30" maxlength="50" value="" >
```
Для работы капчи, необходимо установить модуль и перейти в настройки модуля, в настройках, для каждого сайта, указать:

1) ключ;
2) секретный ключ;
3) тема;
4) активность.

Для получения ключей необходимо перейти www.google.com/recaptcha, зарегистрироваться, и добавить Ваш сайт(домен).

для обновления рекапчи после ajax или после открытия popup-окон, необходимо вызвать функцию
```
Recaptchafree.reset();

```
так же доступны все виджеты на страницы,  массив id виджетов
Recaptchafree.items
Для обновления рекапчи в Композитном режиме, необходимо вызвать обновление, пример
```html
<script type="text/javascript">
    if (window.frameCacheVars !== undefined)
    {
        BX.addCustomEvent("onFrameDataReceived" , function(json) {
            Recaptchafree.reset();
        });
    }
</script>
```
Маска исключения, будет отключена замена на установленных страницах, так же для данных страниц возможна ручная установка рекапчи, для этого нужно скрыть стандартные поля капчи Битрикс и поле name="captcha_word" указать value="*****" - произвольный набор из 5 символов и установить <div class="g-recaptcha" data-sitekey="****"></div>, где **** - Ваш ключ.

Доступна проверка стандартной капчи, через api, если в запросе нет вызова рекапчи.
```
$APPLICATION->CaptchaCheckCode($captcha_word, $captcha_sid)
```
Для браузеров Opera Mobile, Android Native Broweser ниже 4.0 будет показана стандартная капча


Требования к серверу с версии 0.1.0: установленная библиотека "curl"(с версии 0.1.0 beta), или  запросы будут отправляться через "file_get_contents", параметры необходимые при этом в php.ini

```
extension=php_openssl.dll 
allow_url_include = On
```

Invisible reCaptcha и настройка:

С версии 0.1.0 beta доступна Invisible reCaptcha. Для ее работы  необходимо в настройках указать параметр "Размер"="Невидимый". При этом  можно указать положение логотипа reCaptcha на странице. Внимание, для работы в режиме "Invisible", необходимо обновить ключи.

При режиме Invisible reCaptcha, на событие формы "submit" будет вызывать  проверку и возвращать callback функцию, которая будет отправлять  веб-форму методом submit. Поэтому, если у Вас есть проверки на submit() или отправка форм ajax,  необходимо переопределить функцию "RecaptchafreeSubmitForm". Обратите внимание, что функция будет вызываться для всех форм, где стоит рекапча, а в функцию передается только ссылка на форму текущую.

ссылка на веб-форму, которая прошла проверку ReCaptcha
```
Recaptchafree.form_submit
```
шаблон костомизации:
```javascript
var _RecaptchafreeSubmitForm = RecaptchafreeSubmitForm;
RecaptchafreeSubmitForm = function(token){
    if(Recaptchafree.form_submit !== undefined){ 
       // если ссылка есть на форму
       //token - ключ ответа, который необходимо передать для текущей формы, имя переменной g-recaptcha-response
```       
далее действия: отправка ajax или/и вызов события
```javascript
           $(Recaptchafree.form_submit).trigger("submit_ajax"); // пример вызова костомного события для проверки и отправки веб-формы ajax
        }
};
```
Пример:
```javascript
var _RecaptchafreeSubmitForm = RecaptchafreeSubmitForm;
RecaptchafreeSubmitForm = function(token){
    if(Recaptchafree.form_submit !== undefined){ // если ссылка существует на форму
        var x = document.createElement("INPUT"); //  создаем поле hidden 
        x.setAttribute("type", "hidden");  
        x.name = "g-recaptcha-response"; // имя поля g-recaptcha-response
        x.value = token; // значение token
        Recaptchafree.form_submit.appendChild(x);  // добавляем в текущую форму
        var elements = Recaptchafree.form_submit.elements; // список элементов формы
        for (var i = 0; i < elements.length; i++) { // получаем submit и берем ее имя и значение, чтобы передать, 
           //к примеру веб-формы Битрикс требуют обязательно при передачи ajax значения submit 
            if(elements[i].getAttribute("type") === "submit")  {
                var submit_hidden = document.createElement("INPUT"); // create submit input hidden
                submit_hidden.setAttribute("type", "hidden");  
                submit_hidden.name = elements[i].name;
                submit_hidden.value = elements[i].value;
                Recaptchafree.form_submit.appendChild(submit_hidden);  // append current form
            }
        }
        $(Recaptchafree.form_submit).trigger("submit_ajax"); // вызов события для веб-формы
    } 

};

// по событию отправка формы

$(document).on("submit_ajax", "#feedback", function(){
    $.ajax({
        type: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize() + "&ajax=y",
        cache: false,
        dataType: "html",
        success: function(data){
           //data - ответ
        },
        error: function(msg){
          alert( "Ошибка отправки, попробуйте позже" );   
        },
        complete:function(){
           Recaptchafree.reset(); // сброс рекапчи, для повторного ввода
        } 
    });
    
    return false;
});
```