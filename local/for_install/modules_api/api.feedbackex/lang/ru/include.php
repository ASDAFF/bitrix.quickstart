<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$MESS['AFEX_INCLUDE_TRANSLIT_FROM'] = 'а,б,в,г,д,е,ё,ж,з,и,й,к,л,м,н,о,п,р,с,т,у,ф,х,ц,ч,ш,щ,ъ,ы,ь,э,ю,я,А,Б,В,Г,Д,Е,Ё,Ж,З,И,Й,К,Л,М,Н,О,П,Р,С,Т,У,Ф,Х,Ц,Ч,Ш,Щ,Ъ,Ы,Ь,Э,Ю,Я,@';
$MESS['AFEX_INCLUDE_TRANSLIT_TO']   = 'a,b,v,g,d,e,ye,zh,z,i,y,k,l,m,n,o,p,r,s,t,u,f,kh,ts,ch,sh,shch,,y,,e,yu,ya,A,B,V,G,D,E,YE,ZH,Z,I,Y,K,L,M,N,O,P,R,S,T,U,F,KH,TS,CH,SH,SHCH,,Y,,E,YU,YA,at';

$MESS['AFEX_INCLUDE_EVENT_SEND_ERROR'] = 'Ошибка! Сообщение не отправляется';
$MESS['AFEX_INCLUDE_EVENT_MESS_ERROR'] = 'Ошибка! В настройках компонента не задан почтовый шаблон';
$MESS['AFEX_INCLUDE_DOWNLOAD_FILES']   = 'Загруженные файлы';

$MESS['AFEX_INCLUDE_FORM_FIELDS'] = array(
	//Пользователь - Базовые поля
	'TITLE'               => array(
		'NAME' => 'Ваше имя',
		'TYPE' => 'STRING',
	),
	'LAST_NAME'           => array(
		'NAME' => 'Фамилия',
		'TYPE' => 'STRING',
	),
	'NAME'                => array(
		'NAME' => 'Имя',
		'TYPE' => 'STRING',
	),
	'SECOND_NAME'         => array(
		'NAME' => 'Отчество',
		'TYPE' => 'STRING',
	),
	'EMAIL'               => array(
		'NAME' => 'Ваш E-mail',
		'TYPE' => 'EMAIL',
	),
	'PHONE'               => array(
		'NAME' => 'Мобильный телефон',
		'TYPE' => 'STRING',
	),
	'MESSAGE'               => array(
		'NAME' => 'Сообщение',
		'TYPE' => 'TEXTAREA',
	),
	'LOGIN'               => array(
		'NAME' => 'Логин',
		'TYPE' => 'STRING',
	),
	'PASSWORD'            => array(
		'NAME' => 'Пароль',
		'TYPE' => 'PASSWORD',
	),
	'CONFIRM_PASSWORD'    => array(
		'NAME' => 'Подтвердите пароль',
		'TYPE' => 'PASSWORD',
	),
	'XML_ID'              => array(
		'NAME' => 'Внешний код',
		'TYPE' => 'STRING',
	),

	//Личные данные
	'PERSONAL_PROFESSION' => array(
		'NAME' => 'Профессия',
		'TYPE' => 'STRING',
	),
	'PERSONAL_WWW'        => array(
		'NAME' => 'Веб-сайт',
		'TYPE' => 'STRING',
	),
	'PERSONAL_ICQ'        => array(
		'NAME' => 'ICQ',
		'TYPE' => 'STRING',
	),
	/*'PERSONAL_GENDER'     => array(
		'NAME' => 'Пол',
		'TYPE' => 'SELECT',
	),*/
	'PERSONAL_BIRTHDAY'   => array(
		'NAME' => 'Дата рождения',
		'TYPE' => 'DATE',
	),
	/*'PERSONAL_PHOTO'      => array(
		'NAME' => 'Фотография',
		'TYPE' => 'FILE',
	),*/
	'PERSONAL_PHONE'      => array(
		'NAME' => 'Телефон',
		'TYPE' => 'STRING',
	),
	'PERSONAL_FAX'        => array(
		'NAME' => 'Факс',
		'TYPE' => 'STRING',
	),
	'PERSONAL_MOBILE'     => array(
		'NAME' => 'Мобильный',
		'TYPE' => 'STRING',
	),
	'PERSONAL_PAGER'      => array(
		'NAME' => 'Пейджер',
		'TYPE' => 'STRING',
	),
	/*'PERSONAL_COUNTRY'      => array(
		'NAME' => 'Страна',
		'TYPE' => 'SELECT',
	),*/
	'PERSONAL_STATE'      => array(
		'NAME' => 'Область / край',
		'TYPE' => 'STRING',
	),
	'PERSONAL_CITY'       => array(
		'NAME' => 'Город',
		'TYPE' => 'STRING',
	),
	'PERSONAL_ZIP'        => array(
		'NAME' => 'Почтовый индекс',
		'TYPE' => 'STRING',
	),
	'PERSONAL_STREET'     => array(
		'NAME' => 'Улица, дом',
		'TYPE' => 'TEXTAREA',
	),
	'PERSONAL_MAILBOX'    => array(
		'NAME' => 'Почтовый ящик',
		'TYPE' => 'STRING',
	),
	'PERSONAL_NOTES'      => array(
		'NAME' => 'Дополнительные заметки',
		'TYPE' => 'TEXTAREA',
	),

	//Работа
	'WORK_COMPANY'        => array(
		'NAME' => 'Компания',
		'TYPE' => 'STRING',
	),
	'WORK_WWW'            => array(
		'NAME' => 'Веб-сайт',
		'TYPE' => 'STRING',
	),
	'WORK_DEPARTMENT'     => array(
		'NAME' => 'Департамент / Отдел',
		'TYPE' => 'STRING',
	),
	'WORK_POSITION'       => array(
		'NAME' => 'Должность',
		'TYPE' => 'STRING',
	),
	'WORK_PROFILE'        => array(
		'NAME' => 'Направления деятельности',
		'TYPE' => 'TEXTAREA',
	),
	/*'WORK_LOGO'           => array(
		'NAME' => 'Логотип компании',
		'TYPE' => 'STRING',
	),*/
	'WORK_PHONE'          => array(
		'NAME' => 'Телефон',
		'TYPE' => 'STRING',
	),
	'WORK_FAX'            => array(
		'NAME' => 'Факс',
		'TYPE' => 'STRING',
	),
	'WORK_PAGER'          => array(
		'NAME' => 'Пейджер',
		'TYPE' => 'STRING',
	),
	/*'WORK_COUNTRY'        => array(
		'NAME' => 'Страна',
		'TYPE' => 'STRING',
	),*/
	'WORK_STATE'          => array(
		'NAME' => 'Область / край',
		'TYPE' => 'STRING',
	),
	'WORK_CITY'           => array(
		'NAME' => 'Город',
		'TYPE' => 'STRING',
	),
	'WORK_ZIP'            => array(
		'NAME' => 'Почтовый индекс',
		'TYPE' => 'STRING',
	),
	'WORK_STREET'         => array(
		'NAME' => 'Улица, дом',
		'TYPE' => 'TEXTAREA',
	),
	'WORK_MAILBOX'        => array(
		'NAME' => 'Почтовый ящик',
		'TYPE' => 'STRING',
	),
	'WORK_NOTES'          => array(
		'NAME' => 'Дополнительные заметки',
		'TYPE' => 'TEXTAREA',
	),
);