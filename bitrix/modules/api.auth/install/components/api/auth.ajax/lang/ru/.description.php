<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$MESS['API_AUTH_AJAX_DESCRIPTION'] = array(
	 'NAME'        => 'Комплексная авторизация в модальном окне',
	 'DESCRIPTION' => 'Компонент выводит всю авторизацию в модальном окне',
	 'ICON'        => '/images/icon.gif',
	 'SORT'        => 2,
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