<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $APPLICATION;

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$module_id = 'wsm.callback';

if(!CModule::IncludeModule($module_id))
{
	ShowError(GetMessage("WSM_CALLBACK_MODULE_NOT_INSTALLED"));
	return;
}

if(!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}

$SITE_ID = SITE_ID;

$arParams["IBLOCK_ID"] = COption::GetOptionInt($module_id, 'iblock', 0, $SITE_ID);
$arParams["PROPERTY_TIME"] = COption::GetOptionInt($module_id, 'iblock_property_time', 0, $SITE_ID);
$arParams["PROPERTY_THEME"] = COption::GetOptionInt($module_id, 'iblock_property_theme', 0, $SITE_ID);
$arParams["FORM_PROPERTY"] = explode(',',COption::GetOptionString($module_id, 'form_property', '', $SITE_ID));
$arParams["FORM_CAPTCHA"] = COption::GetOptionString($module_id, 'form_captcha', 'N', $SITE_ID);
$arParams["FORM_CAPTCHA"] = $arParams["FORM_CAPTCHA"] == 'Y' ? 'Y' : 'N' ;

if(!is_array($arParams["FORM_PROPERTY"]))
	$arParams["FORM_PROPERTY"] = array();
foreach($arParams["FORM_PROPERTY"] as $key=>$val)
	if($val==="")
		unset($arParams["FORM_PROPERTY"][$key]);

		
if($this->StartResultCache(false, array(SITE_ID)))
{
	$rsIBlock = CIBlock::GetList(array(), array(
		"ACTIVE" => "Y",
		"ID" => $arParams["IBLOCK_ID"],
		));

	if($arIBlock = $rsIBlock->GetNext())
	{
		$arResult["FORM_PROPERTY"]["NAME"] = array(
			'ID' => 'NAME',
			'CODE' => 'NAME',
			'NAME' => GetMessage("T_CALLBAK_FIELDS_NAME"), 
			'FIELD' => '<input type="text" name="CALLBACK[NAME]" id="wsm_callback_NAME" placeholder="'.GetMessage("T_CALLBAK_FIELDS_HINT").'"/>',
			'IS_REQUIRED' => 'Y',
			);
		
		//props
		$arFilter = Array(
			'IBLOCK_ID' => $arIBlock["ID"], 
			'ACTIVE' 	=> 'Y',
			);
		
		$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), $arFilter );
		while ($prop = $properties->GetNext())
		{
			if(in_array($prop["PROPERTY_TYPE"], array('L','N','S')) && in_array($prop["ID"], $arParams["FORM_PROPERTY"]) && count($arParams["FORM_PROPERTY"]))
			{
				$name = 'CALLBACK['.$prop['ID'].']';
				$field = '';
				$id = 'wsm_callback_'.$prop['ID'];
				
				switch($prop["PROPERTY_TYPE"])
				{
					case 'S':
					case 'N':
						if($arParams["PROPERTY_TIME"] == $prop["ID"])
						{
							$name = 'CALLBACK['.$prop['ID'];

							$field = array(
								'<input type="text" name="'.$name.'_FROM]" id="'.$id.'"  placeholder="'.$prop['HINT'].'"/>',
								'<input type="text" name="'.$name.'_TO]" id="'.$id.'_to" placeholder="'.$prop['HINT'].'"/>',
								);
						}	
						else
						{
							$field = '<input type="text" name="'.$name.'" id="'.$id.'" placeholder="'.$prop['HINT'].'"/>';
						}					
					break;	
					case 'L':	
						$field = '<select name="'.$name.'" id="'.$id.'">';
						$property_enums = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>$arIBlock["ID"], "PROPERTY_ID"=>$prop_fields["ID"]));
						while($enum_fields = $property_enums->GetNext())
						{
							$field .= '<option value="'.$enum_fields['ID'].'">'.$enum_fields['VALUE'].'</option>';
						}	
						$field .= '</select>';
					break;	
				}
				
				$arResult["FORM_PROPERTY"][$prop["ID"]] = array(
					'NAME' => $prop["NAME"],
					'ID' => $prop["ID"],
					'CODE' => $prop["CODE"],
					'FIELD' => $field,
					'IS_REQUIRED' => $prop['IS_REQUIRED'],
					);
			}
		}
		
		if($arParams["FORM_CAPTCHA"] == 'Y')
			$arResult["CAPTCHA_CODE"] = htmlspecialchars($APPLICATION->CaptchaGetCode());

		$this->IncludeComponentTemplate();
	}
	else
	{
		ShowError(GetMessage("T_MODUL_NOT_SETTING"));
	}
}
?>