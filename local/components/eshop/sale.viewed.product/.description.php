<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arComponentDescription = array(
   "NAME" => GetMessage("NAME"),
   "DESCRIPTION" => GetMessage("DESCRIPTION"),
   "ICON" => "/images/view.gif",
   "PATH" => array(
       "ID" => "development",
       "NAME" => "DEVELOPMENT",
      "CHILD" => array(
         "ID" => "view",
         "NAME" => GetMessage("DESCRIPTION")
      )
   )
);
?>