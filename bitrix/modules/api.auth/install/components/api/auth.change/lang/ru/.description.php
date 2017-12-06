<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$MESS['API_AUTH_CHANGE_DESCRIPTION'] = array(
	 'NAME'        => 'Смена пароля',
	 'DESCRIPTION' => 'Компонент для смены пароля',
	 'ICON'        => '/images/icon.gif',
	 'SORT'        => 3,
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