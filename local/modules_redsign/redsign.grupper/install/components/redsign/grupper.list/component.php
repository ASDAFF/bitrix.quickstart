<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule('redsign.grupper'))
{
	ShowError( GetMessage("ALFA_MSG_ERROR_NO_MODULE") );
	return;
}

if(IntVal($arParams["CACHE_TIME"])<1)
	$arParams["CACHE_TIME"] = 3600000;

if( $this->StartResultCache() )
{
	$arGroups = array();
	
	$rsGroups = CRSGGroups::GetList(array("SORT"=>"ASC","ID"=>"ASC"),array());
	while($arGroup = $rsGroups->Fetch())
	{
		$arGroups[$arGroup["ID"]]["GROUP"] = $arGroup;
		$rsBinds = CRSGBinds::GetList(array("ID"=>"ASC"),array("GROUP_ID"=>$arGroup["ID"]));
		while($arBind = $rsBinds->Fetch())
		{
			$arGroups[$arGroup["ID"]]["BINDS"][] = $arBind["IBLOCK_PROPERTY_ID"];
		}
	}
	
	$arGroupedProps = array();
	$arGroupedPropsID = array();
	$i = 0;
	foreach($arGroups as $groupID => $groupData)
	{
		$arGroupedProps[$i]["GROUP"] = $groupData["GROUP"];
		if(is_array($groupData['BINDS'])){
			foreach($arParams["DISPLAY_PROPERTIES"] as $CODE => $property){
				if(in_array($property["ID"], $groupData["BINDS"])){
					$arGroupedProps[$i]["PROPERTIES"][] = $property;
					$arGroupedPropsID[] = $property["ID"];
				}
			}
		}
		$i++;
		
	}

	$arNotGroupedProps = array();
	foreach($arParams["DISPLAY_PROPERTIES"] as $CODE => $property)
	{
		if(!in_array($property["ID"],$arGroupedPropsID))
			$arNotGroupedProps[] = $property;
	}
	
	$arResult["GROUPED_ITEMS"] = $arGroupedProps;
	$arResult["NOT_GROUPED_ITEMS"] = $arNotGroupedProps;

	$this->IncludeComponentTemplate();
}
?>