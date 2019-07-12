<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
if(WIZARD_IS_RERUN || !CModule::IncludeModule("iblock"))
	return;

$arrUserFields=array(
	
);
$obUserField  = new CUserTypeEntity;
$tmpIblock=array();
foreach($arrUserFields as $userField)
{
	if($userField['SETTINGS']['IBLOCK_ID'])
	{
		$matches=array();
		if(!$tmpIblock[$userField['SETTINGS']['IBLOCK_CODE']])
		{
			$res = CIBlock::GetList(
				Array(), 
				Array(
					'TYPE'=>$userField['SETTINGS']['IBLOCK_TYPE_ID'], 					
					"CODE"=>$userField['SETTINGS']['IBLOCK_CODE']
				), false
			);
			if($ar_res = $res->Fetch())
			{
				$tmpIblock[$userField['SETTINGS']['IBLOCK_CODE']]=$ar_res['ID'];
			}			
		}
		$userField['SETTINGS']['IBLOCK_ID']=$tmpIblock[$userField['SETTINGS']['IBLOCK_CODE']];
	}
	if(preg_match("/IBLOCK_([0-9]+)/", $userField['ENTITY_ID'], $matches) && $userField['IBLOCK_CODE']) 
	{	
		if(!$tmpIblock[$userField['IBLOCK_CODE']])
		{	
			$res = CIBlock::GetList(
				Array(), 
				Array(
					'TYPE'=>$userField['IBLOCK_TYPE_ID'], 					
					"CODE"=>$userField['IBLOCK_CODE']
				), false
			);
			if($ar_res = $res->Fetch())
			{
				$tmpIblock[$userField['IBLOCK_CODE']]=$ar_res['ID'];				
			}
		}
		$userField['ENTITY_ID']=str_replace($matches[1], $tmpIblock[$userField['IBLOCK_CODE']], $userField['ENTITY_ID']);				
	}
		
	$resFields=CUserTypeEntity::GetList(array($by=>$order), array('ENTITY_ID' => $userField['ENTITY_ID'], "FIELD_NAME"=>$userField['FIELD_NAME']));
	if(!$arRes = $resFields->GetNext())
	{
		$ID = $obUserField->Add($userField);
		if($ID && $userField['USER_TYPE_ID']=='enumeration')
			$res = $obEnum->SetEnumValues($ID, $userField['SETTINGS']['LIST']);			
	}
}
?>