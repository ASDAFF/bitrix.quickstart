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

if(is_array($arResult["SEARCH"]) && !empty($arResult["SEARCH"])):?>
<div class="tags-cloud">
<noindex>
	<?foreach($arResult["SEARCH"] as $key => $res):?>
		<div class="tag"><a href="<?=$res["URL"]?>"><?=$res["NAME"]?></a></div>
	<?endforeach?>
</noindex>
</div>
<?endif?>