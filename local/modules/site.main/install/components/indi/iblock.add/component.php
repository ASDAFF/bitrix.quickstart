<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use \Site\Main as Main;
if(!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("CC_BIEAF_IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}
if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;


$cacheID = SITE_ID."|".$componentName."|".md5(serialize($arCacheParams))."|".$USER->GetGroups();	
$cachePath = "/".SITE_ID.CComponentEngine::MakeComponentPath($componentName);

$cache = new Main\Cache($cacheID, $cachePath, $arParams["CACHE_TIME"]);
if ($cache->start()) {
	$arResult = array();

	//Получаем все свойства инфоблока
	$rsIBLockPropertyList = CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arParams["IBLOCK_ID"]));
	while ($arProperty = $rsIBLockPropertyList->GetNext())
	{
		$arPropertyList[$arProperty["CODE"]] = $arProperty;
	}
	//Получаем все поля инфоблока
	$arFieldsList = CIBlock::GetFields($arParams["IBLOCK_ID"]);

	//Анализируем все переданные поля
	foreach ($arParams["FIELDS"] as &$arProperty){
		if($arProperty == ""){break;}
		$arPropertyElement = array();

		//Задаем тип свойства  для пользовательских полей
		if($arPropertyList[$arProperty]){

			//Для свойства типа список получаем все возможные значения
			if($arPropertyList[$arProperty]['PROPERTY_TYPE'] == "L"){
				$propEnumList = CIBlockProperty::GetPropertyEnum($arPropertyList[$arProperty]['ID'],Array("SORT"=>"asc")); 
				while($arEnumList = $propEnumList->GetNext())
				{
					$arPropertyElement["VALUES"][] = $arEnumList;
				} 
			}

			$arPropertyElement["TYPE"] = $arPropertyList[$arProperty]["PROPERTY_TYPE"];
			//Если это телефон или email
			if($arPropertyList[$arProperty]["CODE"] == "EMAIL"){
				$arPropertyElement["TYPE"] = "EMAIL";
			}
			if($arPropertyList[$arProperty]["CODE"] == "PHONE"){
				$arPropertyElement["TYPE"] = "PHONE";
			}
		}

		//Задаем тип свойства для обычных полей
		else{
			if($arProperty == "DETAIL_TEXT" ||$arProperty == "PREVIEW_TEXT"){
				$arPropertyElement["TYPE"] = "T";
			}
			elseif($arProperty == "DETAIL_PICTURE" ||$arProperty == "PREVIEW_PICTURE"){
				$arPropertyElement["TYPE"] = "F";
			}
			else{
				$arPropertyElement["TYPE"] = "S";
			}
		}
		//Передаем обязательность
		if(in_array($arProperty,$arParams["REQUARED_FIELDS"])){
			$arPropertyElement["REQUARED"] = 1;
		}

		//Задаем названия для полей
		//Если для поля задано кастомное название в параметрах компонента
		if($arParams["CUSTOM_LABELS_".$arProperty]){
			$arPropertyElement["NAME"] = $arParams["CUSTOM_LABELS_".$arProperty]; 
		}
		else{
			//Задаем названия для пользовательских полей
			if($arPropertyList[$arProperty]){
				$arPropertyElement["NAME"] = $arPropertyList[$arProperty]["NAME"];  
			}
			//Задаем названия для обычных полей
			else{
				$arPropertyElement["NAME"] = $arFieldsList[$arProperty]["NAME"]; 
			}
		}

		//Записываем свойство в итоговый массив
		$arResult["PROPERTY_LIST"][$arProperty] = $arPropertyElement;
	}


	$cache->end($arResult);
} else {
	$arResult = $cache->getVars();
}

//Если форма было отправлена
if($_POST["submit-form"]){

	//Проверяем ошибки
	$arResult["ERRORS"] = array();
	$arErrors =  array();
	foreach($arParams["REQUARED_FIELDS"] as $reqKey => $req){
		if(!$_REQUEST[$req] && $req!=""){
			$arResult["PROPERTY_LIST"][$req]["ERRROR"] = "Y";
			$arErrors[] = $reqKey;

		}	
	}
	$arResult["ERRORS"] = $arErrors; 

	//Если есть ошибки заполняем поля
	if(!empty($arErrors)){
		foreach($arResult["PROPERTY_LIST"] as $propertyKey => &$property){
			$property["VALUE"] = $_REQUEST[$propertyKey];
		} 
	}

	//если нет ошибок
	else{
		//Добавляем в инфоблок
		$el = new CIBlockElement;

		//Запоняем свойства
		$PROP = array();
		foreach($arPropertyList as $propertykey => $arProperty){
			$PROP[$propertykey] = $_REQUEST[$propertykey];
		}

		//Формируем имя элемента
		$countNameParts = 0;
		foreach($arParams["NAME_FORMAT"] as $namePart){
			if($namePart != ""){
				if($countNameParts == 0) {
					$name = $_REQUEST[$namePart];
				}
				else{
					$name = $name."-".$_REQUEST[$namePart];
				}

				$countNameParts++;
			}
		}
		if(!$name){
			$name = "Новый элемент";
		}
		if($arParams["USE_DATE_IN_NAME"] == "Y"){
			$name = $name."-".date("d.m.Y");
		}
		//Запоняем обычные свойства
		$arLoadProductArray = Array(
			"IBLOCK_SECTION_ID" => false, 
			"IBLOCK_ID"      => $arParams["IBLOCK_ID"],
			"PROPERTY_VALUES"=> $PROP,
			"NAME"           => $name,
			"ACTIVE"         => $arParams["ACTIVATE_ELEMENT"],
			"DETAIL_TEXT"    => $_REQUEST["DETAIL_TEXT"],
			"PREVIEW_TEXT"    => $_REQUEST["PREVIEW_TEXT"],
			"PREVIEW_PICTURE"    => $_REQUEST["PREVIEW_PICTURE"],
			"PREVIEW_PICTURE"    => $_REQUEST["PREVIEW_PICTURE"],
		);

		if($PRODUCT_ID = $el->Add($arLoadProductArray)){
			//Отправляем уведомление администратору  -  новый тип почтового события - NEW_ELEMENT!!!

			//Формируем ссылку на добавленный элемент
			$link = "http://".SITE_SERVER_NAME."/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=".$arParams["IBLOCK_ID"]."&type=".$arParams["IBLOCK_TYPE"]."&ID=".$PRODUCT_ID."&lang=ru&find_section_section=-1&WF=Y";

			$arEventFields = $_REQUEST;
			$arEventFields["ELEMENT"] = $name;
			$arEventFields["LINK"] = $link;
			CEvent::Send("NEW_ELEMENT","s1", $arEventFields);
			//Выдаем нотификацию
			$arResult["OK"] = "Y";
			
			LocalRedirect(
				$_SERVER['PHP_SELF'] 
				."?formresult=".$arResult["OK"]
			);
		}
		else{
			$arResult["ERRORS"] = $el->LAST_ERROR;
		}
	}

} 
$this->IncludeComponentTemplate();



?>
