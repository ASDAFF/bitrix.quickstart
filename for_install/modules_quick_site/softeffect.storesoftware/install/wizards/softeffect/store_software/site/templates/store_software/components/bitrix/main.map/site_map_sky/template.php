<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!is_array($arResult["arMap"]) || count($arResult["arMap"]) < 1)
	return;

$arRootNode = Array();

//echo "<pre>"; print_r($arResult); echo "</pre>";

foreach($arResult["arMap"] as $index => $arItem)
{
	if ($arItem["LEVEL"] == 0)
		$arRootNode[] = $index;
}

$allNum = count($arRootNode);
$colNum = ceil($allNum / $arParams["COL_NUM"]);

// собираем каталог сюда
$temp=array();
$temp[] = array(
	'LEVEL' => '0',
	'ID' => '',
	'IS_DIR' => 'Y',
	'NAME' => GetMessage('SE_MAP_CATALOG_TITLE'),
	'PATH' => $_SERVER['DOCUMENT_ROOT'],
	'FULL_PATH' => SITE_DIR.'catalog/',
	'SEARCH_PATH' => SITE_DIR.'catalog/',
	'DESCRIPTION' => '',
	'STRUCT_KEY' => '0'
);
global $IB_CATALOG;
$i=0;
$dbSec = CIBlockSection::GetList(array('NAME'=>'ASC', 'SORT'=>'ASC'), array('IBLOCK_ID'=>$IB_CATALOG, 'ACTIVE'=>'Y', 'DEPTH_LEVEL'=>'1'));
while ($arSec = $dbSec->GetNext()) {
	$temp[] = array(
		'LEVEL' => '1',
		'ID' => '',
		'IS_DIR' => 'N',
		'NAME' => $arSec['NAME'],
		'PATH' => $_SERVER['DOCUMENT_ROOT'],
		'FULL_PATH' => SITE_DIR.'catalog/'.$arSec['CODE'].'/',
		'SEARCH_PATH' => SITE_DIR.'catalog/'.$arSec['CODE'].'/',
		'DESCRIPTION' => '',
		'STRUCT_KEY' => $i
	);
	$i++;
}
$arResult["arMap"] = array_merge($temp, $arResult["arMap"]);
?>
<table class="map-columns">
<tr>
	<td>
		<ul class="map-level-0">

		<?
		$previousLevel = -1;
		$counter = 0;
		$column = 1;
		foreach($arResult["arMap"] as $index => $arItem):?>

			<?if ($arItem["LEVEL"] < $previousLevel):?>
				<?=str_repeat("</ul></li>", ($previousLevel - $arItem["LEVEL"]));?>
			<?endif?>


			<?if ($counter >= $colNum && $arItem["LEVEL"] == 0): 
					$allNum = $allNum-$counter;
					$colNum = ceil(($allNum) / ($arParams["COL_NUM"] > 1 ? ($arParams["COL_NUM"]-$column) : 1));
					$counter = 0;
					$column++;
			?>
				</ul></td><td><ul class="map-level-0">
			<?endif?>

			<?if (array_key_exists($index+1, $arResult["arMap"]) && $arItem["LEVEL"] < $arResult["arMap"][$index+1]["LEVEL"]):?>

				<li><a href="<?=$arItem["FULL_PATH"]?>"><?=$arItem["NAME"]?></a><?if ($arParams["SHOW_DESCRIPTION"] == "Y" && strlen($arItem["DESCRIPTION"]) > 0) {?><div><?=$arItem["DESCRIPTION"]?></div><?}?>
					<ul class="map-level-<?=$arItem["LEVEL"]+1?>">

			<?else:?>

					<li><a href="<?=$arItem["FULL_PATH"]?>"><?=$arItem["NAME"]?></a><?if ($arParams["SHOW_DESCRIPTION"] == "Y" && strlen($arItem["DESCRIPTION"]) > 0) {?><div><?=$arItem["DESCRIPTION"]?></div><?}?></li>

			<?endif?>


			<?
				$previousLevel = $arItem["LEVEL"];
				if($arItem["LEVEL"] == 0)
					$counter++;
			?>

		<?endforeach?>

		<?if ($previousLevel > 1)://close last item tags?>
			<?=str_repeat("</ul></li>", ($previousLevel-1) );?>
		<?endif?>

		</ul>
	</td>
</tr>
</table>