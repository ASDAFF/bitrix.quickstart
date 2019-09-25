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

//bxslider plugin
$APPLICATION->SetAdditionalCSS($this->GetFolder()."/bxslider/jquery.bxslider.css");
$APPLICATION->AddHeadScript($this->GetFolder()."/bxslider/jquery.bxslider.min.js");

$this->setFrameMode(true);
?>
<ul ын class="slider-list">
	<?
	foreach($arResult["ITEMS"] as $arItem)
	{  
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		?>
		<li class="slider-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
			<?
			if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] && is_array($arItem["DETAIL_PICTURE"]))
			{
				$urlSlider = str_replace("#SITE_DIR#",SITE_DIR,$arItem["PROPERTIES"]["LINK"]["VALUE"]);
             if(!$urlSlider)
             {
                $urlSlider = $arItem["PROPERTIES"]["LINK"]["VALUE"];
             }
				?>
				<a class="slider-item-link"href="<?=$urlSlider?>">
					<img  border="0" src="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arItem["DETAIL_PICTURE"]["ALT"]?>" title="<?=$arItem["DETAIL_PICTURE"]["TITLE"]?>"/>
				</a>
				<?
			}
			?>
		</li>
	<?
	}
	?>
</ul>
<div style="margin-bottom: 15px;"></div>
