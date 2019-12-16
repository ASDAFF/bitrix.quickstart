<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//***********************************
//setting section
//***********************************
?>
<form class="col-xs-12 col-sm-6" action="<?=$arResult["FORM_ACTION"]?>" method="post">
<?echo bitrix_sessid_post();?>
<div class="panel">
<div class="panel__head">
    <h2 class="panel__name"><?echo GetMessage("subscr_title_settings")?></h2>
</div>
<div class="panel__body">
    <div class="form-group">
        <input class="form-control" type="text" name="EMAIL" value="<?=$arResult["SUBSCRIPTION"]["EMAIL"]!=""?$arResult["SUBSCRIPTION"]["EMAIL"]:$arResult["REQUEST"]["EMAIL"];?>" size="30" maxlength="255" placeholder="<?echo GetMessage("subscr_email")?>*">
    </div>
    
    <h3><?echo GetMessage("subscr_rub")?></h3>
    <div class="form-group">
        <?foreach($arResult["RUBRICS"] as $itemID => $itemValue):?>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="RUB_ID[]" value="<?=$itemValue["ID"]?>"<?if($itemValue["CHECKED"]) echo " checked"?>>
                    <svg class="checkbox__icon icon-check icon-svg"><use xlink:href="#svg-check"></use></svg><?=$itemValue["NAME"]?>
                </label>
            </div>
        <?endforeach;?></p>
    </div>
    
    <h3><?echo GetMessage("subscr_fmt")?></h3>
    <div class="form-group">
        <div class="radio radio-inline">
            <label>
                <input type="radio" name="FORMAT" value="text"<?if($arResult["SUBSCRIPTION"]["FORMAT"] == "text") echo " checked"?>>
                <i class="radio__icon"></i><?echo GetMessage("subscr_text")?>
            </label>
        </div>
        <div class="radio radio-inline">
            <label>
                <input type="radio" name="FORMAT" value="html"<?if($arResult["SUBSCRIPTION"]["FORMAT"] == "html") echo " checked"?>>
                <i class="radio__icon"></i>HTML
            </label>
        </div>
    </div>
	<p><?echo GetMessage("subscr_settings_note1")?></p>
    <p><?echo GetMessage("subscr_settings_note2")?></p>
</div>
</div>
<input type="hidden" name="PostAction" value="<?echo ($arResult["ID"]>0? "Update":"Add")?>" />
<input type="hidden" name="ID" value="<?echo $arResult["SUBSCRIPTION"]["ID"];?>" />
<?if($_REQUEST["register"] == "YES"):?>
	<input type="hidden" name="register" value="YES" />
<?endif;?>
<?if($_REQUEST["authorize"]=="YES"):?>
	<input type="hidden" name="authorize" value="YES" />
<?endif;?>
    <p>
        <input class="btn btn1" type="submit" name="Save" value="<?echo ($arResult["ID"] > 0? GetMessage("subscr_upd"):GetMessage("subscr_add"))?>">
        <input class="btn btn3" type="reset" value="<?echo GetMessage("subscr_reset")?>" name="reset">
    </p>
</form>