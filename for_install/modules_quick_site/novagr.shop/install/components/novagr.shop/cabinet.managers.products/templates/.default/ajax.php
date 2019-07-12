<?php
/**
 * Обработчик ajax запросов в лк - редактирование продуктов
 * 
 */ 

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
include_once('translit.class.php');

__IncludeLang(dirname(__FILE__)."/lang/ru/ajax.php");

CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");

$uploadsDir = "/upload/folder_for_previews/";
$uploadsDirPath = $_SERVER["DOCUMENT_ROOT"] . $uploadsDir;

if (!file_exists($uploadsDirPath)) {

	mkdir($uploadsDirPath, 0777, true);
} 
global $USER;
if (!isManager()) {
	die(GetMessage("ACCESS_DENIED"));
}

$userID = $USER->GetID();

$siteUTF8 = true;
$rsSites = CSite::GetByID(SITE_ID);
$arSite = $rsSites->Fetch();

if (strtolower($arSite["CHARSET"]) == "windows-1251") {
	$siteUTF8 = false;
	// конвертим реквест чтоб не было кракозябр
	foreach ($_REQUEST as $key => $item) {

		if (is_array($item)) {
			foreach ($item as $k => $value) {
				if (is_array($value)) {
					foreach ($value as $k2 => $value2) {
						if (is_array($value2)) {
						
							
							
						} else {
							if (!empty($_REQUEST[$key][$k][$k2])) $_REQUEST[$key][$k][$k2] = iconv('UTF-8', 'windows-1251', $_REQUEST[$key][$k][$k2]);
						}
							
					}	
				} else {
					
					if (!empty($_REQUEST[$key][$k])) $_REQUEST[$key][$k] = iconv('UTF-8', 'windows-1251', $_REQUEST[$key][$k]);
				}	
					
			}
		} else {
			if (!empty($_REQUEST[$key])) $_REQUEST[$key] = iconv('UTF-8', 'windows-1251', $_REQUEST[$key]);
		}
	}
	
}


$addJson = false;

// определяем папку для записи картинок для текущего юзера

if (isset($_REQUEST["action"])) $action = $_REQUEST["action"]; // действие обработчика
else $action = false;


if (isset($_REQUEST["iblid"])) $iblockID = (int)$_REQUEST["iblid"];
else $iblockID = false;

// определяем группу дилеров
$filter = Array("STRING_ID" => "sale_administrator");
$rsGroups = CGroup::GetList(($by="c_sort"), ($order="desc"), $filter);

if  ($arGroup = $rsGroups->Fetch()) {
	
	$managerGroupId = $arGroup["ID"];
} else {
	die();
}

if (isset($_REQUEST["product_id"]) && $_REQUEST["product_id"]>0) {
	$productID = (int)$_REQUEST["product_id"];

	// проводим проверку на доступ к данному элементу у текущего пользователя
	//$res = userCanEdit($productID, $USER, $managerGroupId);
	//if ($res == false) die("У вас нет прав на доступ к объекту");

}
else $productID = false;


$result = array();

