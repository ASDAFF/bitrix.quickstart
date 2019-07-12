<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if($arResult["ERRORS"]) {
	$StrError = implode("<br>",$arResult["ERRORS"]);
	echo ShowError($StrError);
}
?>

<?if(!$USER->IsAuthorized()):?>
		<script src="http://loginza.ru/js/widget.js" type="text/javascript"></script>
		<a href="<?=$arResult['WIDGET_URL']?>&providers_set=<?=implode(",", $arParams["PROVIDERS_SET"])?>&lang=<?=$arParams["LANG"]?><?if($arParams["PROVIDER"] != ""):?>&provider=<?=$arParams["PROVIDER"]?><?endif?>&mobile=auto" class="loginza">
		<?if($arParams["TEXT_LINK"] != "" and $arParams["IMAGE_LINK"] == ""):?>
			<?=$arParams["TEXT_LINK"]?>
		<?elseif($arParams["TEXT_LINK"] == "" and $arParams["IMAGE_LINK"] != ""):?>
			<img src="<?=$arParams["IMAGE_LINK"]?>" alt=""/>
		<?elseif($arParams["TEXT_LINK"] != "" and $arParams["IMAGE_LINK"] != ""):?>
			<img src="<?=$arParams["IMAGE_LINK"]?>" alt="<?=$arParams["TEXT_LINK"]?>"/>
		<?else:?>
			<img src="http://loginza.ru/img/sign_in_button_gray.gif" alt=""/>
		<?endif;?>
		</a>
<?else:?>
	<?
	$current_hi= COption::GetOptionString('infospice.loginzapro', 'hi', '');
	if(empty($current_hi)){
		$current_hi = "Hello";
	}
	?>
	<?=$current_hi?>, <strong><?=$USER->GetLogin()?></strong>! <a href="<?=$APPLICATION->GetCurPageParam("logout=yes", array("logout"))?>">Exit</a>
<?endif;?>