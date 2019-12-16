<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//******************************************
//subscription authorization form
//******************************************
?>

<form class="col-xs-12 col-sm-6" action="<?echo $arResult["FORM_ACTION"].($_SERVER["QUERY_STRING"]<>""? "?".htmlspecialcharsbx($_SERVER["QUERY_STRING"]):"")?>" method="post">
    <h2><?echo GetMessage("subscr_auth_sect_title")?></h2>
    <p><?echo GetMessage("adm_auth_note")?></p>
    <div class="form-group">
        <input class="form-control" type="text" name="sf_EMAIL" size="20" value="<?echo $arResult["REQUEST"]["EMAIL"];?>" title="<?echo GetMessage("subscr_auth_email")?>" placeholder="E-mail">
    </div>
    <div class="form-group">
        <input class="form-control" type="password" name="AUTH_PASS" size="20" value="" title="<?echo GetMessage("subscr_auth_pass_title")?>" placeholder="<?echo GetMessage("subscr_auth_pass")?>">
    </div>
    <input class="btn btn1" type="submit" name="autorize" value="<?echo GetMessage("adm_auth_butt")?>">
<input type="hidden" name="action" value="authorize" />
<?echo bitrix_sessid_post();?>
</form>

<form class="col-xs-12 col-sm-6" action="<?=$arResult["FORM_ACTION"]?>">
    <h2><?echo GetMessage("subscr_pass_title")?></h2>
    <p><?echo GetMessage("subscr_pass_note")?></p>
    <div class="form-group">
        <input class="form-control" type="text" name="sf_EMAIL" size="20" value="<?echo $arResult["REQUEST"]["EMAIL"];?>" title="<?echo GetMessage("subscr_auth_email")?>" placeholder="E-mail">
    </div>
    <input class="btn btn1" type="submit" name="sendpassword" value="<?echo GetMessage("subscr_pass_button")?>">
<input type="hidden" name="action" value="sendpassword" />
<?echo bitrix_sessid_post();?>
</form>
