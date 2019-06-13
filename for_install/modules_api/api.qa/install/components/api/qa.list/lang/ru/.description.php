<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$MESS['API_QA_LIST_DESCRIPTION'] = array(
	 'NAME'        => 'Список вопросов и ответов',
	 'DESCRIPTION' => 'Компонент выводит список вопросов и ответов',
	 'ICON'        => '/images/icon.gif',
	 'SORT'        => 10,
	 'COMPLEX'     => 'N',
	 'CACHE_PATH'  => 'Y',
	 'PATH'        => array(
			'ID'    => 'tuning-soft',
			'NAME'  => 'Тюнинг-Софт',
			'CHILD' => array(
				 'ID'   => 'api_qa',
				 'NAME' => 'TS Умные вопросы и ответы',
			),
	 ),
);
