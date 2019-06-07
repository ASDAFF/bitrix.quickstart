<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$MESS['AFD_INC_MODULE_ERROR']        = 'Модуль "TS Умный конструктор форм на инфоблоках" не установлен';
$MESS['AFD_INC_IBLOCK_MODULE_ERROR'] = 'Модуль "Инфоблоки" не установлен';

//BASE
$MESS["IBLOCK_TYPE"]    = "Тип инфоблока";
$MESS["IBLOCK_ID"]      = "Инфоблок";
$MESS['UNIQUE_FORM_ID'] = 'ID формы';
$MESS['REDIRECT_URL']   = 'Редирект на страницу';
$MESS['CHOOSE']         = '(не выбрано)';

//VISUAL
$MESS['INCLUDE_LHE']               = 'Упрощенный HTML-редактор для текстовых полей';
$MESS['HIDE_FIELDS']               = 'Скрывать поля формы в input[type=hidden]';
$MESS['DIVIDER_FIELDS']            = 'Поля формы строкой/разделителем';
$MESS['GROUPS_ID']                 = 'Группы пользователей для поля "Привязка к пользователю"';
$MESS['FORM_AUTOCOMPLETE']         = 'Автокомплит формы';
$MESS['FORM_HORIZONTAL']           = 'Горизонтальная форма';
$MESS['SHOW_TITLE']                = 'Выводить заголовок формы';
$MESS['FORM_WIDTH']                = 'Ширина формы, px/%';
$MESS['FORM_WIDTH_DEFAULT']        = '60%';
$MESS['FORM_TITLE']                = 'Заголовок формы';
$MESS['FORM_TITLE_TEXT']           = 'Напишите нам';
$MESS['MESS_SUCCESS']              = 'Сообщение об успешной отправке';
$MESS['MESS_SUCCESS_TEXT']         = 'Заявка №#TICKET_ID# отправлена!';
$MESS['MESS_SUCCESS_DESC']         = 'Текст под сообщением';
$MESS['MESS_SUCCESS_DESC_DEFAULT'] = 'Мы рассмотрим сообщение и обязательно свяжемся с вами';
$MESS['SHOW_ERRORS']               = 'Выводить подсказки ошибок';

$MESS['SUBMIT_BUTTON_TEXT']          = 'Текст кнопки "Отправить"';
$MESS['SUBMIT_BUTTON_TEXT_VALUE']    = 'Отправить заявку';
$MESS['SUBMIT_BUTTON_AJAX']          = 'Текст кнопки "Отправляется..."';
$MESS['SUBMIT_BUTTON_AJAX_DEFAULT']  = 'Отправляется заявка...';
$MESS['SUBMIT_BUTTON_CLASS']         = 'CSS-классы кнопки "Отправить"';
$MESS['SUBMIT_BUTTON_CLASS_DEFAULT'] = 'afd-button';
$MESS['MESS_CHOOSE']                 = 'Текст для (выбрать)';
$MESS['MESS_CHOOSE_DEFAULT']         = '(выбрать)';
$MESS['MESS_REQUIRED_FIELD']         = 'Сообщение об ошибке под полем';
$MESS['MESS_REQUIRED_FIELD_DEFAULT'] = '#FIELD# обязательно';
$MESS['MESS_CHECK_EMAIL']            = 'Cообщение о некорректном email';
$MESS['MESS_CHECK_EMAIL_DEFAULT']    = 'Указанный email некорректен';

//THEME_SETTINGS
$MESS['THEME_SETTINGS'] = 'Тема';

//JQUERY_SETTINGS
$MESS['JQUERY_SETTINGS']       = 'jQuery';
$MESS['JQUERY_ON']             = 'Включить';
$MESS['JQUERY_VERSION']        = 'Версия';
$MESS['JQUERY_VERSION_VALUES'] = array(
	 'jquery'  => 'v1.8.3 (встроенная)',
	 'jquery2' => 'v2.1.3 (встроенная)',
);

