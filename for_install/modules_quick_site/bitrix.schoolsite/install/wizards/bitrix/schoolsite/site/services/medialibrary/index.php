<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!CModule::IncludeModule("fileman"))
	return;

$source_base = dirname(__FILE__);
$documentRoot = rtrim(str_replace(Array("\\\\", "//", "\\"), Array("\\", "/", "/"), $_SERVER["DOCUMENT_ROOT"]), "\\/");
$source_base = substr($source_base, strLen($documentRoot));
$source_base = str_replace(array("\\", "//"), "/", "/".$source_base."/");

__IncludeLang(dirname(__FILE__)."/lang/".LANGUAGE_ID.'/'.basename(__FILE__));

$arCollections = array(
	array('name' => GetMessage('ML_COL_NAME_0'), 'desc' => GetMessage('ML_COL_DESC_0')),
	array('name' => GetMessage('ML_COL_NAME_1'), 'desc' => GetMessage('ML_COL_DESC_1')),
	array('name' => GetMessage('ML_COL_NAME_2'), 'desc' => GetMessage('ML_COL_DESC_2'), 'ex_parent' => 0)
);

$arExCols = array();
for($i = 0, $l = count($arCollections); $i < $l; $i++)
{
	$arExCols[$i] = CMedialib::EditCollection(array(
		'name' => $arCollections[$i]['name'],
		'desc' => $arCollections[$i]['desc'],
		'keywords' => '',
		'parent' => isset($arCollections[$i]['ex_parent'], $arExCols[$arCollections[$i]['ex_parent']]) ? intVal($arExCols[$arCollections[$i]['ex_parent']]) : 0,
		'type' => 0
	));
}

// Save elements
$arItems = array(
	array('fname' => 'ml01.jpg', 'name' => GetMessage('ML_IT_NAME_1'), 'ex_cols' => array(2)),
	array('fname' => 'ml02.jpg', 'name' => GetMessage('ML_IT_NAME_2'), 'ex_cols' => array(1)),
	array('fname' => 'ml03.jpg', 'name' => GetMessage('ML_IT_NAME_3'), 'ex_cols' => array(2)),
	array('fname' => 'ml04.jpg', 'name' => GetMessage('ML_IT_NAME_4'), 'ex_cols' => array(1)),
	array('fname' => 'ml05.jpg', 'name' => GetMessage('ML_IT_NAME_5'), 'ex_cols' => array(2)),
	array('fname' => 'ml06.jpg', 'name' => GetMessage('ML_IT_NAME_6'), 'ex_cols' => array(1,2)),
	array('fname' => 'ml07.jpg', 'name' => GetMessage('ML_IT_NAME_7'), 'ex_cols' => array(1,2)),
	array('fname' => 'ml08.jpg', 'name' => GetMessage('ML_IT_NAME_8'), 'ex_cols' => array(1)),
	array('fname' => 'ml09.jpg', 'name' => GetMessage('ML_IT_NAME_9'), 'ex_cols' => array(0,1)),
	array('fname' => 'ml10.jpg', 'name' => GetMessage('ML_IT_NAME_10'), 'ex_cols' => array(0,1))
);


for($i = 0, $l = count($arItems); $i < $l; $i++)
{
	$path = $source_base.'files/'.$arItems[$i]['fname'];
	$arCols = array();
	for ($j = 0, $n = count($arItems[$i]['ex_cols']); $j < $n; $j++)
		$arCols[] = $arExCols[$arItems[$i]['ex_cols'][$j]];

	CMedialibItem::Edit(array(
		'path' => $path,
		'arFields' => array(
			'NAME' => $arItems[$i]['name'],
			'DESCRIPTION' => '',
			'KEYWORDS' => ''
		),
		'arCollections' => $arCols
	));
}
?>