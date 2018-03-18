<?

AddEventHandler("iblock", "OnStartIBlockElementAdd", array('MyHandlers', 'OnStartIBlockElementAdd'));
AddEventHandler("iblock", "OnAfterIBlockElementAdd", array('MyHandlers', 'SendNewMessage'));
class MyHandlers{	
	
	private static $properties=array();
	
	function OnStartIBlockElementAdd(&$arFields){
//		AddMessage2Log("<pre>\n\$arFields=".var_export($arFields, true)."</pre>", "OnStartIBlockElementAdd");	
		CModule::IncludeModule('iblock');
		$res = CIBlock::GetByID($arFields["IBLOCK_ID"]);		
		if($ar_res = $res->GetNext()){	 
			if($ar_res['CODE']!='discount_coupon') return true;
						
			self::$properties=array();
			$res_prop = CIBlock::GetProperties($arFields["IBLOCK_ID"]);		
			while($property = $res_prop->GetNext()){	 
				self::$properties[$property['CODE']]=$property['ID'];
			}										
	        $arFields['NAME']=implode(' ', 
	        	array($arFields['PROPERTY_VALUES'][self::$properties['last_name']], $arFields['PROPERTY_VALUES'][self::$properties['name']]));
	        $arFields['PROPERTY_VALUES'][self::$properties['code']]='1001'; 
	        $arFields['PROPERTY_VALUES'][self::$properties['status']]='1';
	        $arFields['PROPERTY_VALUES'][self::$properties['date_status_chang']]=ConvertTimeStamp(); 		        
		}
	}
	
	function SendNewMessage(&$arFields){
//		AddMessage2Log("<pre>\n\$arFields=".var_export($arFields, true)."</pre>", "SendNewMessage");		
		CModule::IncludeModule('iblock');
		$res = CIBlockElement::GetByID($arFields["ID"]);		
		if($ar_res = $res->GetNext()){		
			if($ar_res['IBLOCK_CODE']!='discount_coupon') return true;
			
			$arFields['PROPERTY_VALUES'][self::$properties['code']]='1001'.str_pad($arFields["ID"], 5, "0", STR_PAD_LEFT); 			
			CIBlockElement::SetPropertyValuesEx($arFields["ID"], $ar_res['IBLOCK_ID'], 
				array(self::$properties['code'] => $arFields['PROPERTY_VALUES'][self::$properties['code']]));
							
			$arEventFields=$arFields;
			$arEventFields['LINK_TO_EDIT']='http://'.SITE_SERVER_NAME.'/bitrix/admin/iblock_element_edit.php?ID='.$arFields['ID'].
				'&type=catalogs&lang=ru&IBLOCK_ID='.$arFields["IBLOCK_ID"];
			CEvent::Send("NEW_ELEMENT", SITE_ID, $arEventFields); 			
		}	  
	} 
}
?>