//CRM_LEAD_SETTINGS
$MESS['GROUP_CRM']       = 'CRM';
$MESS['CRM_ON']          = 'Включить';
$MESS['CRM_ID']          = 'CRM';
$MESS['CRM_FIELD']       = 'Поле в CRM';
$MESS['CRM_LEAD_TITLE']  = 'Шаблон названия лида';
$MESS['CRM_SHOW_ERRORS'] = 'Выводить ошибки';

//IBLOCK_SETTINGS
$MESS['IBLOCK_SETTINGS']             = 'Запись в инфоблок';
$MESS['IBLOCK_ON']                   = 'Включить';
$MESS['IBLOCK_TICKET_CODE']          = 'Свойство для нумерации тикетов';
$MESS['IBLOCK_ELEMENT_ACTIVE']       = 'Элемент активен после добавления';
$MESS['IBLOCK_ELEMENT_NAME']         = 'Шаблон названия элемента';
$MESS['IBLOCK_ELEMENT_NAME_DEFAULT'] = 'Ticket##TICKET_ID#';
//$MESS['IBLOCK_ELEMENT_NAME_DEFAULT'] = '#SERVER_NAME#: [TID##TICKET_ID#] Обращение из формы "#FORM_TITLE#"';
$MESS['IBLOCK_ELEMENT_CODE']         = 'Шаблон кода элемента';
$MESS['IBLOCK_ELEMENT_CODE_DEFAULT'] = 'Ticket##TICKET_ID#';

//POST_SETTINGS
$MESS['POST_SETTINGS']                 = 'Отправка писем';
$MESS['POST_ON']                       = 'Включить';
$MESS['POST_REPLACE_FROM']             = 'Заменять в письме "От кого" на "E-mail" пользователя';
$MESS['POST_EMAIL_FROM']               = 'E-Mail адрес отправителя';
$MESS['POST_EMAIL_TO']                 = 'E-Mail адрес получателя';
$MESS['ENABLED_FIELDS']                = 'Выводить только эти поля';
$MESS['POST_EMAIL_CODE']               = 'Поле «E-Mail адрес посетителя»';
$MESS['COMPATIBLE_ON']                 = 'Включить совместимость скопированных шаблонов';
$MESS['POST_ADMIN_MESSAGE_ID']         = 'Почтовый шаблон администратора';
$MESS['POST_ADMIN_SUBJECT']            = 'Тема сообщения для администратора';
$MESS['POST_ADMIN_SUBJECT_DEFAULT']    = '#SITE_NAME#: Сообщение из формы обратной связи'; //[Ticket##TICKET_ID#] #FORM_TITLE#
$MESS['POST_USER_MESSAGE_ID']          = 'Почтовый шаблон посетителя';
$MESS['POST_USER_SUBJECT']             = 'Тема сообщения для посетителя';
$MESS['POST_USER_SUBJECT_DEFAULT']     = '#SITE_NAME#: Копия сообщения из формы обратной связи';
$MESS['POST_MESS_STYLE_WRAP']          = 'CSS-стили для <div> поля в письме';
$MESS['POST_MESS_STYLE_WRAP_DEFAULT']  = 'padding:10px;border-bottom:1px dashed #dadada;';
$MESS['POST_MESS_STYLE_NAME']          = 'CSS-стили для <div> названия поля в письме';
$MESS['POST_MESS_STYLE_NAME_DEFAULT']  = 'font-weight:bold;';
$MESS['POST_MESS_STYLE_VALUE']         = 'CSS-стили для <div> значения поля в письме';
$MESS['POST_MESS_STYLE_VALUE_DEFAULT'] = '';

