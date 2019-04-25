<?php
error_reporting(E_ALL);
header('Content-type: text/html; charset=utf-8');
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__));
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('NO_AGENT_CHECK', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$token = 'z11a6Hvs6uShBuzxFGM1';

function error() {
	header("HTTP/1.0 404 Not Found");
	echo 'error';
	exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST' or $_POST['token'] != $token or !isset($_POST['data'])) {
	error();
}

// Символьный код по названию
function code($name) {
	$name = mb_strtolower($name);
	$name = preg_replace(['/-{2,}/', '/\s{2,}/'], ['-', ' '], $name);
	$tr = [
		'а'=>'a', 'б'=>'b', 'в'=>'v', 'г'=>'g', 'д'=>'d',
		'е'=>'e', 'ё'=>'e', 'ж'=>'j', 'з'=>'z', 'и'=>'i',
		'й'=>'y', 'к'=>'k', 'л'=>'l', 'м'=>'m', 'н'=>'n',
		'о'=>'o', 'п'=>'p', 'р'=>'r', 'с'=>'s', 'т'=>'t',
		'у'=>'u', 'ф'=>'f', 'х'=>'h', 'ц'=>'ts', 'ч'=>'ch',
		'ш'=>'sh', 'щ'=>'sch', 'ъ'=>'y', 'ы'=>'yi', 'ь'=>'',
		'э'=>'e', 'ю'=>'yu', 'я'=>'ya', ' '=> '-', '.'=>'',
		','=>'', '/'=>'-', '!'=>'', '('=>'', ')'=>''
	];
	return strtr($name, $tr);
}

// Проверка наличия раздела
function isSection($name, $id = 0) {
	$items = GetIBlockSectionList(1, $id, ['sort' => 'asc'], 1, ['NAME' => $name]);
	if (!$items->arResult) {
		return false;
	} else {
		return (int)$items->arResult[0]['ID'];
	}
}

// Создание раздела
function createSection($name, $id = 0) {
	$block = new CIBlockSection;
	$options = [
		'ACTIVE' => 'Y',
		'IBLOCK_ID' => 1,
		'IBLOCK_SECTION_ID' => $id,
		'CODE' => code($name),
		'NAME' => $name,
		'SORT' => 500,
		'DESCRIPTION_TYPE' => 'text'
	];
	return $block->Add($options, true, true);
}

// Создание или обновление раздела и получение его id
function createOrUpdateSection($name, $id = 0) {
	$ident = isSection($name, $id);
	if (!$ident) {
		$ident = createSection($name, $id);
	}
	return $ident;
}

// Проверка на существование записи и возврат ее id в случае наличия
function isGood($data) {
	$filter = [
		//'SECTION_ID' => $id,
		'PROPERTY_1' => $data->code,
		'ACTIVE' => 'Y'
	];
	$good = CIBlockElement::GetList([], $filter, false, ['nPageSize' => 1]);
	if (!$good->arResult) {
		return false;
	} else {
		if (stristr(mb_strtolower($data->code), 'rgc') === false and stristr(mb_strtolower($good->arResult[0]['CODE']), 'rgc') !== false) {
			// Не обновляем по приоритетам
			echo 3;
			exit;
		}
		return (int)$good->arResult[0]['ID'];
	}
}

// Дополнительные изображения
function getImages($data) {
	$images = [];
	foreach ($data->images as $image) {
		$images[] = [
			'VALUE' => CFile::MakeFileArray($image),
			'DESCRIPTION' => $data->name
		];
	}
	unset($images[0]);
	return $images;
}

// Добавление записи
function addGood($data, $id) {
	$properties = [
		'CML2_ARTICLE' => $data->code,
		'PRICE' => $data->priceNum,
		'DISCOUNT' => $data->discount,
		'CURRENCY' => $data->currency,
		'QUANTITY' => '1',
		'BRAND' => $data->manufacturer,
		'TYPE' => $data->subsection
	];
	if ($data->priceNum < 1) {
		$properties['CURRENCY'] = 0;
	}
	if (count($data->images) > 1) {
		$properties['MORE_PHOTO'] = getImages($data);
	}
	$options = [
		'MODIFIED_BY' => 1,
		'IBLOCK_SECTION_ID' => $id,
		'IBLOCK_ID' => 1,
		'CODE' => $data->code,
		'PROPERTY_VALUES'=> $properties,
		'NAME' => $data->name,
		'ACTIVE' => 'Y',
		'PREVIEW_TEXT' => $data->data,
		'DETAIL_TEXT' => $data->additionally,
		'PREVIEW_TEXT_TYPE' => 'html',
		'DETAIL_TEXT_TYPE' => 'html',
	];
	if (!empty($data->images[0])) {
		$options['PREVIEW_PICTURE'] = CIBlock::ResizePicture(CFile::MakeFileArray($data->images[0]), [
			'WIDTH' => 300,
            'HEIGHT' => 300,
		]);
		$options['DETAIL_PICTURE'] = CFile::MakeFileArray($data->images[0]);
	}
	$el = new CIBlockElement;
	return $el->Add($options);
}

// Обновление записи
function updateGood($data, $ident, $id) {
	$properties = [
		'CML2_ARTICLE' => $data->code,
		'PRICE' => $data->priceNum,
		'DISCOUNT' => $data->discount,
		'CURRENCY' => $data->currency,
		'QUANTITY' => '1',
		//'BRAND' => $data->manufacturer,
		//'TYPE' => $data->subsection
	];
	if ($data->priceNum < 1) {
		$properties['CURRENCY'] = 0;
	}
	$prop['MORE_PHOTO'] = ['VALUE' => false];
	CIBlockElement::SetPropertyValuesEx($ident, false, $prop);
	if (count($data->images) > 1) {
		$properties['MORE_PHOTO'] = getImages($data);
	}
	CIBlockElement::SetPropertyValuesEx($ident, false, $properties);
	$options = [
		'MODIFIED_BY' => 1,
		'IBLOCK_SECTION_ID' => $id,
		'IBLOCK_ID' => 1,
		'CODE' => $data->code,
		//'PROPERTY_VALUES'=> $properties,
		'NAME' => $data->name,
		'ACTIVE' => 'Y',
		//'PREVIEW_TEXT' => $data->data,
		//'DETAIL_TEXT' => $data->additionally,
		//'PREVIEW_TEXT_TYPE' => 'html',
		//'DETAIL_TEXT_TYPE' => 'html',
	];
	if (!empty($data->images[0])) {
		$options['PREVIEW_PICTURE'] = CIBlock::ResizePicture(CFile::MakeFileArray($data->images[0]), [
			'WIDTH' => 300,
            'HEIGHT' => 300,
		]);
		$options['DETAIL_PICTURE'] = CFile::MakeFileArray($data->images[0]);
	}
	$el = new CIBlockElement;
	return $el->Update($ident, $options);
}

// Создание или обновление записи и получение информации
function createOrUpdateRecord($data, $id) {
	$ident = isGood($data);
	if (!$ident) {
		$res = addGood($data, $id);
		if (is_numeric($res)) {
			return 1;
		} else {
			return 'error';
		}
	} else {
		if (updateGood($data, $ident, $id)) {
			return 2;
		} else {
			return 'error';
		}
	}
}

// Поиск по имени
function findByName($name) {
	$filter = [
		'NAME' => $name,
		'ACTIVE' => 'Y'
	];
	$good = CIBlockElement::GetList([], $filter, false, ['nPageSize' => 1]);
	if (!$good->arResult) {
		return false;
	} else {
		return [
			'id' => (int)$good->arResult[0]['ID'],
			'section' => (int)$good->arResult[0]['IBLOCK_SECTION_ID']
		];
		return (int)$good->arResult[0]['ID'];
	}
}

if (CModule::IncludeModule('iblock')) {
	$data = json_decode($_POST['data']);
	$data->name = urldecode($data->name);
	$data->altName = urldecode($data->altName);
	$data->data = str_replace('amp;', '&', $data->data);
	$data->additionally = str_replace('amp;', '&', $data->additionally);
	if (stristr(mb_strtolower($data->code), 'rgc') !== false) {
		$good = findByName($data->altName);
		if ($good !== false and is_array($good)) {
			if (updateGood($data, $good['id'], $good['section'])) {
				echo 2;
			} else {
				echo 'error';
			}
			exit;
		}
	}
	$tabId = createOrUpdateSection($data->tab);
	$sectionId = createOrUpdateSection($data->section, $tabId);
	//$subsectionId = createOrUpdateSection($data->subsection, $sectionId);
	echo createOrUpdateRecord($data, $sectionId);
} else {
	error();
}