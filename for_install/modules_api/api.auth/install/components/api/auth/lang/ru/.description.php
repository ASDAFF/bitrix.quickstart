<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$MESS['API_AUTH_DESCRIPTION'] = array(
	 'NAME'        => 'Комплексная авторизация',
	 'DESCRIPTION' => 'Компонент выводит все необходимые компоненты авторизации',
	 'ICON'        => '/images/icon.gif',
	 'SORT'        => 1,
	 'COMPLEX'     => 'Y',
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