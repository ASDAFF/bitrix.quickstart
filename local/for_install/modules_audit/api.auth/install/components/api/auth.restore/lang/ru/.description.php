<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$MESS['API_AUTH_RESTORE_DESCRIPTION'] = array(
	 'NAME'        => 'Вспомнить пароль',
	 'DESCRIPTION' => 'Компонент восстановления пароля',
	 'ICON'        => '/images/icon.gif',
	 'SORT'        => 8,
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