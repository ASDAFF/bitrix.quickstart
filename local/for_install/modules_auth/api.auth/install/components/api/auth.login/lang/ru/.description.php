<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$MESS['API_AUTH_LOGIN_DESCRIPTION'] = array(
	 'NAME'        => 'Вход на сайт',
	 'DESCRIPTION' => 'Компонент входа на сайт',
	 'ICON'        => '/images/icon.gif',
	 'SORT'        => 5,
	 'COMPLEX'     => 'N',
	 'CACHE_PATH'  => 'Y',
	 'PATH'        => array(
			'ID'    => 'tuning-soft',
			'NAME'  => 'Тюнинг-Софт',
			'CHILD' => array(
				 'ID'   => 'api_auth',
				 'NAME' => 'TS Умная авторизация',
			),
	 ),
);