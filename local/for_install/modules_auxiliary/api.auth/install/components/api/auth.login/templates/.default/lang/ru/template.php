<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

//---------- class.php ----------//
$MESS['API_AUTH_LOGIN_PAGE_TITLE']   = 'Вход на сайт';


//---------- template.php ----------//
$MESS['API_AUTH_LOGIN_SECURE_NOTE']    = 'Перед отправкой формы пароль будет зашифрован. Это позволит избежать передачи пароля в открытом виде.';
$MESS['API_AUTH_LOGIN_BUTTON']         = 'Войти';
$MESS['API_AUTH_LOGIN_FIELD_LOGIN']    = 'Логин';
$MESS['API_AUTH_LOGIN_FIELD_EMAIL']    = 'E-mail';
$MESS['API_AUTH_LOGIN_LOGIN_OR_EMAIL'] = 'Логин или E-mail';
$MESS['API_AUTH_LOGIN_FIELD_PASSWORD'] = 'Пароль';
$MESS['API_SOC_AUTH_TITLE']            = 'Войти через социальные сети';

$MESS['API_AUTH_LOGIN_LOGIN_URL']    = '';
$MESS['API_AUTH_LOGIN_RESTORE_URL']  = 'Вспомнить пароль';
$MESS['API_AUTH_LOGIN_REGISTER_URL'] = 'Регистрация';

//CAPTCHA
$MESS['API_AUTH_LOGIN_TPL_CAPTCHA_SID']     = 'Код защиты от автоматических сообщений';
$MESS['API_AUTH_LOGIN_TPL_CAPTCHA_WORD']    = 'Введите код защиты';
$MESS['API_AUTH_LOGIN_TPL_CAPTCHA_REFRESH'] = 'Нажмите, чтобы обновить код защиты';
$MESS['API_AUTH_LOGIN_TPL_CAPTCHA_LOADING'] = 'Закгрузка captcha...';

//USER_CONSENT
$MESS['API_AUTH_LOGIN_USER_CONSENT_ERROR']         = 'Для продолжения вы должны принять согласие на обработку персональных данных';
$MESS['API_AUTH_LOGIN_USER_CONSENT_TITLE']         = 'Согласие на обработку персональных данных';
$MESS['API_AUTH_LOGIN_USER_CONSENT_FIELDS']        = 'Логин, E-mail, Пароль, IP-адрес';
$MESS['API_AUTH_LOGIN_USER_CONSENT_BTN_ACCEPT']    = 'Принимаю';
$MESS['API_AUTH_LOGIN_USER_CONSENT_BTN_REJECT']    = 'Не принимаю';
$MESS['API_AUTH_LOGIN_USER_CONSENT_LOADING']       = 'Загрузка..';
$MESS['API_AUTH_LOGIN_USER_CONSENT_ERR_TEXT_LOAD'] = 'Не удалось загрузить текст соглашения.';


//---------- ajax.php ----------//
$MESS['API_AUTH_LOGIN_MESS_SUCCESS'] = 'Добро пожаловать на сайт!';