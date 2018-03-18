<?
if(!check_bitrix_sessid()) return;
/** @var CMain $APPLICATION */
IncludeModuleLangFile(__FILE__);
?>
<hr>
<h2><?=CAdminMessage::ShowNote(GetMessage('SELECT_TARGET_SITE'));?></h2>
<form action="<?echo $APPLICATION->GetCurPage()?>" method="post">
	<?=bitrix_sessid_post()?>
    <input type="hidden" name="lang" value="<?echo LANG?>">
    <input type="hidden" name="id" value="api.feedback">
    <input type="hidden" name="install" value="Y">
    <input type="hidden" name="step" value="2">
	<select name="API_SITE_ID[]" size="<?=count($arSites);?>" multiple="multiple">
			<?foreach($arSites as $siteId=>$arSite):?>
				<option value="<?=$arSite["ID"]?>">[<?=$arSite["ID"]?>] <?=$arSite["NAME"]?></option>
			<?endforeach?>
	</select>
	<br><br>
	<input type="submit" name="inst" value="<?echo GetMessage("MOD_INSTALL")?>">
</form>
