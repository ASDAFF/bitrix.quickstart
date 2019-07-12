<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'admin-config/config.php');

CModule::AddAutoloadClasses( 
	'',
	array(
		'CSofteffect' => '/admin-config/functions.php', 
	) 
);

CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');
CModule::IncludeModule('sale');

define("SECTION", true);
global $arPortfolioFilter;
$arPortfolioFilter = array();

if (!$_REQUEST['search_sort']) {
	$_REQUEST['search_sort'] = 'ASC';
}

$arResult=array();
$arResultNEW=array();
$arID=array();
$arPortfolioFilter=array();
$arSec = $_SESSION['arSec'];
$arSecL2 = $_SESSION['arSecL2'];
$arResultComponent = $_SESSION['arResultComponent'];
$count=0;
$filterCount=0;

if ($_REQUEST['ajax']=='Y') {
	__IncludeLang($_SERVER['DOCUMENT_ROOT'].$arResultComponent['AJAX_PATH']['COMPONENT_FOLDER'].'/lang/'.LANGUAGE_ID.'/component.php');
}

$arPropLink = CIBlockSectionPropertyLink::GetArray($arResultComponent['CATALOG_IBLOCK_ID'], 0);

$dbElProp = CIBlockElement::GetList(array('SORT'=>'ASC', 'NAME'=>'ASC'), array('IBLOCK_ID'=>$arResultComponent['CATALOG_IBLOCK_ID'], 'ACTIVE'=>'Y', 'SECTION_CODE' => $arResultComponent['CATALOG_SECTION_L2_CODE']));
while ($arElProp = $dbElProp->GetNextElement()) {
	$arItem = $arElProp->GetFields();
	$arItem["PROPERTIES"] = $arElProp->GetProperties();

	##### СПИСОК СВО-В В ФИЛЬТР #####
	foreach ($arItem["PROPERTIES"] as $key => $value) {
		if ($arPropLink[$value['ID']]['SMART_FILTER']!='Y') continue;
		
		if (!isset($arPortfolioFilter[$value['ID']])) {
			$arPortfolioFilter[$value['ID']] = array(
				'ID'=>$value['ID'],
				'CODE'=>$value['CODE'],
				'NAME'=>$value['NAME'],
				'SORT'=>$value['SORT'],
				'LIST'=>array()
			);
		}
		if (is_array($value['VALUE_ENUM_ID'])) {
			foreach ($value['VALUE_ENUM_ID'] as $keyP => $valueP) {
				if ($arPortfolioFilter[$value['ID']]['LIST'][$valueP]) {
					$arPortfolioFilter[$value['ID']]['LIST'][$valueP]['COUNT']++;
				} else {
					$arPortfolioFilter[$value['ID']]['LIST'][$valueP]=array(
						'ID' => $valueP,
						'VALUE' => $value['VALUE'][$keyP],
						'COUNT' => 1
					);
				}
			}
		} elseif (strlen($value['VALUE_ENUM_ID'])>0) {
			if ($arPortfolioFilter[$value['ID']]['LIST'][$value['VALUE_ENUM_ID']]) {
				$arPortfolioFilter[$value['ID']]['LIST'][$value['VALUE_ENUM_ID']]['COUNT']++;
			} else {
				$arPortfolioFilter[$value['ID']]['LIST'][$value['VALUE_ENUM_ID']]=array(
					'ID' => $value['VALUE_ENUM_ID'],
					'VALUE' => $value['VALUE'],
					'COUNT' => 1
				);
			}
		}

		if (count($arPortfolioFilter[$value['ID']]['LIST'])<=0) {
			unset($arPortfolioFilter[$value['ID']]);
		}
	}
	
	// массив со ВСЕМИ значениями сво-в элементов
	foreach ($arItem["PROPERTIES"] as $codeP=>$dataP) {
		if (intval($dataP['SORT'])>=1000) {
			if (is_array($dataP['VALUE']) && count($dataP['VALUE_ENUM_ID'])>0) {
				foreach ($dataP['VALUE_ENUM_ID'] as $key => $value) {
					$propViewAll[$dataP['CODE']][]=$value;
				}
				$propViewAll[$dataP['CODE']] = array_unique($propViewAll[$dataP['CODE']]);
			} elseif (is_string($dataP['VALUE']) && strlen($dataP['VALUE'])>0) {
				if (!in_array($dataP['VALUE_ENUM_ID'], $propViewAll[$dataP['CODE']])) {
					$propViewAll[$dataP['CODE']][]=$dataP['VALUE_ENUM_ID'];
				}
			}
		}
	}

	##### ПРОВЕРКА НА СВО-ВА #####
	// раз мы выбираем сразу все элементы - делаем ручную проверку на сво-ва (экономия запросов)
	// если удовлетворяет - выводим
	$success=0;
	foreach ($_REQUEST['PROPERTY'] as $key => $value) {
		if (in_array($value[0], $arItem["PROPERTIES"][$key]["VALUE_ENUM_ID"]) || $value[0]==$arItem["PROPERTIES"][$key]["VALUE_ENUM_ID"])
			$success++;
	}

	if ($success==count($_REQUEST['PROPERTY'])) { // если прошел проверку по всем сво-вам
		// проверяем по имени, если есть
		if (!$_REQUEST['search_text'] || $_REQUEST['search_text'] && strpos($arItem['NAME'], $_REQUEST['search_text'])!==FALSE) {
			// массив со всеми значениями сво-в ФИЛЬТРОВАННЫХ элементов
			foreach ($arItem["PROPERTIES"] as $codeP=>$dataP) {
				if (intval($dataP['SORT'])>=1000) {
					if (is_array($dataP['VALUE']) && count($dataP['VALUE_ENUM_ID'])>0) {
						foreach ($dataP['VALUE_ENUM_ID'] as $key => $value) {
							$propView[$dataP['CODE']][]=$value;
						}
						$propView[$dataP['CODE']] = array_unique($propView[$dataP['CODE']]);
					} elseif (is_string($dataP['VALUE']) && strlen($dataP['VALUE'])>0) {
						if (!in_array($dataP['VALUE_ENUM_ID'], $propView[$dataP['CODE']])) {
							$propView[$dataP['CODE']][]=$dataP['VALUE_ENUM_ID'];
						}
					}
				}
			}
			
			#################################
			##### сбор массива на вывод #####
			$arPrice = CCatalogProduct::GetOptimalPrice($arItem['ID'], '1', $USER->GetUserGroupArray());
			$price['PRICE'] = $arPrice["DISCOUNT_PRICE"];
		
			if (intval($arPrice['DISCOUNT']["VALUE"])!=0) {
				$priceNoDiscount=(intval($arPrice["DISCOUNT_PRICE"])/(100-intval($arPrice['DISCOUNT']["VALUE"]))*100);
			} else {
				$priceNoDiscount=0;
			}
		
			
			if ($arItem['PREVIEW_PICTURE'])  {
				$pic = $arItem['PREVIEW_PICTURE'];
			} elseif ($arSecL2['PICTURE']) {
				$pic = $arSecL2['PICTURE'];
			} else {
				$dbSecPar = CIBlockSection::GetNavChain(false, $arSecL2['ID']);
				if ($arSecPar = $dbSecPar->GetNext()) {
					$pic = $arSecPar['PICTURE'];
				}
			}
			$renderImage = CFile::ResizeImageGet($pic, Array("width" => '50', "height" => '150'));
			
			if (!$arItem['PROPERTIES']['TYPE_LIC']['VALUE']) {
				$arItem['PROPERTIES']['TYPE_LIC']['VALUE'][]=GetMessage('SE_CATALOGSECTIONLIST_AJAX_LIC');
			}
			
			foreach ($arItem['PROPERTIES']['TYPE_LIC']['VALUE'] as $value) {
				$arResultNEW[$value][$arItem["PROPERTIES"]['EDITION']['VALUE']][$arItem['ID']]=array(
					'ID' => $arItem['ID'],
					'NAME' => $arItem['NAME'],
					'USERS_QUANTITY' => $arItem["PROPERTIES"]['USERS_QUANTITY']['VALUE'],
					'PERIOD' => $arItem["PROPERTIES"]['PERIOD']['VALUE'],
					'FORMAT' => $arItem["PROPERTIES"]['FORMAT']['VALUE'],
					'PICTURE' => $renderImage['src'],
					//'PICTURE'=>'/upload/iblock/ed9/ed9f8d8fa750a9e9e044fe4d20747108.gif',
					'URL' => $arItem['DETAIL_PAGE_URL'],
					'ARTICLE' => $arItem["PROPERTIES"]['CML2_ARTICLE']['VALUE'],
					'PRICE' => SaleFormatCurrency(($price['PRICE']>0) ? intval($price['PRICE']) : 0, "RUB"),
					'OLD_PRICE' => ($arItem["PROPERTIES"]['OLD_PRICE']['VALUE']) ? intval($arItem["PROPERTIES"]['OLD_PRICE']['VALUE']) : intval($priceNoDiscount),
					'DISCOUNT'=>intval($arPrice['DISCOUNT']["VALUE"]),
					'PLATFORM' => CSofteffect::getPlatform($arItem['NAME']),
					'UF_NAME_RUS' => $name_rus,
					'NAME_SEC' => $arSecL2['NAME'],
					'TYPE_LIC' => $arItem["PROPERTIES"]['TYPE_LIC']['VALUE'],
					'DELIVERY_TIME'=>$arItem["PROPERTIES"]['DELIVERY_TIME']['VALUE']
				);
			}
			#################################
			#################################
		}
	}
}

foreach ($arPortfolioFilter as $key => $value) {
	if (count($value['LIST'])>1) { // если в категории фильтра более одной позиции
		$filterCount += count($value['LIST']);
	}
}

require_once $_SERVER['DOCUMENT_ROOT'].$arResultComponent['AJAX_PATH']['TEMPLATE'];
?>