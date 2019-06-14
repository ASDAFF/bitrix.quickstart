<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$MESS['API_MAIN_PROFILE_DESCRIPTION'] = array(
	 'NAME'        => 'Профиль пользователя',
	 'DESCRIPTION' => 'Компонент выводит форму редактирования профиля пользователя',
	 'ICON'        => '/images/icon.gif',
	 'SORT'        => 6,
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