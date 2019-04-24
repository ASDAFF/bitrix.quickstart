<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

//BASE
$MESS['API_FEX_PARAMS_OPTION_SET'] = '(выбрать)';
$MESS['API_FEX_PARAMS_OPTION_ALL'] = '(все)';
$MESS['DISABLE_SEND_MAIL']         = 'Отключить отправку писем';
$MESS['DISABLE_CHECK_SESSID']      = 'Отключить проверку сессий';

//FORM
$MESS['GROUP_FORM']                     = 'Поля формы';
$MESS['API_FEX_PARAMS_CONFIG_PATH']     = 'Путь до своего конфига полей';
$MESS['API_FEX_PARAMS_DISPLAY_FIELDS']  = 'Поля формы';
$MESS['API_FEX_PARAMS_REQUIRED_FIELDS'] = 'Обязательные поля';

//VISUAL
$MESS['HIDE_FIELD_NAME']              = 'Скрывать названия полей  формы';
$MESS['MFP_FORM_TITLE_DISPLAY']       = 'Показывать заголовок  формы';
$MESS['MFP_FORM_TITLE']               = 'Заголовок формы';
$MESS['MFP_FORM_TITLE_VALUE']         = 'Обратная связь';
$MESS['MFP_FORM_TITLE_LEVEL']         = 'Уровень заголовка';
$MESS['MFP_FORM_TITLE_LEVEL_VALUES']  = array('1' => '1', '2' => '2', '3' => '3', '4' => '4');
$MESS['FORM_LABEL_TEXT_ALIGN']        = 'Выравнивание текста в названии поля';
$MESS['FORM_LABEL_TEXT_ALIGN_VALUES'] = array('left' => 'Слева', 'center' => 'По центру', 'right' => 'Справа');
$MESS['FORM_SUBMIT_CLASS']            = 'Класс для кнопки "Отправить"';
$MESS['FORM_SUBMIT_VALUE']            = 'Текст для кнопки "Отправить"';
$MESS['FORM_SUBMIT_VALUE_DEFAULT']    = 'Отправить';
$MESS['FORM_SUBMIT_STYLE']            = 'Стили для кнопки "Отправить"';
$MESS['UNIQUE_FORM_ID']               = 'ID формы';
$MESS['MFP_OK_MESSAGE']               = 'Сообщение, выводимое пользователю после отправки';
$MESS['MFP_OK_TEXT']                  = 'Сообщение успешно отправлено';
$MESS['OK_TEXT_AFTER']                = 'Текст под сообщением';
$MESS['OK_TEXT_AFTER_DEFAULT']        = 'Спасибо! Мы рассмотрим сообщение и обязательно свяжемся с Вами.<br>Пожалуйста, дождитесь ответа.';
$MESS['MFP_EMAIL_TO']                 = 'E-mail, на который будет отправлено письмо';
$MESS['MFP_BCC']                      = 'Скрытая копия';
$MESS['REDIRECT_PAGE']                = 'Страница перенаправления';
$MESS['REPLACE_FIELD_FROM']           = 'Заменять в письме "От кого" на "E-mail" посетителя';
$MESS['FILE_SETTINGS']                = 'Загрузка файлов';
$MESS['INCLUDE_VALIDATION']           = 'Включить jQuery валидатор формы';
$MESS['INCLUDE_INPUTMASK']            = 'Включить маски для полей';
$MESS['INCLUDE_CHOSEN']               = 'Включить оформление списков (select)';
$MESS['FORM_TEXT_BEFORE']             = 'Текст над формой';
$MESS['FORM_TEXT_AFTER']              = 'Текст под формой';
$MESS['BUTTON_TEXT_BEFORE']           = 'Текст над кнопкой';
$MESS['DEFAULT_OPTION_TEXT']          = 'Текст опции по умолчанию';
$MESS['DEFAULT_OPTION_VALUE']         = '-- Выбрать --';
$MESS['FIELD_SIZE']                   = 'Размер полей и кнопок';
$MESS['HIDE_ASTERISK']                = 'Убрать двоеточие и звездочки';
$MESS['FORM_AUTOCOMPLETE']            = 'Автокомплит значений полей формы';
$MESS['FIELD_NAME_POSITION']          = 'Позиция названия поля';
$MESS['FIELD_NAME_POSITION_VALUES']   = array('horizontal' => 'Слева', 'stacked' => 'Над полем');
$MESS['FORM_LABEL_WIDTH']             = 'Ширина названия поля, %/px';
$MESS['FORM_LABEL_WIDTH_VALUE']       = '150px';
$MESS['FORM_FIELD_WIDTH']             = 'Ширина поля, %/px';
$MESS['FORM_FIELD_WIDTH_VALUE']       = '';
$MESS['FORM_TEXTAREA_ROWS']           = 'Высота текстовой области (textarea), число';
$MESS['FIELD_ERROR_MESS']             = 'Шаблон сообщения об ошибке в поле';
$MESS['FIELD_ERROR_MESS_VALUE']       = '#FIELD_NAME# обязательное';
$MESS['FILE_ERROR_MESS']              = 'Cообщение об ошибке';
$MESS['FILE_ERROR_MESS_VALUE']        = 'Все файлы обязательные';
$MESS['EMAIL_ERROR_MESS']             = 'Cообщение о некорректном e-mail';
$MESS['EMAIL_ERROR_MESS_VALUE']       = 'Указанный E-mail некорректен';
$MESS['FORM_WIDTH']                   = 'Ширина обертки формы, %/px';
$MESS['FORM_WIDTH_DEFAULT']           = '';
$MESS['FORM_CLASS']                   = 'CSS-классы обертки формы';
$MESS['FIELD_BORDER_ACTIVE']          = 'border (рамка) активного поля';
$MESS['FIELD_BOX_SHADOW_ACTIVE']      = 'box-shadow (тень) активного поля';


