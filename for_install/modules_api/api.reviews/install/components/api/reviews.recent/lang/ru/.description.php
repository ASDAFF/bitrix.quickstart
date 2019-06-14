<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$MESS['API_REVIEWS_LATEST_DESCRIPTION'] = array(
	 'NAME'        => 'Последние отзывы',
	 'DESCRIPTION' => 'Компонент выводит список последних отзывов',
	 'ICON'        => '/images/icon.gif',
	 'SORT'        => 10,
	 'COMPLEX'     => 'N',
	 'CACHE_PATH'  => 'Y',
	 'PATH'        => array(
			'ID'    => 'tuning-soft',
			'NAME'  => 'Тюнинг-Софт',
			'CHILD' => array(
				 'ID'   => 'api_reviews',
				 'NAME' => 'TS Умные отзывы о магазине и о товаре',
			),
	 ),
);