if (!empty($_REQUEST["name"]) && startsWith($_REQUEST["name"], "add_color") ) {
	// отправляем запрос на добавление цвета
	if (empty($_REQUEST["value"])) {
		$result['result'] = 'ERROR';
		$result['empty_value'] = 1;
	} else {
		$text = GetMessage("ADD_QUERY_COLOR").$_REQUEST["value"];
		$text .= "\n".GetMessage("LABEL_LOGIN").": ".$USER->GetLogin()."\n".GetMessage("LABEL_NAME").": ".$USER->GetFullName();
		// отправляем событие
		$arFields = array(			
				"DATE_ENTER" => date('d.m.Y H:i:s'),
				"TEXT" => $text
		);
	
		$res = CEvent::Send("NEW_VALUE_ADDED", array(SITE_ID), $arFields);
		$result['result'] = 'OK';
		$message = GetMessage("QUERY_OK");
	}
	$result['message'] = $message;
	$addJson = true;
	//$resultJson = json_encode($result);
	//die($resultJson);
}
elseif ($_REQUEST["name"] == "add_material") { // добавляем новый материал
	$result['id'] = '';
	$result['title'] = '';
	$result['empty_value'] = '';

	if (empty($_REQUEST["value"])) {
		$result['result'] = 'ERROR';
		$result['empty_value'] = 1;
		
	} else {
		$text = GetMessage("ADD_QUERY_MATERIAL").$_REQUEST["value"];
		$text .= "\n".GetMessage("LABEL_LOGIN").": ".$USER->GetLogin()."\n".GetMessage("LABEL_NAME").": ".$USER->GetFullName();
		// отправляем событие
		$arFields = array(
				"DATE_ENTER" => date('d.m.Y H:i:s'),
				"TEXT" => $text
		);
		//deb($arFields);
		$res = CEvent::Send("NEW_VALUE_ADDED", array(SITE_ID), $arFields);
		$result['result'] = 'OK';
		$message = GetMessage("QUERY_OK");
		
	}
	
	$result['message'] = $message;
	$addJson = true;
	//$resultJson = json_encode($result);
	//die($resultJson);

}
elseif ($_REQUEST["name"] == "add_brand") { // добавляем новый бренд
	$result['id'] = '';
	$result['title'] = '';
	$result['empty_value'] = '';

	if (empty($_REQUEST["value"])) {
		$result['result'] = 'ERROR';
		$result['empty_value'] = 1;
	
	} else {
		$text = GetMessage("ADD_QUERY_BRAND").$_REQUEST["value"];
		$text .= "\n".GetMessage("LABEL_LOGIN").": ".$USER->GetLogin()."\n".GetMessage("LABEL_NAME").": ".$USER->GetFullName();
		// отправляем событие
		$arFields = array(
				"DATE_ENTER" => date('d.m.Y H:i:s'),
				"TEXT" => $text
		);
		//deb($arFields);
		
		$res = CEvent::Send("NEW_VALUE_ADDED", array(SITE_ID), $arFields);
		$result['result'] = 'OK';
		$message = GetMessage("QUERY_OK");
	
	}
	
	$result['message'] = $message;
	$addJson = true;
	//$resultJson = json_encode($result);
	//die($resultJson);
}

// если $_POST["show_picture"] == 1 , то отдаем тип и путь к закачанному файлу
// нужно для отображения превью картинки
//  если $_POST["show_picture"] == 0 - то обрабатываем отправку формы полностью - редактируем товар
if (isset($_POST["show_picture"]) && $_POST["show_picture"] == '1') {

	$result['only_pic_flag'] = 1; // передаем в js информацию о том что мы только сменили картинку в превью
	// изображение товара
	if (!empty($_FILES["fileInput"]["name"])) {

		$result['result'] = 'ERROR';
		$result['message'] = '';
		$result['name'] = $_FILES["fileInput"]["name"];
		$result['type'] = $_FILES["fileInput"]["type"];
		$result['tmp_name'] = $_FILES["fileInput"]["tmp_name"];
		$result['size'] = $_FILES["fileInput"]["size"];

		$picExtension = '';
		switch($result['type']) {
			case "image/gif":
				$picExtension = "gif";
				break;
			case "image/png":
			case "image/x-png";
				$picExtension = "png";
				break;
			case "image/jpeg";
			case "image/pjpeg";			
			$picExtension = "jpg";
			break;
			default:
				$result['result'] = 'ERROR';
				$result['message'] = GetMessage("PICTURES_WARNING");
		}
		
		if ($_REQUEST["name"]) {
			$result['name'] = Translit::UrlTranslit($_REQUEST["name"]);
			$result['name'] .= mktime()."." . $picExtension;
		
		}
		
		if ($result['size'] > 5000000) {
			$result['result'] = 'ERROR';
			$result['message'] = GetMessage("PICTURES_SIZE_WARNING");

		}

		if ($result['message'] == '') {
				
			// если файл имеет расширение как картинка, и он допустимого размера - то сохраняем его в папку и возвращаем имя				
			$tmp_name = $result['tmp_name'];
			$uploadName = $uploadsDir . $result['name'];
			move_uploaded_file($tmp_name, $_SERVER["DOCUMENT_ROOT"] . $uploadName);
			$result['tmp_name'] = $uploadName;
			$result['result'] = 'OK';
			$result['message'] = GetMessage("IMG_UPLOADED_OK");
		
		}

	} else {

		$result['result'] = 'FILE_EMPTY';
		$result['message'] = GetMessage("IMG_NOT_CHOSED");
	}
	if ($siteUTF8 == false) {
		$result = prepareResultJson($result);
	}
	$resultJson = json_encode($result);
	die($resultJson);
}