//MAIL
$MESS['GROUP_MAIL']                           = 'Почта';
$MESS['MAIL_SUBJECT_ADMIN']                   = 'Тема сообщения для администратора';
$MESS['MAIL_SUBJECT_USER']                    = 'Тема сообщения для посетителя';
$MESS['MAIL_SUBJECT_ADMIN_DEFAULT']           = '#SITE_NAME#: Сообщение из формы обратной связи';
$MESS['MAIL_SUBJECT_USER_DEFAULT']            = '#SITE_NAME#: Копия сообщения из формы обратной связи';
$MESS['MAIL_SEND_USER']                       = 'Отправить копию письма посетителю';
$MESS['WRITE_MESS_FILDES_TABLE']              = 'Записывать поля в почтовый шаблон таблицей';
$MESS['WRITE_MESS_TABLE_STYLE']               = 'Стили для <table> всех полей';
$MESS['WRITE_MESS_TABLE_STYLE_DEFAULT']       = 'border-collapse: collapse; border-spacing: 0;';
$MESS['WRITE_MESS_TABLE_STYLE_NAME']          = 'Стили для <td> названия поля';
$MESS['WRITE_MESS_TABLE_STYLE_NAME_DEFAULT']  = 'max-width: 200px; color: #848484; vertical-align: middle; padding: 5px 30px 5px 0px; border-bottom: 1px solid #e0e0e0; border-top: 1px solid #e0e0e0;';
$MESS['WRITE_MESS_TABLE_STYLE_VALUE']         = 'Стили для <td> значения поля';
$MESS['WRITE_MESS_TABLE_STYLE_VALUE_DEFAULT'] = 'vertical-align: middle; padding: 5px 30px 5px 0px; border-bottom: 1px solid #e0e0e0; border-top: 1px solid #e0e0e0;';
$MESS['WRITE_MESS_DIV_STYLE']                 = 'Стили для <div> поля';
$MESS['WRITE_MESS_DIV_STYLE_DEFAULT']         = 'padding:10px;border-bottom:1px dashed #dadada;';
$MESS['WRITE_MESS_DIV_STYLE_NAME']            = 'Стили для <div> названия поля';
$MESS['WRITE_MESS_DIV_STYLE_NAME_DEFAULT']    = 'font-weight:bold;';
$MESS['WRITE_MESS_DIV_STYLE_VALUE']           = 'Стили для <div> значения поля';
$MESS['WRITE_MESS_DIV_STYLE_VALUE_DEFAULT']   = '';


//JQUERY
$MESS['GROUP_JQUERY']        = "jQuery плагины";
$MESS['INCLUDE_JQUERY']      = 'Включить jQuery-1.8.3 если что-то не работает';
$MESS['INCLUDE_PLACEHOLDER'] = 'Включить placeholder';
$MESS['INCLUDE_AUTOSIZE']    = 'Включить autosize';
$MESS['INCLUDE_FLATPICKR']   = 'Включить flatpickr';

