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
CJSCore::Init(array("fx"));
define("TOP_FILTER_VALUES_COUNT", 4);

global ${$arParams["FILTER_NAME"]};
$arFilter = ${$arParams["FILTER_NAME"]};

if(empty($arResult["ITEMS"]) || !$arResult["SHOW_FILTER"])
	return;

if (file_exists($_SERVER["DOCUMENT_ROOT"].$this->GetFolder().'/themes/'.$arParams["TEMPLATE_THEME"].'/colors.css'))
	$APPLICATION->SetAdditionalCSS($this->GetFolder().'/themes/'.$arParams["TEMPLATE_THEME"].'/colors.css');

?>
<a href class="bj-catalogue-filter-switch"><?=GetMessage("CT_BCSF_FILTER_USE_FILTER")?></a>

<div class="bj-catalogue-filter">
	<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get">
		<?foreach($arResult["HIDDEN"] as $arItem):?>
			<input
				type="hidden"
				name="<?echo $arItem["CONTROL_NAME"]?>"
				id="<?echo $arItem["CONTROL_ID"]?>"
				value="<?echo $arItem["HTML_VALUE"]?>"
			/>
		<?endforeach;?>
		<?foreach($arResult["ITEMS"] as $key=>$arItem):
			$key = md5($key);
			?>
			<?if(isset($arItem["PRICE"])):?>
				<?
				if (!$arItem["VALUES"]["MIN"]["VALUE"] || !$arItem["VALUES"]["MAX"]["VALUE"] || $arItem["VALUES"]["MIN"]["VALUE"] == $arItem["VALUES"]["MAX"]["VALUE"])
					continue;
				$step = (($arItem["VALUES"]["MAX"]["VALUE"]-$arItem["VALUES"]["MIN"]["VALUE"])/100);
				?>
				<div class="form-group bj-range bj-hidden<?=(($_REQUEST[$arItem["VALUES"]["MAX"]["CONTROL_NAME"]] > 0 && isset($_REQUEST['set_filter'])) ? " i-open" : "")?>">
					<label class="h3 bj-hidden-link<?=(($_REQUEST[$arItem["VALUES"]["MAX"]["CONTROL_NAME"]] > 0 && isset($_REQUEST['set_filter'])) ? " i-up" : "")?>"><?=$arItem["NAME"]?></label>
					<div class="bj-hidden__hidden">
						<div class="bj-range__value"><?=GetMessage("CT_BCSF_FILTER_FROM")?> <span class="bj-range__min"><?=($arItem["VALUES"]["MIN"]["HTML_VALUE"] ? floor($arItem["VALUES"]["MIN"]["HTML_VALUE"]) : floor($arItem["VALUES"]["MIN"]["VALUE"]))?></span> <?=GetMessage("CT_BCSF_FILTER_TO")?> <span class="bj-range__max"><?=($arItem["VALUES"]["MAX"]["HTML_VALUE"] ? floor($arItem["VALUES"]["MAX"]["HTML_VALUE"]) : floor($arItem["VALUES"]["MAX"]["VALUE"]))?></span> <?=$arItem["CURRENCIES"]["RUB"]?></div>
						<input type="range" name="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>" max="<?=floor($arItem["VALUES"]["MAX"]["VALUE"])?>" min="<?=floor($arItem["VALUES"]["MIN"]["VALUE"])?>" step="<?=$step?>" value="<?=($arItem["VALUES"]["MIN"]["HTML_VALUE"] ? floor($arItem["VALUES"]["MIN"]["HTML_VALUE"]) : floor($arItem["VALUES"]["MIN"]["VALUE"]))?>" class="bj-range__input-min">
						<input type="range" name="<?=$arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>" min="<?=floor($arItem["VALUES"]["MIN"]["VALUE"])?>" max="<?=floor($arItem["VALUES"]["MAX"]["VALUE"])?>" step="<?=$step?>" value="<?=($arItem["VALUES"]["MAX"]["HTML_VALUE"] ? floor($arItem["VALUES"]["MAX"]["HTML_VALUE"]) : floor($arItem["VALUES"]["MAX"]["VALUE"]))?>" class="bj-range__input-max">
						<div class="bj-range__slider"></div>
					</div>
				</div>
			<?endif?>
		<?endforeach?>
		<?foreach($arResult["ITEMS"] as $key=>$arItem):?>
			<?if($arItem["PROPERTY_TYPE"] == "N" ):?>
				<?
				if (!$arItem["VALUES"]["MIN"]["VALUE"] || !$arItem["VALUES"]["MAX"]["VALUE"] || $arItem["VALUES"]["MIN"]["VALUE"] == $arItem["VALUES"]["MAX"]["VALUE"])
					continue;
				?>
				<div class="form-group bj-range bj-hidden<?=(($_REQUEST[$arItem["VALUES"]["MAX"]["CONTROL_NAME"]] > 0 && isset($_REQUEST['set_filter'])) ? " i-open" : "")?>">
					<label class="h3 bj-hidden-link<?=(($_REQUEST[$arItem["VALUES"]["MAX"]["CONTROL_NAME"]] > 0 && isset($_REQUEST['set_filter'])) ? " i-up" : "")?>"><?=$arItem["NAME"]?></label>
					<div class="bj-hidden__hidden">
						<div class="bj-range__value"><?=GetMessage("CT_BCSF_FILTER_FROM")?> <span class="bj-range__min"><?echo floor($arItem["VALUES"]["MIN"]["VALUE"])?></span> <?=GetMessage("CT_BCSF_FILTER_TO")?> <span class="bj-range__max"><?echo floor($arItem["VALUES"]["MAX"]["VALUE"])?></span> <?=$arItem["CURRENCIES"]["RUB"]?></div>
						<input type="range" name="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>" max="<?=$arItem["VALUES"]["MAX"]["VALUE"]?>" min="<?=$arItem["VALUES"]["MIN"]["VALUE"]?>" step="<?=$step?>" value="<?=$arItem["VALUES"]["MIN"]["HTML_VALUE"]?>" class="bj-range__input-min">
						<input type="range" name="<?=$arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>" min="<?=$arItem["VALUES"]["MIN"]["VALUE"]?>" max="<?=$arItem["VALUES"]["MAX"]["VALUE"]?>" step="<?=$step?>" value="<?=$arItem["VALUES"]["MAX"]["HTML_VALUE"]?>" class="bj-range__input-max">
						<div class="bj-range__slider"></div>
					</div>
				</div>
			<?elseif($arItem["PROPERTY_TYPE"] == "L" && !empty($arItem["VALUES"])):?>
			<div class="form-group bj-hidden<?=((isset($arFilter["OFFERS"]["=PROPERTY_".$key]) && isset($_REQUEST['set_filter'])) ? " i-open" : "")?>">
				<label class="h3 bj-hidden-link<?=((isset($arFilter["OFFERS"]["=PROPERTY_".$key]) && isset($_REQUEST['set_filter'])) ? " i-up" : "")?>"><?=$arItem["NAME"]?></label>
				<div class="bj-hidden__hidden">
				<?foreach($arItem["VALUES"] as $val => $ar):?>
				<label class="bj-cf__size__i bj-checkbox">
					<span class="bj-checkbox__u"></span>
					<input type="checkbox" name="<?echo $ar["CONTROL_NAME"]?>" value="<?echo $ar["HTML_VALUE"]?>" <?echo $ar["CHECKED"]? 'checked="checked"': ''?>>
					<?echo $ar["VALUE"];?>
				</label>
				<?endforeach;?>
				</div>
			</div>
			<?elseif($arItem["PROPERTY_TYPE"] == "S" && !empty($arItem["VALUES"]) && $arItem["CODE"] == "COLOR_REF"):?>
			<div class="form-group bj-cf__colors bj-hidden<?=((isset($arFilter["OFFERS"]["=PROPERTY_".$key]) && isset($_REQUEST['set_filter'])) ? " i-open" : "")?>">
			<label class="h3 bj-hidden-link<?=((isset($arFilter["OFFERS"]["=PROPERTY_".$key]) && isset($_REQUEST['set_filter'])) ? " i-up" : "")?>"><?=$arItem["NAME"]?></label>
			<div class="bj-hidden__hidden">
			<?foreach($arItem["VALUES"] as $val => $ar):?>
			<label class="bj-cf__colors__i bj-checkbox" style="background-image:url('/upload/<?=$ar["IMAGE"]["SUBDIR"]?>/<?=$ar["IMAGE"]["FILE_NAME"]?>');" title="<?=$ar["VALUE"]?>"></span>
				<span class="bj-checkbox__u"></span>
				<input type="checkbox" name="<?echo $ar["CONTROL_NAME"]?>" value="<?echo $ar["HTML_VALUE"]?>" <?echo $ar["CHECKED"]? 'checked="checked"': ''?>>
			</label>
			<?endforeach;?>
			<div class="clearfix"></div>
			</div>
			</div>
			<?elseif(!empty($arItem["VALUES"]) && !isset($arItem["PRICE"])):?>
			<div class="form-group bj-hidden<?=((isset($arFilter["=PROPERTY_".$key]) && isset($_REQUEST['set_filter'])) ? " i-open" : "")?>">
				<label class="h3 bj-hidden-link<?=((isset($arFilter["=PROPERTY_".$key]) && isset($_REQUEST['set_filter'])) ? " i-up" : "")?>"><?=$arItem["NAME"]?></label>
				<div class="bj-hidden__hidden">
				<?foreach($arItem["VALUES"] as $val => $ar):?>
				<div class="checkbox">
					<label><input type="checkbox" name="<?echo $ar["CONTROL_NAME"]?>" value="<?echo $ar["HTML_VALUE"]?>" <?echo $ar["CHECKED"]? 'checked="checked"': ''?>> <?echo $ar["VALUE"];?></label>
				</div>
				<?endforeach;?>
				</div>
			</div>
			<?endif;?>
		<?endforeach;?>

		<button type="submit" class="btn btn-default" name="set_filter" value="<?=GetMessage("CT_BCSF_SET_FILTER")?>"><?=GetMessage("CT_BCSF_SET_FILTER")?></button>
		<input class="btn bj-reset-button" type="submit" id="del_filter" name="del_filter" value="" title="<?=GetMessage("CT_BCSF_DEL_FILTER")?>">
	</form>
</div>
<script type="text/javascript">
BX.message({
	BASKET_FROM: '<? echo GetMessageJS('CT_BCSF_FILTER_FROM'); ?>',
	BASKET_TO: '<? echo GetMessageJS('CT_BCSF_FILTER_TO'); ?>'
});
</script>