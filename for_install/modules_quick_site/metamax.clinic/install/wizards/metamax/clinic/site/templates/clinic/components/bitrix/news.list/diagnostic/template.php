<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<h2><?=$arResult["SECTION"]["PATH"][0]["NAME"]?></h2>
<p><?=$arResult["SECTION"]["PATH"][0]["DESCRIPTION"]?></p>
<br />

<?if(count($arResult["ITEMS"])>0):?>

<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>

<table cellspacing="0" cellpadding="0" class="diagnostic-table">
<tr>
	<th><?=GetMessage("DIAGNOSTIC_TITLE")?></th>
	<?foreach($arParams["PROPERTY_CODE"] as $code):?>
		<th><?=$arResult["ITEMS"][0]["PROPERTIES"][$code]["NAME"]?></th>
	<?endforeach;?>
</tr>

<?foreach($arResult["ITEMS"] as $id=>$arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
<tr id="<?=$this->GetEditAreaId($arItem['ID']);?>"<?if($id%2!=0):?> class="dark"<?endif?>>
<td>
		<div class="name"><?=$arItem["NAME"]?></div>
		<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
			<div><?=$arItem["PREVIEW_TEXT"];?></div>
		<?endif;?>
</td>
		<?foreach($arItem["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
			<td align="center">
			<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
				<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
			<?else:?>
				<?=$arProperty["DISPLAY_VALUE"];?>
			<?endif?>
			</td>
		<?endforeach;?>
</tr>
<?endforeach;?>
</table>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>

<?endif;?>