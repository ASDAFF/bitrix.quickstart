<?php
class CUserIblockPropValueType extends CUserTypeString
{
   function GetUserTypeDescription()
   {
      return array(
         "USER_TYPE_ID" => "c_string",
         "CLASS_NAME" => "CUserIblockPropValueType",
         "DESCRIPTION" => "Привязка к значению свойства инфоблока",
         "BASE_TYPE" => "string",
      );
   }
   
   function GetAdminListViewHTML($arUserField, $arHtmlControl)
   {
      if(strlen($arHtmlControl["VALUE"])>0)
         return $arHtmlControl["VALUE"];
      else
         return ' ';
   }
      
	function GetEditFormHTML($arUserField, $arHtmlControl)
	{
	    $return = '';
	    if(CModule::IncludeModule("iblock") )
	    {			
			$resdb = CIBlockPropertyEnum::GetList(
								 Array("SORT"=>"ASC", "VALUE"=>"ASC"),
								 Array( "ACTIVE" => "Y","IBLOCK_ID" => IBLOCK_ID_CATALOG, 'PROPERTY_ID'=>PROPERTY_ID_TORGOVAYA_MARKA )
								);
			$arPropertyListValue = array();
			while ( $res = $resdb->fetch() ) 
			{
				$arPropertyListValue[ $res['ID'] ] = $res['VALUE'];	
			}			
			
	        if(count($arPropertyListValue)>0):
	        ob_start();?>
	            <select name="<?=$arUserField["FIELD_NAME"]?>">
	                <option value=""></option>
	                <?foreach($arPropertyListValue as $ID => $NAME):?>
	                    <option value="<?=$ID?>" <?if($ID == $arUserField["VALUE"])echo 'selected';?>><?=$NAME?></option>
	                <?  endforeach;?>
	            </select>
	        <?$return = ob_get_contents();
	        ob_end_clean();
	        endif;
	    }
	    return $return;
	}   
}
AddEventHandler("main", "OnUserTypeBuildList", array("CUserIblockPropValueType", "GetUserTypeDescription"));
?>
