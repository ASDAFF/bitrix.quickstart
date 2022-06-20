<?
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;
	
$arComponentParameters = array(
   "GROUPS" => array(
	  "GROUP1" => array(
		 "NAME" => GetMessage("GROUP1_NAME")
	  ),
	  "GROUP2" => array(
		 "NAME" => GetMessage("GROUP2_NAME")
	  ),
   ),
   "PARAMETERS" => array(
	  "PARAM1" => array(
		 "PARENT" => "GROUP1",
		 "NAME" => GetMessage("PARAM1_NAME"),
		 "TYPE" => "STRING",
		 "DEFAULT" => '',
	  ),
	  "PARAM2" => array(
		 "PARENT" => "GROUP2",
		 "NAME" => GetMessage("PARAM2_NAME"),
		 "TYPE" => "LIST",
		 "VALUES" => array('1' => '10', '2' => '50', '3' => '100'),
		 "DEFAULT" => '1',
	  ),
	  
	  "CACHE_TIME" => array(),
   )
);
	
?>