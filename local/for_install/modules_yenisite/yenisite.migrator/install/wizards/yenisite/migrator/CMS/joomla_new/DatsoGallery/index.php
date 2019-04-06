<?
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');
CModule::IncludeModule("iblock");

$arFields = Array(
	'ID'=>'Gallery',
	'SECTIONS'=>'Y',
	'SORT'=>100,
	'LANG'=>Array(
		'en'=>Array(
			'NAME'=>'Gallery',
			)
		)
	);

$obBlocktype = new CIBlockType;
$res = $obBlocktype->Add($arFields);

$arFields = array(
	"SITE_ID" => "s1",
	"ACTIVE" => "Y",
	"IBLOCK_TYPE_ID" => "Gallery",
	"NAME" => "DatsoGallery",
	"CODE" => "DatsoGallery",
);
	
$iblock = new CIBlock;
$res = CIBlock::GetList(array(), array("CODE" => "DatsoGallery"))->GetNext();
if($res)
	$ibid = $res["ID"];
else
	$ibid = $iblock->Add($arFields);

$assoc = array("0" => "0");

$query = "SELECT * FROM ".$arResult["prefix"]."datsogallery_catg ORDER BY parent";
$result = mysql_query($query, $link);
$arItem = array();
$sec = new CIBlockSection;
while($arItem = mysql_fetch_assoc($result))
{
	
	$arFields = array(
		"IBLOCK_ID" => $ibid,
		"IBLOCK_SECTION_ID" => $assoc[$arItem["parent"]],
		"NAME" => $arItem["name"],
		"DESCRIPTION" => $arItem["description"],
		"SORT" => $arItem["ordering"],
		);	
	$sid = $sec->Add($arFields);
	$assoc[$arItem["cid"]] = $sid;
}


$query = "SELECT * FROM ".$arResult["prefix"]."datsogallery";
$result = mysql_query($query, $link);
$arItem = array();
$el = new CIBlockElement;


while($arItem = mysql_fetch_assoc($result))
{
$host = substr_count($arResult["site"], "http://")?$arResult["site"]:"http://".$arResult["site"];
	$arFile = CFile::MakeFileArray($host."/images/stories/dg_thumbnails/".$arItem["imgthumbname"]);
	$arFile2 = CFile::MakeFileArray($host."/images/stories/dg_originals/".$arItem["imgoriginalname"]);
	$arFields = array(
		"IBLOCK_ID" => $ibid,
		"IBLOCK_SECTION_ID" => $assoc[$arItem["catid"]],
		"NAME" => $arItem["imgtitle"],
		"PREVIEW_TEXT" => $arItem["imgtext"],
		"SORT" => $arItem["ordering"],
		"PREVIEW_PICTURE" => $arFile,
		"DETAIL_PICTURE" => $arFile2
		);	
	$id = $el->Add($arFields);
}


	$step += 1;
	$this->content .= $this->ShowHiddenField("step", $step);
	
?>
