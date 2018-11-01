<?php
class MyCurledType extends CUserTypeString
{
   function GetUserTypeDescription()
   {
      return array(
         "USER_TYPE_ID" => "c_string",
         "CLASS_NAME" => "MyCurledType",
         "DESCRIPTION" => "Строка в фигурных скобках",
         "BASE_TYPE" => "string",
      );
   }
//Этот метод вызывается для показа значений в списке
   function GetAdminListViewHTML($arUserField, $arHtmlControl)
   {
      if(strlen($arHtmlControl["VALUE"])>0)
         return "{".$arHtmlControl["VALUE"]."}";
      else
         return ' ';
   }
}
AddEventHandler("main", "OnUserTypeBuildList", array("MyCurledType", "GetUserTypeDescription"));
?>
