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
?>
<div class="col-md-6 hidden-sm hidden-xs">
<?if(!empty($arResult["SEARCH"])):?>
	<nav class="bj-top-subnav"><?
	foreach ($arResult["SEARCH"] as $key => $res){
	?><a href="<?=str_replace("tags", "q", $res["URL"])?>"><?=$res["NAME"]?></a><?
	}
	?></nav>
<?endif;?>
</div>