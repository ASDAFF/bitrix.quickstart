<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arComponentDescription = array(
   "NAME" => GetMessage("NAME"),
   "DESCRIPTION" => GetMessage("DESCRIPTION"),
   "ICON" => "/images/personal.gif",
   "PATH" => array(
      "ID" => "ELECTRO",
      "CHILD" => array(
         "ID" => "personal",
         "NAME" => GetMessage("DESCRIPTION")
      )
   )
);
?>