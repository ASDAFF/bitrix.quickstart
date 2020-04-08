<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
//******************************************
//subscription authorization form
//******************************************
?>

<form action="<?echo $arResult["FORM_ACTION"].($_SERVER["QUERY_STRING"]<>""? "?".htmlspecialcharsbx($_SERVER["QUERY_STRING"]):"")?>" method="post">

	<h2><?=GetMessage("subscr_auth_sect_title")?></h2>
	<p><?=GetMessage("adm_auth_note")?></p>
	
	<label>e-mail</label>
	<div class="field">
		<input type="email" name="sf_EMAIL" value="<?=$arResult["REQUEST"]["EMAIL"];?>" title="<?=GetMessage("subscr_auth_email")?>" class="inputtext" />
	</div>
	
	<label><?=GetMessage("subscr_auth_pass")?></label>
	<div class="field">
		<input type="password" name="AUTH_PASS" value="" title="<?=GetMessage("subscr_auth_pass_title")?>" class="inputtext" />
	</div>
	
	<div class="form_footer">
		<input type="submit" name="autorize" value="<?=GetMessage("adm_auth_butt")?>" class="btn" />
	</div>

	<input type="hidden" name="action" value="authorize" />
	<?echo bitrix_sessid_post();?>
	
</form>


<form action="<?=$arResult["FORM_ACTION"]?>">

	<h2><?=GetMessage("subscr_pass_title")?></h2>
	<p><?=GetMessage("subscr_pass_note")?></p>
	
	<label>e-mail</label>
	<div class="field">
		<input type="email" name="sf_EMAIL" value="<?=$arResult["REQUEST"]["EMAIL"];?>" title="<?=GetMessage("subscr_auth_email")?>" class="inputtext" />
	</div>
	
	<div class="form_footer">
		<input type="submit" name="sendpassword" value="<?echo GetMessage("subscr_pass_button")?>" />
	</div>

	<input type="hidden" name="action" value="sendpassword" />
	<?echo bitrix_sessid_post();?>
</form>