//MODAL_SETTINGS
$MESS['MODAL_SETTINGS']               = 'Модальное окно';
$MESS['USE_MODAL']                    = 'Выводить форму в модальном окне';
$MESS['MODAL_ID']                     = 'ID модального окна';
$MESS['MODAL_BTN_TEXT']               = 'Текст кнопки вызова окна';
$MESS['MODAL_BTN_TEXT_DEFAULT']       = 'Обратная связь';
$MESS['MODAL_BTN_CLASS']              = 'Класс кнопки вызова окна';
$MESS['MODAL_BTN_CLASS_DEFAULT']      = 'api_button';
$MESS['MODAL_BTN_ID']                 = 'ID кнопки вызова окна';
$MESS['MODAL_BTN_ID_DEFAULT']         = '';
$MESS['MODAL_BTN_SPAN_CLASS']         = 'Класс иконки в кнопке вызова окна';
$MESS['MODAL_BTN_SPAN_CLASS_DEFAULT'] = 'api_icon';
$MESS['MODAL_HEADER_TEXT']            = 'Текст в заголовке окна';
$MESS['MODAL_HEADER_TEXT_DEFAULT']    = 'Обратная связь';
$MESS['MODAL_FOOTER_TEXT']            = 'Текст в подвале окна';

//YM_GOALS_SETTINGS
$MESS['YAMETRIKA_SETTINGS']    = 'Yandex.Metrika';
$MESS['YAMETRIKA_ON']          = 'Включить';
$MESS['YAMETRIKA_COUNTER_ID']  = '№ счётчика';
$MESS['YAMETRIKA_TARGET_NAME'] = 'Идентификатор цели успешной отправки формы';

//YM2_SETTINGS
$MESS['YM2_SETTINGS']                 = 'Yandex.Metrika2';
$MESS['YM2_ON']                       = 'Включить';
$MESS['YM2_COUNTER']                  = '№ счётчика';
$MESS['YM2_GOAL_SUBMIT_FORM_SUCCESS'] = 'Идентификатор события успешной отправки формы';

//GA_SETTINGS
$MESS['GA_SETTINGS'] = 'Google Analytics';
$MESS['GA_ON']       = 'Включить';
$MESS['GA_GTAG']     = 'JS-код события gtag.js';
/*$MESS['GA_CATEGORY'] = 'Категория (eventCategory)';
$MESS['GA_ACTION']   = 'Действие (eventAction)';
$MESS['GA_LABEL']    = 'Ярлык (eventLabel)';
$MESS['GA_VALUE']    = 'Ценность (eventValue)';*/

//POWERTIP_SETTINGS
$MESS['POWERTIP_SETTINGS']           = 'Подсказки полей';
$MESS['USE_POWERTIP']                = 'Включить';
$MESS['POWERTIP_FIELD']              = 'Подсказка';
$MESS['POWERTIP_COLOR']              = 'Цвет';
$MESS['POWERTIP_COLOR_VALUES']       = array(
	 'black'  => 'Черный',
	 'blue'   => 'Синий',
	 'green'  => 'Зеленый',
	 'light'  => 'Серый',
	 'orange' => 'Оранжевый',
	 'purple' => 'Фиолетовый',
	 'red'    => 'Красный',
	 'yellow' => 'Желтый',
);
$MESS['POWERTIP_PLACEMENT']          = 'placement';
$MESS['POWERTIP_FOLLOWMOUSE']        = 'followMouse';
$MESS['POWERTIP_POPUPID']            = 'popupId';
$MESS['POWERTIP_OFFSET']             = 'offset';
$MESS['POWERTIP_FADEINTIME']         = 'fadeInTime';
$MESS['POWERTIP_FADEOUTTIME']        = 'fadeOutTime';
$MESS['POWERTIP_CLOSEDELAY']         = 'closeDelay';
$MESS['POWERTIP_INTENTPOLLINTERVAL'] = 'intentPollInterval';

//INPUTMASK_SETTINGS
$MESS['INPUTMASK_SETTINGS'] = 'Маски';
$MESS['INPUTMASK_ON']       = 'Включить';
$MESS['INPUTMASK_JS']       = 'Подключить встроенные JS';
$MESS['INPUTMASK_JS_TIP']   = 'Подключатся скрипты плагина и инициализация. Если отключить, то data-атрибут будет выводиться в полях, а скрипты и инициализация отключатся, тогда сможете свои плагины подключать к маске поля';
$MESS['INPUTMASK_FIELD']    = 'Маска';

