<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<h2><?=$arResult["SECTION"]["PATH"][0]["NAME"]?></h2>
<p><?=$arResult["SECTION"]["PATH"][0]["DESCRIPTION"]?></p>
<br />

<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>

<table cellspacing="0" cellpadding="0" class="doctors-table" width="100%">
<?foreach($arResult["ITEMS"] as $id=>$arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
<tr id="<?=$this->GetEditAreaId($arItem['ID']);?>"<?if($id%2==0):?> class="dark"<?endif?>>
<td valign="top" align="center">
	<img border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" />
</td>
<td valign="top">
		<div class="name"><?=$arItem["NAME"]?></div>
		<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
			<div><?=$arItem["PREVIEW_TEXT"];?></div>
		<?endif;?>
		
		<?foreach($arItem["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
			<p><?=$arProperty["NAME"]?>:<?if($pid=="TIME"):?><br /><?endif;?>
			<b><?if(is_array($arProperty["DISPLAY_VALUE"])):?>
				<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
			<?else:?>
				<?=str_replace("\r\n", "<br />", $arProperty["DISPLAY_VALUE"]);?>
			<?endif?> <?if($pid=="PRICE"):?><?=GetMessage("DOCTORS_CURRENCY")?><?endif;?></b>
			</p>
		<?endforeach;?>
		
		<div class="visit"><a href="<?=SITE_DIR?>visit/?ID=<?=$arItem["ID"]?>"><?=GetMessage("DOCTORS_VISIT")?></a></div>
</td>
</tr>
<?endforeach;?>
</table>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
