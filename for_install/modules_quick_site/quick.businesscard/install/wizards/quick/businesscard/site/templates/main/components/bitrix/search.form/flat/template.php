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
$this->setFrameMode(true);?>
<form action="<?=$arResult["FORM_ACTION"]?>" role="search" class="search-box">
	<div class="form-group has-feedback">
		<input type="text" name="q" class="form-control" required placeholder="<?=GetMessage("BSF_T_SEARCH_TEXT");?>">
		<i class="fa fa-search form-control-feedback"></i>
	</div>
</form>