<?
##############################################
# Askaron.Ibvote module                      #
# Copyright (c) 2011 Askaron Systems         #
# http://askaron.ru                          #
# mailto:mail@askaron.ru                     #
##############################################


global $DB, $MESS, $APPLICATION;

//require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/filter_tools.php");

CModule::AddAutoloadClasses("askaron.ibvote", array(
	"CAskaronIbvoteEvent" => "classes/".strtolower($DB->type)."/event.php"
));
?>