$MESS['HIDE_FORM_AFTER_SEND']       = 'Прятать форму после отправки';
$MESS['SCROLL_TO_FORM_IF_MESSAGES'] = 'Прокручивать страницу к форме';
$MESS['SCROLL_TO_FORM_SPEED']       = 'Скорость прокрутки страницы';


//YM_GOALS_SETTINGS
$MESS['YM_GOALS_SETTINGS'] = 'Я.Метрика - Цели';
$MESS['USE_YM_GOALS']      = 'Настроить';
$MESS['YM_COUNTER_ID']     = '№ счётчика';
$MESS['YM_TARGET_NAME']    = 'Идентификатор цели';


//SERVICE_MACROS_SETTINGS
$MESS['SERVICE_MACROS_SETTINGS'] = 'Служебные поля';
$MESS['PAGE_TITLE']              = 'Заголовок страницы';
$MESS['PAGE_URL']                = 'URL страницы';
$MESS['DIR_URL']                 = 'URL раздела';
$MESS['DATETIME']                = 'Дата/Время';

//MODAL_SETTINGS
$MESS['MODAL_SETTINGS']               = 'Модальное окно';
$MESS['USE_MODAL']                    = 'Выводить в модальном окне';
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



//EULA
$MESS['GROUP_EULA']                = 'Пользовательское соглашение';
$MESS['USE_EULA']                  = 'Выводить соглашение';
$MESS['MESS_EULA']                 = 'Текст соглашения';
$MESS['MESS_EULA_DEFAULT']         = 'Нажимая кнопку «Отправить», я принимаю условия Пользовательского соглашения и даю своё согласие на обработку моих персональных данных, в соответствии с Федеральным законом от 27.07.2006 года №152-ФЗ «О персональных данных», на условиях и для целей, определенных Политикой конфиденциальности.';
$MESS['MESS_EULA_CONFIRM']         = 'Текст сообщения об ошибке';
$MESS['MESS_EULA_CONFIRM_DEFAULT'] = 'Для продолжения вы должны принять условия Пользовательского соглашения';

//PRIVACY
$MESS['GROUP_PRIVACY']                = 'Персональные данные';
$MESS['USE_PRIVACY']                  = 'Выводить соглашение';
$MESS['MESS_PRIVACY']                 = 'Текст соглашения';
$MESS['MESS_PRIVACY_DEFAULT']         = 'Я согласен на обработку персональных данных';
$MESS['MESS_PRIVACY_LINK']            = 'Ссылка на соглашение';
$MESS['MESS_PRIVACY_CONFIRM']         = 'Текст сообщения об ошибке';
$MESS['MESS_PRIVACY_CONFIRM_DEFAULT'] = 'Для продолжения вы должны принять соглашение на обработку персональных данных';



//---------- Подсказки ----------//
$MESS['WRITE_MESS_FILDES_TABLE_TIP'] = 'Некоторые почтовые серверы разрывают таблицы, если у вас такое наблюдается, необходимо отключить';

$MESS['MODAL_ID_TIP']                   = 'Идентификатор модального окна можете оставить как есть или вписать свой.<br>Необходим для вызова модального окна.<br>Допустимы только латиница, цифры, подчеркивания, как у констант';
$MESS['MODAL_BTN_TEXT_TIP']             = 'Если оставить пустым, встроенная кнопка выводиться не будет, тогда в любом месте дизайна можете разместить свою кнопку/ссылку, по которой можно будет открывать форму';
$MESS['MODAL_BTN_CLASS_TIP']            = 'Любой html-класс';
$MESS['MODAL_BTN_ID_TIP']               = 'Любой html-идентификатор';
$MESS['MODAL_BTN_SPAN_CLASS_TIP']       = 'В кнопке есть html-тег "span" которому вы можете задать свой класс, например, для показа иконки шрифта "Font Awesome"';
$MESS['CONFIG_PATH_TIP'] = 'Хранить конфиги полей формы можно где угодно и для каждой формы отдельно, но желательно здесь, где есть демо-файл:<br>
/bitrix/php_interface/include/api.feedbackex';
$MESS['DISPLAY_FIELDS_TIP'] = 'По умолчанию поля подгружаются из модуля, но можно задать свой конфиг, после нажатия на кнопку Ok данные поля обновятся.<br>
Для удобства можно выбрать (все) и тогда форма атоматически будет выводить все поля из конфига, иначе выводит только выбранные.';
$MESS['REQUIRED_FIELDS_TIP'] = 'Если тут ничего не выбрано, то все поля формы будут необязательными';
