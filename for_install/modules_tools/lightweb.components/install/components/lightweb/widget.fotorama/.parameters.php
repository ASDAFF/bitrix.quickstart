<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
	
	if (!CModule::IncludeModule("iblock")){return;}

	//Получаем все активные типы ИБ
	$dbIBlockType = CIBlockType::GetList(
	   array("sort" => "asc"),
	   array("ACTIVE" => "Y")
	);
	$arIBlockTypeList[0]='--';
	while ($arIBlockType = $dbIBlockType->Fetch()){
		if ($arIBlockTypeLang = CIBlockType::GetByIDLang($arIBlockType["ID"], LANGUAGE_ID)){
			$arIBlockTypeList[$arIBlockType["ID"]] = "[".$arIBlockType["ID"]."] ".$arIBlockTypeLang["NAME"];
		}
	}
	
	

	$arComponentParameters = array(
	   "GROUPS" => array(
	      
		  "OPTION" => array(
	         "NAME" => GetMessage("LW_WIDGET_GROUP_PRM_OPTION"),
	         "SORT"	=> "240"
	      ),
	      
	      "PRINT" => array(
	         "NAME" => GetMessage("LW_WIDGET_GROUP_PRM_PRINT"),
	         "SORT"	=> "250"
	      ),
	   ),
		"PARAMETERS" => array(
			"IBLOCK_TYPE_ID" => array(
				"PARENT" => "DATA_SOURCE",
				"NAME" => GetMessage("LW_WIDGET_PRM_IBLOCK_TYPE"),
				"TYPE" => "LIST",
				"ADDITIONAL_VALUES" => "N",
				"VALUES" => $arIBlockTypeList,
				"REFRESH" => "Y",
				"SORT"=>"10"
			),
			
			"WIDTH" => array(
				"PARENT" => "OPTION",
				"NAME" => GetMessage("LW_WIDGET_PRM_OPTION_WIDTH"),
				"TYPE" => "STRING",
				"DEFAULT" => "100%",
				"REFRESH" => "N",
				"SORT"=>"10"
			),
			
			"HEIGHT" => array(
				"PARENT" => "OPTION",
				"NAME" => GetMessage("LW_WIDGET_PRM_OPTION_HEIGHT"),
				"TYPE" => "STRING",
				"DEFAULT" => "400",
				"REFRESH" => "N",
				"SORT"=>"20"
			),
			
			"THUMB_WIDTH" => array(
				"PARENT" => "OPTION",
				"NAME" => GetMessage("LW_WIDGET_PRM_OPTION_THUMB_WIDTH"),
				"TYPE" => "STRING",
				"DEFAULT" => "137",
				"REFRESH" => "N",
				"SORT"=>"30"
			),
			
			"THUMB_HEIGHT" => array(
				"PARENT" => "OPTION",
				"NAME" => GetMessage("LW_WIDGET_PRM_OPTION_THUMB_HEIGHT"),
				"TYPE" => "STRING",
				"DEFAULT" => "77",
				"REFRESH" => "N",
				"SORT"=>"40"
			),
			
			"ELEMENT_COUNT" => array(
				"PARENT" => "PRINT",
				"NAME" => GetMessage("LW_WIDGET_PRM_ELEMENT_COUNT"),
				"TYPE" => "STRING",
				"DEFAULT" => "6",
				"REFRESH" => "N",
				"SORT"=>"30"
			),
			
		)
	);
	
	
	//Обработка событий выбора данных
	
	//Если указан тип ИБ получаем список ИБ заданного типа
	if ($arCurrentValues["IBLOCK_TYPE_ID"]){
		$dbIBlockID = CIBlock::GetList(
			array("sort" => "asc"),
			array("ACTIVE" => "Y", "TYPE"=>$arCurrentValues["IBLOCK_TYPE_ID"])
		);
		$arIBlockIDList[0]='--';
		while($arIBlockID = $dbIBlockID->Fetch()){
			$arIBlockIDList[$arIBlockID["ID"]] = "[".$arIBlockID["ID"]."] ".$arIBlockID["NAME"];
		}
				
		$arComponentParameters["PARAMETERS"]["IBLOCK_ID"]=array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("LW_WIDGET_PRM_IBLOCK_ID"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => $arIBlockIDList,
			"REFRESH" => "Y",
			"SORT"=>"20"
		);
	}

	//Если указан ИБ получаем список элементов
	if ($arCurrentValues["IBLOCK_ID"]){
		
		$resElements = CIBlockElement::GetList(array("sort"=>"asc", "name"=>"asc"), array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"], "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y"), false, array("nPageSize"=>9999), array("ID", "IBLOCK_ID", "NAME"));
		
		$arFields[0]='--';
		while($obElements = $resElements->GetNextElement()){ 
			$arField = $obElements->GetFields();
			$arFields[$arField['ID']]=$arField['NAME'];
		}
		
		$arComponentParameters["PARAMETERS"]["ELEMENT_ID"]=array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("LW_WIDGET_PRM_ELEMENT_ID"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => $arFields,
			"REFRESH" => "Y",
			"SORT"=>"30"
		);
		
	}
	
	//Если указан ИБ и элемен, получаем список его свойств типа файл
	if ($arCurrentValues["IBLOCK_ID"] and $arCurrentValues["ELEMENT_ID"]){
		
		$resProperties = CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"]));
		
		$arPropFields[0]='--';
		while ($arPropField = $resProperties->GetNext()){
			
			if ($arPropField['PROPERTY_TYPE']=='F'){ //Если сфойство типа "Файл"
				if (!empty($arPropField['FILE_TYPE'])){
					$arPropField['FILE_TYPE']=explode(',', str_replace(' ', '', $arPropField['FILE_TYPE']));	
					$arPropField['FILE_TYPE']=array_diff($arPropField['FILE_TYPE'],array('jpeg','jpg','png')); //Если указанны разрешенные форматы
					if (empty($arPropField['FILE_TYPE'])){
						$arPropFields[$arPropField["ID"]]=$arPropField["NAME"];
					}
				}
			}
		}
		
		$arComponentParameters["PARAMETERS"]["PROP_ID"]=array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("LW_WIDGET_PRM_PROP_ID"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => $arPropFields,
			"REFRESH" => "Y",
			"SORT"=>"40"
		);

	}

?>