//VALIDATE_SETTINGS
$MESS['VALIDATE_SETTINGS'] = 'Валидация полей';
$MESS['VALIDATE_ON']       = 'Включить';
$MESS['VALIDATE_RULE']     = 'Валидатор (правило)';
$MESS['VALIDATE_MESS']     = 'Валидатор (сообщение)';

//FIELD_X_SETTINGS
$MESS['FIELD_X_SETTINGS'] = 'Настройка поля - ';

//FILE_SETTINGS
$MESS['FILES_SETTINGS']       = 'Загрузка файлов';
$MESS['UPLOAD_FOLDER']        = 'Временная директория, используемая для хранения файлов во время закачивания';
$MESS['UPLOAD_FOLDER_TIP']    = 'После отправки формы файлы из временной директории удаляются';
$MESS['UPLOAD_FILE_SIZE']     = 'Максимальный размер загружаемого файла - ' . ini_get('upload_max_filesize');
$MESS['UPLOAD_FILE_SIZE_TIP'] = '(K - килобайт, М - мегабайт, G - гигабайт, T - терабайт)';
$MESS['UPLOAD_FILE_LIMIT']    = 'Максимальное  количество одновременно закачиваемых файлов - ' . ini_get('max_file_uploads');

//EULA
$MESS['GROUP_EULA']                = 'Пользовательское соглашение';
$MESS['USE_EULA']                  = 'Выводить условия Пользовательского соглашения';
$MESS['MESS_EULA']                 = 'Пользовательское соглашение';
$MESS['MESS_EULA_DEFAULT']         = 'Нажимая кнопку «Отправить», я принимаю условия Пользовательского соглашения и даю своё согласие на обработку моих персональных данных, в соответствии с Федеральным законом от 27.07.2006 года №152-ФЗ «О персональных данных», на условиях и для целей, определенных Политикой конфиденциальности.';
$MESS['MESS_EULA_CONFIRM']         = 'Сообщение о необходимости принять Пользовательское соглашения';
$MESS['MESS_EULA_CONFIRM_DEFAULT'] = 'Для продолжения вы должны принять условия Пользовательского соглашения';

//PRIVACY
$MESS['GROUP_PRIVACY']                = 'Персональные данные';
$MESS['USE_PRIVACY']                  = 'Выводить соглашение на обработку персональных данных';
$MESS['MESS_PRIVACY']                 = 'Пользовательское соглашение';
$MESS['MESS_PRIVACY_DEFAULT']         = 'Я согласен на обработку персональных данных';
$MESS['MESS_PRIVACY_LINK']            = 'Ссылка на соглашение';
$MESS['MESS_PRIVACY_CONFIRM']         = 'Сообщение о необходимости принять соглашение';
$MESS['MESS_PRIVACY_CONFIRM_DEFAULT'] = 'Для продолжения вы должны принять соглашение на обработку персональных данных';

//USER_CONSENT
$MESS['GROUP_USER_CONSENT']      = 'Согласие пользователя';
$MESS['USER_CONSENT_USE']        = 'Запрашивать согласие';
$MESS['USER_CONSENT_ID']         = 'Соглашение';
$MESS['USER_CONSENT_ID_DEF']     = '(не выбрано)';
$MESS['USER_CONSENT_IS_CHECKED'] = 'Галка по умолчанию проставлена';
$MESS['USER_CONSENT_REPLACE']    = 'Названия полей, которые попадут в текст соглашения';

//COMP_VARS
$MESS['GROUP_COMP_VARS'] = 'Встроенные переменные';
$MESS['PAGE_VARS']       = 'Переменные страницы';
$MESS['SERVER_VARS']     = 'Переменные сервера';
$MESS['UTM_VARS']        = 'Переменные utm-метки';

