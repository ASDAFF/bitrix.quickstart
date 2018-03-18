<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="qustions-list">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<p id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<div class="question">
		<div class="box-c"> <em class="ctl"><b>&bull;</b></em> <em class="ctr"><b>&bull;</b></em></div> 
		<div class="box-inner">
			<div><?=$arItem["PREVIEW_TEXT"];?></div>
			<div class="news-date-time"><?=$arItem["NAME"]?>, <?=$arItem["DISPLAY_ACTIVE_FROM"]?></div>
		</div>
		<div class="box-c"><em class="cbl"><b>&bull;</b></em><em class="cbr"><b>&bull;</b></em></div>
		</div>
		
		<?if($arItem["FIELDS"]["DETAIL_TEXT"]):?>
			<div class="answer"><?=$arItem["FIELDS"]["DETAIL_TEXT"];?></div>
		<?endif;?>
		
		<?foreach($arItem["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
			<small>
			<?=$arProperty["NAME"]?>:&nbsp;
			<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
				<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
			<?else:?>
				<?=$arProperty["DISPLAY_VALUE"];?>
			<?endif?>
			</small><br />
		<?endforeach;?>
	</p>
<?endforeach;?>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
</div>
