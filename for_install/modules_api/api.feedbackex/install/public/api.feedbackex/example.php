<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$arFields = array(
	 'ONE'   => array(
			'NAME' => 'Строка',
			'TYPE' => 'STRING',
	 ),
	 'TWO'   => array(
			'NAME' => 'E-mail',
			'TYPE' => 'EMAIL',
	 ),
	 /*'THREE'  => array(
			'NAME' => 'Пароль',
			'TYPE' => 'PASSWORD',
	 ),*/
	 'FOUR'  => array(
			'NAME' => 'Дата',
			'TYPE' => 'DATE',
	 ),
	 'FIVE'  => array(
			'NAME' => 'Дата и время',
			'TYPE' => 'DATE_TIME',
	 ),
	 'SIX'   => array(
			'NAME'   => 'Список',
			'TYPE'   => 'SELECT',
			'VALUES' => array('Да', 'Нет'),
	 ),
	 'SEVEN' => array(
			'NAME'   => 'Флажки',
			'TYPE'   => 'CHECKBOX',
			'VALUES' => array('Да', 'Нет'),
	 ),
	 'EIGHT' => array(
			'NAME'   => 'Радиокнопки',
			'TYPE'   => 'RADIO',
			'VALUES' => array('Да', 'Нет'),
	 ),
	 'NINE'  => array(
			'NAME' => 'Текст',
			'TYPE' => 'TEXTAREA',
	 ),
);