$MESS['PAGE_VARS_VALUES'] = array(
	 'FORM_TITLE' => 'Заголовок формы',
	 'PAGE_TITLE' => 'Заголовок страницы',
	 'PAGE_URL'   => 'URL-адрес страницы',
	 'DIR_URL'    => 'URL-адрес раздела',
	 'DATE_TIME'  => 'Дата и время',
	 'DATE'       => 'Дата',
	 'IP'         => 'IP-адрес посетителя',
);

//USER_VARS
$MESS['GROUP_USER_VARS']          = 'Пользовательские переменные';
$MESS['API_FD_PARAMS_PAGE_TITLE'] = 'Заголовок страницы';
$MESS['API_FD_PARAMS_PAGE_URL']   = 'URL-адрес страницы';
$MESS['API_FD_PARAMS_DIR_URL']    = 'URL-адрес раздела';
$MESS['API_FD_PARAMS_DATE_TIME']  = 'Дата и время';
$MESS['API_FD_PARAMS_DATE']       = 'Дата';
$MESS['API_FD_PARAMS_IP']         = 'IP-адрес посетителя';

//WYSIWYG
$MESS['GROUP_WYSIWYG'] = 'Визуальный редактор';
$MESS['WYSIWYG_ON']    = 'Включить';

$MESS['USE_BX_CAPTCHA'] = 'Выводить Битрикс-CAPTCHA';


//TIP
$MESS['CRM_LEAD_TITLE_TIP']       = 'По умолчанию подставится адрес сайта, но можно сформировать из макросов полей CRM, например: #LAST_NAME# #NAME# #SECOND_NAME#.<br>Посмотреть коды других полей можете в настройках поля ниже в выпадающем списке';
$MESS['POST_EMAIL_FROM_TIP']      = 'Если поле оставить пустым, то компонент сначала ищет E-mail в настроках сайта, если там нет, то в настройках главного модуля';
$MESS['POST_EMAIL_TO_TIP']        = 'Если поле оставить пустым, то компонент сначала ищет E-mail в настроках сайта, если там нет, то в настройках главного модуля';
$MESS['POST_REPLACE_FROM_TIP']    = 'Будьте осторожны, часть хостингов и почтовиков либо помечают как спам, либо полностью блокируют отправку писем с подменой поля От кого, но не все.';
$MESS['UNIQUE_FORM_ID_TIP']       = 'Если оставить пустым, тогда форма автоматически назначит уникальный идентификатор';
$MESS['FORM_AUTOCOMPLETE_TIP']    = 'Дает возможность подстановки браузером ранее введенные пользователем данные в поля формы, как подсказки при клике в поле, по умолчанию включено';
$MESS['FORM_WIDTH_TIP']           = 'Ширину можно задавать в пикселя и процентах, если очистить поле то ширина не задается, форма растянется на всю возможную ширину';
$MESS['PAGE_VARS_TIP']            = 'Данные переменные будут работать только на странице, во включаемом файле или области не будет работать';
$MESS['USER_CONSENT_REPLACE_TIP'] = 'Впишите через запятую те поля формы, которые собирают персональные данные, например:<br> Ваше имя, Ваш e-mail, Номер телефона, IP-адрес';
$MESS['COMPATIBLE_ON_TIP']        = 'Включайте для совместимости только для скопированного ранее версии 2.2.0 шаблона, который не .default, т.е. не встроенный, а скопированный и измененный вами';
$MESS['GA_ON_TIP']                = 'Отслеживание событий:<br>
https://developers.google.com/analytics/devguides/collection/analyticsjs/events?hl=ru';
$MESS['GA_GTAG_TIP']              = "Пример события отправки формы:<br>
gtag('event', 'submit', {<br>
&nbsp;&nbsp;'event_category': 'forms',<br>
&nbsp;&nbsp;'event_label': 'Обратная связь',<br>
&nbsp;&nbsp;'value': 1<br>
});
";
$MESS['YM2_GOAL_SUBMIT_FORM_SUCCESS_TIP'] = 'Идентификатор js-события в метрике и в этом поле должен совпадать, желательно включать идентификатор самой формы, чтобы не запутаться, если целей будет много, например: form1_submit_form_success';