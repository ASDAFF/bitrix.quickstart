<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$arFields = array(
	 'ONE'   => array(
			'NAME' => '������',
			'TYPE' => 'STRING',
	 ),
	 'TWO'   => array(
			'NAME' => 'E-mail',
			'TYPE' => 'EMAIL',
	 ),
	 /*'THREE'  => array(
			'NAME' => '������',
			'TYPE' => 'PASSWORD',
	 ),*/
	 'FOUR'  => array(
			'NAME' => '����',
			'TYPE' => 'DATE',
	 ),
	 'FIVE'  => array(
			'NAME' => '���� � �����',
			'TYPE' => 'DATE_TIME',
	 ),
	 'SIX'   => array(
			'NAME'   => '������',
			'TYPE'   => 'SELECT',
			'VALUES' => array('��', '���'),
	 ),
	 'SEVEN' => array(
			'NAME'   => '������',
			'TYPE'   => 'CHECKBOX',
			'VALUES' => array('��', '���'),
	 ),
	 'EIGHT' => array(
			'NAME'   => '�����������',
			'TYPE'   => 'RADIO',
			'VALUES' => array('��', '���'),
	 ),
	 'NINE'  => array(
			'NAME' => '�����',
			'TYPE' => 'TEXTAREA',
	 ),
);