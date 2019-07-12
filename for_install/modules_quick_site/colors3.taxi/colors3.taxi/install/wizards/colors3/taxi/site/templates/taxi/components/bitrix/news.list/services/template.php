<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<h1><?=$APPLICATION->GetTitle('title')?></h1>

<?if (!count($arResult["ITEMS"])):?>
	<?echo GetMessage("DEFAULT_INFO");?>
<?else:?>

<?foreach($arResult["ITEMS"] as $key=>$arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div id="<?=$this->GetEditAreaId($arItem['ID']);?>" class="span6 serv<?if($key%2 == 0):?> f<?endif;?>">
		<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
			<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
				<a title="<?=$arItem["NAME"]?>" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img class="preview_picture" border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["NAME"]?>" /></a>
			<?else:?>
				<img class="preview_picture" border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["NAME"]?>" />
			<?endif;?>
		<?endif?>

		<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"] && $arItem["PREVIEW_TEXT"] && $arItem["DETAIL_TEXT"]):?>
			<h3>
				<a title="<?=$arItem["NAME"]?>" href="<?=$arItem["DETAIL_PAGE_URL"]?>">
					<span><?echo $arItem["NAME"]?></span>
				</a>
			</h3>
		<?elseif (($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"] && $arItem["PREVIEW_TEXT"]) or ($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"] &&$arItem["DETAIL_TEXT"])):?>
			<h3>
				<?echo $arItem["NAME"]?>
			</h3>
		<?endif;?>

		<?if($arItem["PREVIEW_TEXT"]):?>
			<?echo $arItem["PREVIEW_TEXT"]?>
		<?elseif($arItem["DETAIL_TEXT"]):?>
			<?echo $arItem["DETAIL_TEXT"]?>
		<?endif;?>
	</div>
<?endforeach;?>

<?endif;?>

<style type="text/css">
	#content .in .span6 {
		margin-bottom: 20px;
	}
	#content .in .span6.f {
		margin-left: 0;
	}
	#content .in .span6 h3 {
	    font-family: 'PT Sans Narrow','Arial Narrow',Arial,Helvetica,FreeSans,"Liberation Sans","Nimbus Sans L",sans-serif;
	    line-height: 1;
	    margin-bottom: 11px;
	    text-transform: none;
	}
	#content .in .span6 h3 a, #content .in .span6 h3 a span{
		font-size: inherit;
		font-family: inherit;
	}

</style>