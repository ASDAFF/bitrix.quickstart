<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$arFields = array(
	 'NAME'   => array(
			'NAME' => 'Ваше имя',
			'TYPE' => 'STRING',
	 ),
	 'EMAIL'   => array(
			'NAME' => 'Ваш E-mail',
			'TYPE' => 'EMAIL',
	 ),
	 'PHONE'   => array(
		  'NAME' => 'Ваш телефон',
		  'TYPE' => 'STRING',
	 ),
	 'MESSAGE'  => array(
			'NAME' => 'Сообщение',
			'TYPE' => 'TEXTAREA',
	 ),
);