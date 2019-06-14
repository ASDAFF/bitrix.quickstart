<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$MESS['API_TYPO_MODULE_ERROR'] = 'Модуль "TS Умное сообщение об ошибке на сайте" не установлен';

//GROUP_BASE
$MESS['API_TYPO_JQUERY_ON']        = 'Подключать jQuery';
$MESS['API_TYPO_JQUERY_ON_VALUES'] = array(
	 'N'       => '(нет)',
	 'jquery'  => '1.8.3.min.js (встроенная)',
	 'jquery2' => '2.1.3.min.js (встроенная)',
);

$MESS['API_TYPO_AJAX_URL']           = 'URL php-обработчика ajax';
$MESS['API_TYPO_AJAX_URL_DEFAULT']   = '/bitrix/components/api/typo/ajax.php';
$MESS['API_TYPO_MAX_LENGTH']         = 'Максимальная длина текста';
$MESS['API_TYPO_MAX_LENGTH_DEFAULT'] = 300;
$MESS['API_TYPO_EMAIL_FROM']         = 'E-mail, который отправитель';
$MESS['API_TYPO_EMAIL_TO']           = 'E-mail, куда будет отправлено письмо';


//GROUP_MESSAGE
$MESS['API_TYPO_GROUP_MESSAGE']                 = 'Основные фразы';
$MESS['API_TYPO_MESS_TPL_CONTENT']              = 'Текст в шаблоне';
$MESS['API_TYPO_MESS_TPL_CONTENT_DEFAULT']      = 'Нашли ошибку на странице?<br>Выделите её и нажмите Ctrl + Enter';
$MESS['API_TYPO_MESS_ALERT_TEXT_MAX']           = 'Ошибка! Максимум N символов';
$MESS['API_TYPO_MESS_ALERT_TEXT_MAX_DEFAULT']   = 'Ошибка!<br>Максимум 300 символов';
$MESS['API_TYPO_MESS_ALERT_TEXT_EMPTY']         = 'Ошибка! Не выделен текст';
$MESS['API_TYPO_MESS_ALERT_TEXT_EMPTY_DEFAULT'] = 'Ошибка!<br>Не выделен текст с ошибкой';
$MESS['API_TYPO_MESS_ALERT_SEND_OK']            = 'Спасибо! Cообщение отправлено';
$MESS['API_TYPO_MESS_ALERT_SEND_OK_DEFAULT']    = 'Спасибо!<br>Cообщение отправлено';
$MESS['API_TYPO_MESS_MODAL_TITLE']              = 'Заголовок модального окна';
$MESS['API_TYPO_MESS_MODAL_TITLE_DEFAULT']      = 'Сообщить об ошибке';
$MESS['API_TYPO_MESS_MODAL_COMMENT']            = 'Текст поля "Комментарий"';
$MESS['API_TYPO_MESS_MODAL_COMMENT_DEFAULT']    = 'Комментарий (не обязательно)';
$MESS['API_TYPO_MESS_MODAL_SUBMIT']             = 'Текст кнопки "Отправить"';
$MESS['API_TYPO_MESS_MODAL_SUBMIT_DEFAULT']     = 'Отправить';
$MESS['API_TYPO_MESS_MODAL_CLOSE']              = 'Текст кнопки "Закрыть"';
$MESS['API_TYPO_MESS_MODAL_CLOSE_DEFAULT']      = 'Закрыть';


//_TIP
$MESS['AJAX_URL_TIP']   = 'Можете указать путь до своего php-обработчика ajax';
$MESS['EMAIL_FROM_TIP'] = 'Можете оставить пустым';
$MESS['EMAIL_TO_TIP']   = 'Можете оставить пустым';
