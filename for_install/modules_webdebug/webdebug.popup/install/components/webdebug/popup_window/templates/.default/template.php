<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if($arResult["FILE"]!=''):?>
	<?if ($arParams['POPUP_LINK_SHOW']!='N'):?>
		<a href="#" id="<?=$arResult['POPUP_LINK_ID']?>"<?if($arParams['POPUP_LINK_HIDDEN']=='Y'):?> style="display:none"<?endif?>><?=$arParams['POPUP_LINK_TEXT']?></a>
	<?endif?>
	<?=CWD_Popup::BeginEx($arResult['POPUP_PARAMS']);?>
	<?include($arResult["FILE"]);?>
	<?=CWD_Popup::EndEx();?>
<?endif?>