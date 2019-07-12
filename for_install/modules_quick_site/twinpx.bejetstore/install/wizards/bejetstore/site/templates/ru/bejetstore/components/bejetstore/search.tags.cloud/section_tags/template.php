<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

if(empty($arResult["SEARCH"]))
	return;
?>
<ul class="nav nav-pills bj-h1-nav hidden-xs text-right"><?
	foreach ($arResult["SEARCH"] as $key => $res){
	?><li><a href="<?=str_replace("tags", "q", $res["URL"])?>"><?=$res["NAME"]?></a></li><?
	}
	?>
</ul>
<hr>