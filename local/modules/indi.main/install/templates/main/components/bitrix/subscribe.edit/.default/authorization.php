<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//*************************************
//show current authorization section
//*************************************
?>

<form action="<?=$arResult["FORM_ACTION"]?>" method="post">
	
	<?echo bitrix_sessid_post();?>
	
	<h2><?=GetMessage("subscr_title_auth")?></h2>
	
	<?if($arResult["ID"]==0):?>
		<p><?=GetMessage("subscr_auth_logout1")?> <a href="<?=$arResult["FORM_ACTION"]?>?logout=YES&amp;sf_EMAIL=<?=$arResult["REQUEST"]["EMAIL"]?><?=$arResult["REQUEST"]["RUBRICS_PARAM"]?>"><?=GetMessage("adm_auth_logout")?></a><?=GetMessage("subscr_auth_logout2")?></p>
	<?else:?>
		<p><?=GetMessage("subscr_auth_logout3")?> <a href="<?=$arResult["FORM_ACTION"]?>?logout=YES&amp;sf_EMAIL=<?=$arResult["REQUEST"]["EMAIL"]?><?=$arResult["REQUEST"]["RUBRICS_PARAM"]?>"><?=GetMessage("adm_auth_logout")?></a><?=GetMessage("subscr_auth_logout4")?></p>
	<?endif;?>
	
	<div class="field">
		<?=GetMessage("adm_auth_user")?>
		<?=htmlspecialcharsbx($USER->GetFormattedName(false));?> [<?=htmlspecialcharsbx($USER->GetLogin())?>].
	</div>
	
</form>
