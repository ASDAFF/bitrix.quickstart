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

if(!empty($arResult['ITEMS'])):
	$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
	$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
	$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));
?>
<ul>
	<?foreach ($arResult['ITEMS'] as $key => $arItem):
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
	?>
	<li data-transition="<?=$arItem["PROPERTIES"]["TRANSITION"]["VALUE"]?>" 
		data-slotamount="7" 
		data-masterspeed="500" 
		data-saveperformance="on" id="<?=$this->GetEditAreaId($arItem['ID'])?>">

		<img src="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>"
		<?if(!empty($arItem["PROPERTIES"]["KENBURNS"]["VALUE"])):?>
			data-kenburns="on" 
			data-duration="10000" 
			data-ease="Linear.easeNone" 
			data-bgfit="100" 
			data-bgfitend="115" 
			data-bgpositionend="right center"<?else:?>
			data-bgposition="center top"
			data-bgfit="cover"
			data-bgrepeat="no-repeat"<?endif?>>
		<?if(!empty($arItem["PROPERTIES"]["DARK_BG"]["VALUE"])):?><div class="tp-caption dark-translucent-bg" data-x="center" data-y="bottom" data-speed="800" data-start="0" style="background-color:rgba(0,0,0,0.5);"></div><?endif?>
		
		<?if(!empty($arItem["PROPERTIES"]["HEADER"]["VALUE"])):?>
			<div class="tp-caption tp-resizeme <?=$arItem["PROPERTIES"]["HEADER_ANIMATION"]["VALUE"]?> <?=$arItem["PROPERTIES"]["HEADER_FONT"]["VALUE"]?> <?=$arItem["PROPERTIES"]["HEADER_BG"]["VALUE"]?>" 
				data-x="<?=$arItem["PROPERTIES"]["HEADER_X"]["VALUE"]?>" 
				data-y="<?=$arItem["PROPERTIES"]["HEADER_Y"]["VALUE"]?>" 
				data-start="<?=$arItem["PROPERTIES"]["HEADER_START"]["VALUE"]?>"
				data-speed="<?=$arItem["PROPERTIES"]["HEADER_SPEED"]["VALUE"]?>"
				data-endspeed="<?=$arItem["PROPERTIES"]["HEADER_ENDSPEED"]["VALUE"]?>">
			<?=$arItem["PROPERTIES"]["HEADER"]["VALUE"]?>
			</div>
		<?endif?>
		<?if(!empty($arItem["PROPERTIES"]["DESCRIPTION"]["VALUE"])):?>
			<div class="tp-caption tp-resizeme <?=$arItem["PROPERTIES"]["DESCRIPTION_ANIMATION"]["VALUE"]?> <?=$arItem["PROPERTIES"]["DESCRIPTION_FONT"]["VALUE"]?> <?=$arItem["PROPERTIES"]["DESCRIPTION_BG"]["VALUE"]?>" 
				data-x="<?=$arItem["PROPERTIES"]["DESCRIPTION_X"]["VALUE"]?>"
				data-y="<?=$arItem["PROPERTIES"]["DESCRIPTION_Y"]["VALUE"]?>"
				data-start="<?=$arItem["PROPERTIES"]["DESCRIPTION_START"]["VALUE"]?>"
				data-speed="<?=$arItem["PROPERTIES"]["DESCRIPTION_SPEED"]["VALUE"]?>"
				data-endspeed="<?=$arItem["PROPERTIES"]["DESCRIPTION_ENDSPEED"]["VALUE"]?>">
			<?=$arItem["PROPERTIES"]["DESCRIPTION"]["VALUE"]?>
			</div>
		<?endif?>
		
		<?if(!empty($arItem["PROPERTIES"]["SHOW_BUTTON_1"]["VALUE"])):?>
			<div class="tp-caption tp-resizeme <?=$arItem["PROPERTIES"]["SHOW_BUTTON_1_ANIMATION"]["VALUE"]?>" 
				data-x="<?=$arItem["PROPERTIES"]["SHOW_BUTTON_1_X"]["VALUE"]?>" 
				data-y="<?=$arItem["PROPERTIES"]["SHOW_BUTTON_1_Y"]["VALUE"]?>" 
				data-start="<?=$arItem["PROPERTIES"]["SHOW_BUTTON_1_START"]["VALUE"]?>"
				data-speed="<?=$arItem["PROPERTIES"]["SHOW_BUTTON_1_SPEED"]["VALUE"]?>"
				data-endspeed="<?=$arItem["PROPERTIES"]["SHOW_BUTTON_1_ENDSPEED"]["VALUE"]?>">
			<a href="<?=$arItem["PROPERTIES"]["HREF_BUTTON_1"]["VALUE"]?>" class="btn <?=$arItem["PROPERTIES"]["SHOW_BUTTON_1_SIZE"]["VALUE"]?> <?=$arItem["PROPERTIES"]["SHOW_BUTTON_1_COLOR"]["VALUE"]?>"><?=$arItem["PROPERTIES"]["TEXT_BUTTON_1"]["VALUE"]?><i class="fa fa-angle-double-right pl-10"></i></a>
			</div>
		<?endif?>
		<?if(!empty($arItem["PROPERTIES"]["SHOW_BUTTON_2"]["VALUE"])):?>
			<div class="tp-caption tp-resizeme <?=$arItem["PROPERTIES"]["SHOW_BUTTON_2_ANIMATION"]["VALUE"]?>" 
				data-x="<?=$arItem["PROPERTIES"]["SHOW_BUTTON_2_X"]["VALUE"]?>" 
				data-y="<?=$arItem["PROPERTIES"]["SHOW_BUTTON_2_Y"]["VALUE"]?>" 
				data-start="<?=$arItem["PROPERTIES"]["SHOW_BUTTON_2_START"]["VALUE"]?>"
				data-speed="<?=$arItem["PROPERTIES"]["SHOW_BUTTON_2_SPEED"]["VALUE"]?>"
				data-endspeed="<?=$arItem["PROPERTIES"]["SHOW_BUTTON_2_ENDSPEED"]["VALUE"]?>">
			<a href="<?=$arItem["PROPERTIES"]["HREF_BUTTON_2"]["VALUE"]?>" class="btn <?=$arItem["PROPERTIES"]["SHOW_BUTTON_2_SIZE"]["VALUE"]?> <?=$arItem["PROPERTIES"]["SHOW_BUTTON_2_COLOR"]["VALUE"]?>"><?=$arItem["PROPERTIES"]["TEXT_BUTTON_2"]["VALUE"]?><i class="fa fa-angle-double-right pl-10"></i></a>
			</div>
		<?endif?>

		<?if(!empty($arItem["PROPERTIES"]["HREF_VIDEO"]["VALUE"])):?>
			<div class="tp-caption <?=$arItem["PROPERTIES"]["HREF_VIDEO_ANIMATION"]["VALUE"]?>"
				data-x="<?=$arItem["PROPERTIES"]["HREF_VIDEO_X"]["VALUE"]?>"
				data-y="<?=$arItem["PROPERTIES"]["HREF_VIDEO_Y"]["VALUE"]?>"
				data-start="<?=$arItem["PROPERTIES"]["HREF_VIDEO_START"]["VALUE"]?>"
				data-speed="<?=$arItem["PROPERTIES"]["HREF_VIDEO_SPEED"]["VALUE"]?>"
				data-endspeed="<?=$arItem["PROPERTIES"]["HREF_VIDEO_ENDSPEED"]["VALUE"]?>"
				data-hoffset="-660"
				data-autoplay="false"
				data-autoplayonlyfirsttime="false"
				data-nextslideatend="true">
				<div class="embed-responsive embed-responsive-16by9">
					<iframe class="embed-responsive-item" src='<?=$arItem["PROPERTIES"]["HREF_VIDEO"]["VALUE"]?>?rel=0&hd=1&showinfo=0&color=white&html5=1' style='width:<?=$arItem["PROPERTIES"]["HREF_VIDEO_WIDTH"]["VALUE"]?>px; height:<?=$arItem["PROPERTIES"]["HREF_VIDEO_HEIGHT"]["VALUE"]?>px;'></iframe>
				</div>
			</div>
		<?endif?>

	</li>
	<?endforeach?>
</ul>
<div class="tp-bannertimer tp-bottom"></div>
<?endif?>