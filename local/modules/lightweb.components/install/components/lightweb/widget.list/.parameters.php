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

			"FIELD_SORT" => array(
				"PARENT" => "PRINT",
				"NAME" => GetMessage("LW_WIDGET_PRM_FIELD_SORT"),
				"TYPE" => "LIST",
				"ADDITIONAL_VALUES" => "N",
				"VALUES" => array(
					'id'=>GetMessage("LW_WIDGET_PRM_FIELD_SORT_ID"),
					'name'=>GetMessage("LW_WIDGET_PRM_FIELD_SORT_NAME"),
					'sort'=>GetMessage("LW_WIDGET_PRM_FIELD_SORT_SORT"),
					'active_from'=>GetMessage("LW_WIDGET_PRM_FIELD_SORT_ACTIVE_FROM"),
					'timestamp_x'=>GetMessage("LW_WIDGET_PRM_FIELD_SORT_TIMESTAMP_X")
				),
				"REFRESH" => "N",
				"SORT"=>"10"
			),
			
			"SORT_ORDER" => array(
				"PARENT" => "PRINT",
				"NAME" => GetMessage("LW_WIDGET_PRM_SORT_ORDER"),
				"TYPE" => "LIST",
				"ADDITIONAL_VALUES" => "N",
				"VALUES" => array(
					'asc'=>GetMessage("LW_WIDGET_PRM_SORT_ORDER_ASC"),
					'desc'=>GetMessage("LW_WIDGET_PRM_SORT_ORDER_DESC"),
				),
				"REFRESH" => "N",
				"SORT"=>"20"
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
	
	//Если указан тип ИБ
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
	

?>