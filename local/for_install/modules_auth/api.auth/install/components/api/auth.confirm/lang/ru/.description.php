<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$MESS['API_AUTH_CONFIRM_DESCRIPTION'] = array(
	 'NAME'        => 'Подтверждение',
	 'DESCRIPTION' => 'Компонент подтверждения регистрации',
	 'ICON'        => '/images/icon.gif',
	 'SORT'        => 4,
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