if ($action == 'change_active' && $productID >0 && !empty($_REQUEST["state"])) {
	
	// делаем товар активным/неактивным
	if ($_REQUEST["state"] == 'true') {
		$ACTIVE = "Y";
	} else {
		$ACTIVE = "N";
	}
	
	$el = new CIBlockElement;
	
	$arLoad = Array(
		"MODIFIED_BY"    => $userID, // элемент изменен текущим пользователем
		"ACTIVE" => $ACTIVE
	);
	
	$res = $el->Update($productID, $arLoad);	

	if ($res) {
		$result['result'] = 'OK';
		$result['message'] = GetMessage("EDIT_OK");
	} else {
		$result['result'] = 'ERROR';
		$result['message'] = GetMessage("EDIT_NOT_OK");
	}
	
	$result['state'] = $ACTIVE;	
	$addJson = true;
	//$resultJson = json_encode($result);
	//die($resultJson);
			
} elseif (
		($action == "get_edit_form" && $productID>0 && $iblockID>0)
		OR
		($action == "get_add_form" && $iblockID>0)) {
	// форма добавления/редактирования товара
		
	include('form.php');
	die('');
} elseif ($iblockID > 0 && 
		(($action == 'edit_product' && $productID > 0) || $action == 'add_product' )
		) {

	$errors = array();
	
	// получаем названия свойств обмеров
	$realSize = array();
	foreach ($_REQUEST as $key => $value) {
		if (startsWith($key, "REAL_")) {
			$realSize[] = $key;
		}
	}
	
	//обрабатываем запрос на редактирование товара
	$el = new CIBlockElement;
	$arLoad = array();
	$PROP = array();
	// получаем
	// Основной материал
	if (!empty($_REQUEST["material_id"])) $PROP["MATERIAL"] = intval($_REQUEST["material_id"]);
	
	// Бренд
	if (!empty($_REQUEST["brand_id"])) $PROP["VENDOR"] = intval($_REQUEST["brand_id"]);

	if (!empty($_REQUEST["TITLE"])) $PROP["TITLE"] = $_REQUEST["TITLE"];
	if (!empty($_REQUEST["HEADER1"])) $PROP["HEADER1"] = $_REQUEST["HEADER1"];
	if (!empty($_REQUEST["KEYWORDS"])) $PROP["KEYWORDS"] = $_REQUEST["KEYWORDS"];
	if (!empty($_REQUEST["META_DESCRIPTION"])) $PROP["META_DESCRIPTION"] = $_REQUEST["META_DESCRIPTION"];
	
	// страна производитель
	if (isset($_REQUEST["COUNTRY"]) && $_REQUEST["COUNTRY"] !=-1) $PROP["COUNTRY"] = intval($_REQUEST["COUNTRY"]);	
	
	if (!empty($_REQUEST["samples"])) $PROP["SAMPLES"] = $_REQUEST["samples"];
	else {
		$errors[] = GetMessage("SAMPLE_NOT_FILLED");
	} 
	
	// Материал - описание
	if (isset($_REQUEST["MATERIAL_DESC"])) $PROP["MATERIAL_DESC"] = array('VALUE'=>array('TYPE'=>'HTML', 'TEXT'=>$_REQUEST["MATERIAL_DESC"]));
	
	if (isset($_REQUEST["section"])) $IBLOCK_SECTION = intval($_REQUEST["section"]);

	$arLoad ["MODIFIED_BY"] = $USER->GetID(); // элемент изменен текущим пользователем
	$arLoad ["NAME"] = $_REQUEST["name"];
	if ($_REQUEST["name"] && $action == 'add_product') {
		$arLoad['CODE'] = Translit::UrlTranslit($_REQUEST["name"]);
	
	}
	
	$price = 0;
	if (isset($_REQUEST["price"])) {
	
		$price = $_REQUEST["price"];
	}	
	
	$arLoad ["IBLOCK_SECTION"] = $IBLOCK_SECTION;
	if (isset($_REQUEST["PREVIEW_TEXT"])) {
		$arLoad ["PREVIEW_TEXT"] = $_REQUEST["PREVIEW_TEXT"];
		$arLoad ["PREVIEW_TEXT_TYPE"] = 'html';
		
	}
	if (isset($_REQUEST["DETAIL_TEXT"])) {
		$arLoad ["DETAIL_TEXT"] = $_REQUEST["DETAIL_TEXT"];
		$arLoad ["DETAIL_TEXT_TYPE"] = 'html';
	}
		
	if (count($errors)>0) {
		
		$result['result'] = 'ERROR';
		$message = GetMessage("ERROR_TEXT_1") .$productID.GetMessage("ERROR_TEXT_2").join("<br>",$errors); 
		
	} else {
	
		// сохраняем товар	
		if ($action == 'add_product') {
			$result['action'] = 'add';
			$arLoad ["ACTIVE"] = "Y";
			$arLoad ["IBLOCK_ID"] = $iblockID;
			
			$arLoad ["PROPERTY_VALUES"] = $PROP;
			//deb($arLoad);
			if ($newElemId = $el->Add($arLoad)) {
				//echo "New ID: ".$newElemId;
				$message = GetMessage("PRODUCT_ADDED");
				$productID = $newElemId;
				$result['result'] = 'OK';
			}
			else {
				$result['result'] = 'ERROR';
				$message =  $el->LAST_ERROR;
			}
			//deb($message);
			$result['zagolFormConfirm'] = GetMessage("PRODUCT_ADDED_1");
			$result['textFormConfirm'] = GetMessage("PRODUCT_ADDED_2");
			
		} else {
			$result['action'] = 'update';
			$res = $el->Update($productID, $arLoad);
			// сохраняем свойства
			$resSaveProp = CIBlockElement::SetPropertyValuesEx($productID, false, $PROP);
			
			$result = array();
			if ($res) {
				$result['result'] = 'OK';
			
				$message = GetMessage("PRODUCT_TEXT_1") .$productID.GetMessage("PRODUCT_TEXT_2");
				$result['zagolFormConfirm'] = GetMessage("PRODUCT_EDIT_OK");
				$result['textFormConfirm'] = GetMessage("PRODUCT_EDIT_OK");
			}
			else {
				$result['result'] = 'ERROR';
				$message = GetMessage("ERROR_TEXT_1") .$productID.GetMessage("ERROR_TEXT_3");
			}	
				
			
		} 
	}
	
	// сохраняем тп для данного товара
	$arFilter = array();
	$arFilter["IBLOCK_CODE"] = "novagr_standard_products_offers";
	$arFilter["PROPERTY_CML2_LINK"] = $productID;

	$arSelect = array("ID", "NAME", "IBLOCK_ID");
	$rsElement = CIBlockElement::GetList(false, $arFilter, false, false, $arSelect );
	
	$offersExists = array(); // тп которые уже существуют - их обновляем
	while ($data = $rsElement -> Fetch()) {

		$offersExists[$data["ID"]] = $data;
	}	
	
	// получаем id для иб торговых предложений		
	$res = CIBlock::GetList(Array(), Array('CODE'=>'novagr_standard_products_offers'), true)->Fetch();
	$offersIblockID =$res["ID"];
	// тп которые обрабатываем
	$offersProcess = array();
	if (is_array($_REQUEST["colorOffer"])) {
		foreach ($_REQUEST["colorOffer"] as $sizeID => $arr) {
			$arAdd = array();
			foreach ($realSize as $realSizeName) {
				if (!empty($_REQUEST[$realSizeName][$sizeID])) {
					$arAdd[$realSizeName] = $_REQUEST[$realSizeName][$sizeID];
				}
			}
			
			foreach ($arr as $offerID => $colorID) {
				$priceOffer = 0;
				$quantity = $_REQUEST["quantityOffer"][$sizeID][$offerID];
				$priceOffer = $_REQUEST["priceOffer"][$sizeID][$offerID];
				if (empty($priceOffer) && !empty($price)) $priceOffer = $price;
				
				// если тп с таким айди существует - обновляем его, иначе - создаем
				if (isset($offersExists[$offerID])) {
					$offersProcess[] = $offerID;
					// сохраняем свойства
					$params = array("COLOR"=>$colorID);
					$params = array_merge($params, $arAdd);
					
					$resSaveProp = CIBlockElement::SetPropertyValuesEx($offerID, false, $params);
					//обновляем остатки
					CCatalogProduct::Update($offerID, array("QUANTITY" => $quantity));
				} else {
										
					//создаем тп
		
					$arLoad = array();
					$PROP = array();
					$arLoad ["ACTIVE"] = "Y";
					$arLoad ["IBLOCK_ID"] = $offersIblockID;
					$arLoad ["MODIFIED_BY"] = $USER->GetID(); // элемент изменен текущим пользователем
					$arLoad ["NAME"] = $_REQUEST["name"];
					// получаем
					$PROP["COLOR"] = $colorID;
					$PROP["CML2_LINK"] = $productID;
					$PROP["STD_SIZE"] = $sizeID;
					$PROP = array_merge($PROP, $arAdd);
					$arLoad ["PROPERTY_VALUES"] = $PROP;
	
					if ($offerID = $el->Add($arLoad)) {
	
						$arFields = array("ID" => $offerID,"QUANTITY" => $quantity );
						CCatalogProduct::Add($arFields);
					}		
					else {
						//echo  $el->LAST_ERROR;
					}
				}
				
				//priceOffer[168][1155]
				// сохраняем цену
				if ($result['result']=='OK') {
				
					$price = $_REQUEST["price"];
					$prFields = Array(
							"PRODUCT_ID" => $offerID, // идентификатор товара
							"CATALOG_GROUP_ID" => 1, // идентификатор цены
							"PRICE" => $priceOffer,
							"CURRENCY" => "RUB"
					);
				
					CModule::IncludeModule("catalog");
				
					$res = CPrice::GetList(array(),array("PRODUCT_ID" => $offerID, "CATALOG_GROUP_ID" => 1 ));
				
					// если цены еще нет до добавляем ее, если есть - обновляем
					if ($arr = $res->Fetch()) {
				
						$resNewPrice = CPrice::Update($arr["ID"], $prFields);
					}
					else {
				
						$resNewPrice = CPrice::Add($prFields);
					}
				}				
			} // end foreach ($arr as $offerID => $colorID) {			
		}
	} // end if (is_array($_REQUEST["colorOffer"])) {
	
	// удаляем цвета(тп) которые удалили крестиком
	foreach ($offersExists as $key => $value) {
		if (!in_array($key, $offersProcess)) {
			CIBlockElement::Delete($key);
		}
	}
	
	// обрабатываем  фото

	if (!empty($_REQUEST["photo_object"]) && $iblockID > 0 && $productID > 0 ) {
	
		$photoObject = json_decode($_REQUEST["photo_object"], 1);
		
		$photoPropUpdate = array();
		// обновляем свойство фото в элементе если была добавлена хоть одна фотка, либо удалена
		$photoAddedFlag = false;
		$colorPhotos = array();
		if (count($photoObject)>0) {
			foreach ($photoObject as $key => $item) {
				//deb($item);			
				if (!empty($item["elem_color"])) $colorPhotos[$item["elem_color"]][] = $item["elem_pic"];
				
			}	

			$j = 1;
			$props = array();
			
			foreach ($colorPhotos as $color => $photos) {
				$arFile = array();
				foreach ($photos as $photo) {
					$arFile[] = array("VALUE" => CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$photo),"DESCRIPTION"=>"");
				}
				$props["PHOTO_COLOR_".$j] = $arFile;
				$props["PHOTONAME_COLOR_".$j] = $color;
				$j++;
			}

			CIBlockElement::SetPropertyValuesEx($productID, $iblockID, $props);
			// удаляем остальные свойства
			$props = array();
			for ($i = $j; $i <= 10; $i++) {
				$props["PHOTO_COLOR_".$i] = array("VALUE" => "","DESCRIPTION"=>"");
				$props["PHOTONAME_COLOR_".$i] = "";
			}		
			CIBlockElement::SetPropertyValuesEx($productID, $iblockID, $props);
	
		}
	}	
	
	// сохраняем цену
	if (isset($price) && $result['result']=='OK') {
		
		$prFields = Array(		
			"PRODUCT_ID" => $productID, // идентификатор товара
			"CATALOG_GROUP_ID" => 1, // идентификатор цены
			"PRICE" => $price,
			"CURRENCY" => "RUB"
		);	
		
		CModule::IncludeModule("catalog");
		
		$res = CPrice::GetList(array(),array("PRODUCT_ID" => $productID, "CATALOG_GROUP_ID" => 1 )	);
		
		// если цены еще нет до добавляем ее, если есть - обновляем
		if ($arr = $res->Fetch()) {

			$resNewPrice = CPrice::Update($arr["ID"], $prFields);
		}
		else {

			$resNewPrice = CPrice::Add($prFields);
		}
	}

	// очищаем папку от ранее созданных файлов
	clearFolder($uploadsDirPath);
	
	$result['firstBtnText'] = GetMessage("RETURN_TO_LIST");
	$result['secondBtnText'] = GetMessage("ADD_NEW_PRODUCT");	

	$result['message'] = $message;
	$addJson = true;
	//$resultJson = json_encode($result);
	//die($resultJson);
	
}
elseif ($action == 'find_material' && !empty($_REQUEST["q"])) {
	$arFilter = array("NAME" => $_REQUEST["q"] ."%", "IBLOCK_CODE" =>'materials');
	
	$rsElement = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, false, array("ID","NAME") );
	$values = array();
	while ($data = $rsElement -> GetNext()) {

		$values[] = array("id" => $data["ID"], "title" => $data["NAME"]);
	}

	$resultJson = json_encode($values);

	$callback = htmlspecialcharsbx($_GET['callback']);
	echo $callback.'('."{'values':".$resultJson."}".')';
	die();
}
elseif (
	$action == 'find_color'
	&&
	!empty($_REQUEST["q"])
) {
	$arFilter = array("NAME" => htmlspecialcharsbx($_REQUEST["q"]) ."%", "IBLOCK_CODE" =>'colors');

	$rsElement = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, false, array("ID","NAME") );
	$values = array();
	while ($data = $rsElement -> GetNext()) {

		$values[] = array("id" => $data["ID"], "title" => $data["NAME"], 'type' => 'color');
	}

	$resultJson = json_encode($values);

	$callback = htmlspecialcharsbx($_GET['callback']);
	echo $callback.'('."{'values':".$resultJson."}".')';
	die();
}
elseif ($action == 'find_brand' && !empty($_REQUEST["q"])) {
	$arFilter = array("NAME" => htmlspecialcharsbx($_REQUEST["q"]) ."%", "IBLOCK_CODE" =>'vendor');

	$rsElement = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, false, array("ID","NAME") );
	$values = array();
	while ($data = $rsElement -> GetNext()) {

		$values[] = array("id" => $data["ID"], "title" => $data["NAME"]);
	}

	$resultJson = json_encode($values);

	$callback = htmlspecialcharsbx($_GET['callback']);
	echo $callback.'('."{'values':".$resultJson."}".')';
	die();
}
elseif ($action == 'get_classificator_popup' && !empty($_REQUEST["name"])) {
	// отдаем html для попапа выбора элемента в справочнике
	if ($_REQUEST["name"]== "add_size") {
		
		$currentSections = array();
		if (isset($_REQUEST["secid"]) && is_array($_REQUEST["secid"])) {
			foreach ($_REQUEST["secid"] as $item) {
				$currentSections[] = $item;
			}
		}
			
		$APPLICATION->IncludeComponent("novagr.shop:cabinet.sizes.choser", "", array(
		
		"STD_SIZE_IBLOCK_CODE" => 'std_sizes',
		"HEADLINE" => GetMessage("SIZE_CHOOSE"),
		"BUTTON_TITLE" => GetMessage("SIZE_CHOOSE2"),
		"DIV_ID" => "modal_div",
		"nPageSize" => "10",
		"AJAX" => "Y",
		"AJAX_CONTENT" => "N",
		"SELECTED_SECTION" => $currentSections,		
		"MULTIPLE_CHOICE" => "Y",
		"JS_ARRAY_FOR_VALUES" => "choosedSizes",
		"CURRENT_VALUES" => $_REQUEST["current_value"],				
		),
		false
	); 
	}
	die();
	
} elseif ($iblockID > 0 && isset($_REQUEST["iNumPage"]) && isset($_REQUEST["nPageSize"]) && ($_REQUEST["AJAX"] == "Y") ) {
	// отдаем html попапа выбора элемента в справочнике - по клику на пагинации
	$arParams =  array(
			"STD_SIZE_IBLOCK_CODE" => 'std_sizes',
			"AJAX" => "Y",
			"AJAX_CONTENT" => "Y"
	);
	
	$APPLICATION->IncludeComponent("novagr.shop:cabinet.sizes.choser", ".default", $arParams, false);
	die('');
} 

if ($addJson == true) {
	
	if ($siteUTF8 == false) {
		$result = prepareResultJson($result);
	}
	$resultJson = json_encode($result);
	die($resultJson);
}

function prepareResultJson($result) {
	foreach ($result as $key => $item) {
	
		if (is_string($result[$key])) {
			$result[$key] = iconv('windows-1251', 'UTF-8', $result[$key]);
		} elseif (is_array($result[$key])) {
				
			foreach ($result[$key] as $k => $v) {
				if (is_string($v)) {
					$result[$key][$k] = iconv('windows-1251', 'UTF-8', $v);
				} elseif (is_array($v))  {

				}
			}
		}
	}
	return $result;
}

die('fin');
?>