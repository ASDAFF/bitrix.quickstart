<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

//---------- component.php ----------//
$MESS['AFDC_MODULE_ERROR'] = 'Модуль "TS Умный конструктор форм на инфоблоках" не установлен';
$MESS['AFDC_IBLOCK_ERROR'] = 'Модуль "Инфоблоки" не установлен';

//ERROR
$MESS['AFDC_DEFAULT_MESS_CHECK_EMAIL']    = 'Указанный e-mail некорректен';
$MESS['AFDC_DEFAULT_MESS_REQUIRED_FIELD'] = '#FIELD# обязательно';

//DANGER
$MESS['AFDC_DANGER_CHECK_BITRIX_SESSID'] = 'Ошибка! Ваша сессия истекла, отправьте сообщение повторно';
$MESS['AFDC_DANGER_SEND_ADMIN_MESSAGE']  = 'Ошибка! Cообщение администратору не отправилось, попробуйте еще раз';
$MESS['AFDC_DANGER_SEND_USER_MESSAGE']   = 'Ошибка! Копия сообщения вам не отправилась, попробуйте еще раз';
$MESS['AFDC_DANGER_CAPTCHA_WRONG']       = 'Неверно указан код защиты';

//WARNING
$MESS['AFDC_WARNING_IBLOCK_TICKET_CODE']    = 'В разделе настроек «Запись в инфоблок» не задан  «Код свойства для нумерации тикетов»';
$MESS['AFDC_WARNING_POST_EMAIL_CODE']       = 'В разделе настроек «Отправка писем» включена опция «Подменять поле "От" на E-mail посетителя», а свойство «E-Mail адрес посетителя» в разделе «Основные параметры» не задано';
$MESS['AFDC_WARNING_POST_ADMIN_MESSAGE_ID'] = 'В разделе настроек «Отправка писем» не задан  «Почтовый шаблон для администратора»';
$MESS['AFDC_WARNING_UNIQUE_FORM_ID']        = 'В разделе настроек «Основные параметры» не задан ID формы';
$MESS['AFDC_WARNING_CRM_LEAD']              = 'В разделе настроек «CRM» укажите CRM';
$MESS['AFDC_WARNING_IBLOCK_ID']             = 'В разделе настроек «Основные параметры» не задан Инфоблок';
$MESS['AFDC_WARNING_UPLOAD_FOLDER']         = 'Скрипт не может создать папку для загрузки файлов, недостаточно прав';

//DEFAULT MESS
$MESS['AFDC_DEFAULT_MESS_SUCCESS']       = 'Заявка №#TICKET_ID# отправлена!';
$MESS['AFDC_DEFAULT_MESS_SUCCESS_DESC']  = 'Мы рассмотрим сообщение и обязательно свяжемся с Вами';
$MESS['AFDC_DEFAULT_MESS_SUCCESS_BTN']   = 'Обновить';
$MESS['AFDC_DEFAULT_SUBMIT_BUTTON_TEXT'] = 'Отправить заявку';
$MESS['AFDC_DEFAULT_SUBMIT_BUTTON_AJAX'] = 'Отправляется заявка...';
$MESS['AFDC_DEFAULT_MESS_CHOOSE']        = '(выбрать)';
$MESS['AFDC_DEFAULT_POST_ADMIN_SUBJECT'] = '#SITE_NAME#: Сообщение из формы обратной связи';
$MESS['AFDC_DEFAULT_POST_USER_SUBJECT']  = '#SITE_NAME#: Копия сообщения из формы обратной связи';


//---------- template.php ----------//

//USER_CONSENT
$MESS['AFDC_USER_CONSENT_ERROR']         = 'Для продолжения вы должны принять согласие на обработку персональных данных';
$MESS['AFDC_USER_CONSENT_TITLE']         = 'Согласие на обработку персональных данных';
$MESS['AFDC_USER_CONSENT_BTN_ACCEPT']    = 'Принимаю';
$MESS['AFDC_USER_CONSENT_BTN_REJECT']    = 'Не принимаю';
$MESS['AFDC_USER_CONSENT_LOADING']       = 'Загрузка..';
$MESS['AFDC_USER_CONSENT_ERR_TEXT_LOAD'] = 'Не удалось загрузить текст соглашения.';



$MESS['AFD_AJAX_UPLOAD_DROP'] = 'Перетащите сюда файлы или нажмите для выбора';
$MESS['AFD_AJAX_UPLOAD_INFO'] = 'Максимальный размер загружаемого файла #UPLOAD_FILE_SIZE# в формате #FILE_TYPE#.<br>
Максимальное количество файлов - #UPLOAD_FILE_LIMIT# шт.<br>';

$MESS['AFD_AJAX_UPLOAD_onFileSizeError'] = '{{fileName}} размером {{fileSize}} превышает допустимый размер <b>{{maxFileSize}}</b>';
$MESS['AFD_AJAX_UPLOAD_onFileTypeError'] = 'Тип файла {{fileType}} не соответствует разрешенному {{allowedTypes}}';
$MESS['AFD_AJAX_UPLOAD_onFileExtError']  = 'Разрешены следующие расширения файлов: <b>{{extFilter}}</b>';
$MESS['AFD_AJAX_UPLOAD_onFilesMaxError'] = 'Разрешено максимум {{maxFiles}} файлов';

//CAPTCHA
$MESS['AFD_AJAX_FIELD_CAPTCHA_SID']     = 'Защита от роботов';
$MESS['AFD_AJAX_FIELD_CAPTCHA_WORD']    = 'Введите код защиты';
$MESS['AFD_AJAX_FIELD_CAPTCHA_REFRESH'] = 'Нажмите, чтобы обновить код защиты';
$MESS['AFD_AJAX_FIELD_CAPTCHA_LOADING'] = 'Закгрузка captcha...';