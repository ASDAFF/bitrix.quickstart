<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$MESS['API_AUTH_REGISTER_DESCRIPTION'] = array(
	 'NAME'        => 'Регистрация',
	 'DESCRIPTION' => 'Компонент регистрации',
	 'ICON'        => '/images/icon.gif',
	 'SORT'        => 7,
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