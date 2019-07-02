<?
$arComponentDescription = array(
   "NAME" => GetMessage("COMPONENT_NAME"),
   "DESCRIPTION" => GetMessage("COMPONENT_DESC"),
   "ICON" => "/images/icon.gif",
   "PATH" => array(
      "ID" => "rk_soft",
	  "NAME" => GetMessage("SECTION_COMPONENT_NAME"),
      "CHILD" => array(
         "ID" => "shop.select_city",
         "NAME" => GetMessage("SUB_SECTION_COMPONENT_NAME")
      )
   ),
   "AREA_BUTTONS" => array(
      array(
         "URL" => "",
         "SRC" => "",
         "TITLE" => ""
      ),
   ),
   "CACHE_PATH" => "Y",
   "COMPLEX" => "N"
);
?>