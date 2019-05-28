<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if($_POST['web_form_submit']){
    // Обработка анкеты

    $ORDER_ID = $_POST['ORDER_ID'];
    unset($_POST['ORDER_ID']);
    unset($_POST['web_form_submit']);

    foreach($_POST as $code => $data) {
        if(is_numeric($code)){
			if(strip_tags($data) != ''){
				$el = new CIBlockElement;
				$PROP = array();
				$PROP["SKU"] = $code;  // свойству с кодом 12 присваиваем значение "Белый"

				$arLoadProductArray = Array(
					"MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
					"IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
					"IBLOCK_ID"      => $arParams['COMMENTS_IBLOCK_ID'],
					"PROPERTY_VALUES"=> $PROP,
					"NAME"           => "Комментарий к товару ID ".$code,
					"ACTIVE"         => "Y",            // активен
					"PREVIEW_TEXT"   => $data,
				);
				if($PRODUCT_ID = $el->Add($arLoadProductArray)){
					$success = "New ID: ".$PRODUCT_ID;
				}
			}
        }else{
            if ($arProp = CSaleOrderProps::GetList(array(), array('CODE' => $code))->Fetch()) {
				$db_order_props_tmp = CSaleOrderPropsValue::GetList(($b="NAME"),($o="ASC"), Array("ORDER_PROPS_ID"=>$arProp['ID'], 'ORDER_ID' => $ORDER_ID));
				while ($ar_order_props_tmp = $db_order_props_tmp->Fetch())
				{
					CSaleOrderPropsValue::Update($ar_order_props_tmp['ID'],array(
						'VALUE' => $data,
					));
				}
            }
        }
    }
    COption::SetOptionString("sale","interview_".$ORDER_ID, 'Y');
    $arResult['MESSAGE'] = GetMessage('WPP_ORDER_INTERVIEW_SUCCESS');
}else{
	
    if(empty($_REQUEST['id'])){
        // Ошибка, ID не задан
        $arResult['MESSAGE'] = GetMessage('WPP_ORDER_INTERVIEW_ERROR_1');
    }else{
        $orderInfo = CSaleOrder::GetByID($_REQUEST['id']);
        // Ошибка. Заказа с таким ID не существует
        if(empty($orderInfo)) {
            $arResult['MESSAGE'] = GetMessage('WPP_ORDER_INTERVIEW_ERROR_2'); 
        }else{ 
            if(COption::GetOptionString("sale","interview_".$orderInfo['ID']) == 'Y') {
                $arResult['MESSAGE'] = GetMessage('WPP_ORDER_INTERVIEW_ERROR_3');
            }
        }
    }
    if($arResult['MESSAGE'] == ''){
        // установим заголовок страницы, включающий ID заказа
        $APPLICATION->SetTitle(GetMessage('WPP_ORDER_INTERVIEW_TITLE').$orderInfo['ID']);
        // Получим свойства заказа, для списка критериев оценки качества.
        
		$db_props = CSaleOrderProps::GetList(
            array("SORT" => "ASC"),
            array(
                "PROPS_GROUP_ID" => $arParams['PROP_GROUP'], // Группа товаров "Анкета пользователя"
            ),
            false,
            false,
            array()
        );
		
        while($props = $db_props->GetNext()){
            if ($props["TYPE"] == "TEXT") {
                $arResult['CRITERIA'][] = array(
                    'ID' => $props["ID"],
                    'CODE' => $props["CODE"],
                    'NAME' => $props["NAME"],
                    'DEFAULT_VALUE' => $props["DEFAULT_VALUE"]
                );
            } elseif ($props["TYPE"] == "TEXTAREA") {
                $arResult['FIELDS'][] = array(
                    'ID' => $props["ID"],
                    'CODE' => $props["CODE"],
                    'NAME' => $props["NAME"],
                    'DEFAULT_VALUE' => $props["DEFAULT_VALUE"]
                );
            }
        }
		

        // Получим товары заказа
        $dbBasketItems = CSaleBasket::GetList(array(), array("ORDER_ID" => $orderInfo['ID']), false, false, array('ID', 'NAME', 'DETAIL_PAGE_URL', 'PRODUCT_ID'));
        while ($arItem = $dbBasketItems->GetNext()) {
            $productInfo = CIBlockElement::GetByID($arItem['PRODUCT_ID'])->Fetch();
            $arItem['IMG'] = CFile::GetPath($productInfo['DETAIL_PICTURE']);
            $arResult['PRODUCTS'][] = $arItem;
        }

        $arResult['ORDER_ID'] = $orderInfo['ID'];
    }
}
$this->IncludeComponentTemplate();
?>