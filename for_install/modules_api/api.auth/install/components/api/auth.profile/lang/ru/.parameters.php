<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

//BASE
$MESS['API_MAIN_PROFILE_PM_CHECK_RIGHTS']    = 'Проверять права доступа';
$MESS['API_MAIN_PROFILE_PM_USER_FIELDS']     = 'Поля пользователя';
$MESS['API_MAIN_PROFILE_PM_CUSTOM_FIELDS']   = 'Доп. поля';
$MESS['API_MAIN_PROFILE_PM_REQUIRED_FIELDS'] = 'Обязательные поля';
$MESS['API_MAIN_PROFILE_PM_READONLY_FIELDS'] = 'Только для чтения';
$MESS['API_MAIN_PROFILE_PM_SEND_MAIL']       = 'Отправлять письмо при изменениях профиля';

//VISUAL
$MESS['API_MAIN_PROFILE_PM_SHOW_LABEL'] = 'Выводить название поля';


$MESS['API_MAIN_PROFILE_PM_FIELDS'] = array(
	 'ID'                 => 'ID ----- Регистрационная информация -----',
	 'ACTIVE'             => 'Активность',
	 'DATE_REGISTER'      => 'Дата регистрации',
	 'LAST_LOGIN'         => 'Дата последней авторизации',
	 'LAST_ACTIVITY_DATE' => 'Дата последнего хита на сайте',
	 'TIMESTAMP_X'        => 'Дата последнего изменения',

	 'TITLE'               => 'Обращение ----- Личные данные -----',
	 'LAST_NAME'           => 'Фамилия',
	 'NAME'                => 'Имя',
	 'SECOND_NAME'         => 'Отчество',
	 'LOGIN'               => 'Логин',
	 'EMAIL'               => 'E-mail',
	 'PASSWORD'            => 'Новый пароль',
	 'CONFIRM_PASSWORD'    => 'Подтверждение пароля',
	 //'EXTERNAL_AUTH_ID'    => 'Код источника внешней авторизации',
	 //'XML_ID'              => 'Код внешнего источника',
	 'LANGUAGE_ID'         => 'Язык',

	 //PERSONAL
	 'PERSONAL_PROFESSION' => 'Профессия ----- Информация о работе -----',
	 'PERSONAL_PHONE'      => 'Телефон',
	 'PERSONAL_MOBILE'     => 'Мобильный телефон',
	 'PERSONAL_WWW'        => 'Веб-страница',
	 'PERSONAL_ICQ'        => 'ICQ',
	 'PERSONAL_FAX'        => 'Факс',
	 'PERSONAL_PAGER'      => 'Пейджер',
	 'PERSONAL_MAILBOX'    => 'Почтовый ящик',
	 'PERSONAL_BIRTHDAY'   => 'Дата рождения',
	 'PERSONAL_GENDER'     => 'Пол',
	 'PERSONAL_COUNTRY'    => 'Страна',
	 'PERSONAL_STATE'      => 'Регион',
	 'PERSONAL_CITY'       => 'Город',
	 'PERSONAL_STREET'     => 'Улица',
	 'PERSONAL_ZIP'        => 'Почтовый индекс',
	 'PERSONAL_PHOTO'      => 'Фотография',
	 'PERSONAL_NOTES'      => 'Дополнительные заметки',

	 //WORK
	 'WORK_COMPANY'        => '----- Компания -----',
	 'WORK_DEPARTMENT'     => 'Департамент / Отдел',
	 'WORK_PHONE'          => 'Телефон',
	 'WORK_POSITION'       => 'Должность',
	 'WORK_WWW'            => 'Веб-страница',
	 'WORK_FAX'            => 'Факс',
	 'WORK_PAGER'          => 'Пейджер',
	 'WORK_MAILBOX'        => 'Почтовый ящик',
	 'WORK_PROFILE'        => 'Направления деятельности',
	 'WORK_COUNTRY'        => 'Страна',
	 'WORK_STATE'          => 'Регион',
	 'WORK_CITY'           => 'Город',
	 'WORK_STREET'         => 'Улица',
	 'WORK_ZIP'            => 'Почтовый индекс',
	 'WORK_LOGO'           => 'Логотип компании',
	 'WORK_NOTES'          => 'Дополнительные заметки',
);


//TIP
$MESS['CHECK_RIGHTS_TIP'] = 'Проверяется право доступа: Главный модуль - Изменение своего профиля';