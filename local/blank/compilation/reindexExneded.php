<?php 
AddEventHandler("search", "BeforeIndex", "BeforeIndexHandler");
function BeforeIndexHandler($arFields)
{
   if(!CModule::IncludeModule("iblock")) 
      return $arFields;
   if($arFields["MODULE_ID"] == "iblock")
   {
   	  if( $arFields["PARAM2"] == IBLOCK_ID_CATALOG )
	  {
	  		 $db_props = CIBlockElement::GetProperty(                       
		                                   	$arFields["PARAM2"],         
		                                    $arFields["ITEM_ID"],          
		                                    array("sort" => "asc"),      
		                                    Array("CODE"=>"CML2_ARTICLE")); 
		      if($ar_props = $db_props->Fetch())
		         $arFields["TITLE"] .= " @".$ar_props["VALUE"];   
		         
		      
		      $db_props2 = CIBlockElement::GetProperty(                      
		                                    $arFields["PARAM2"],         
		                                    $arFields["ITEM_ID"],          
		                                    array("sort" => "asc"),       
		                                    Array("CODE"=>"KOD2")); 
		      if($ar_props2 = $db_props2->Fetch())
		         $arFields["TITLE"] .= " @".$ar_props2["VALUE"]; 	
			  }	 
   }
   
   return $arFields; 
}
?>
