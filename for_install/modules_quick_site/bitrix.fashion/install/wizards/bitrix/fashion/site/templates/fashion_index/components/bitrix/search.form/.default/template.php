<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="searchform">
<form action="<?=$arResult["FORM_ACTION"]?>">
    <p>
        <label for="s"><?=GetMessage("PLACEHOLDER");?></label>
        <?if($arParams["USE_SUGGEST"] === "Y"):?><?$APPLICATION->IncludeComponent(
            "bitrix:search.suggest.input",
            "",
            array(
                "NAME" => "q",
                "VALUE" => "",
                "INPUT_SIZE" => 15,
                "DROPDOWN_SIZE" => 10,
            ),
            $component, array("HIDE_ICONS" => "Y")
        );?>
        <?else:?>
        <input type="text" name="q" id="s" class="hide-label" value="" maxlength="50" />
        <?endif;?>
        <input name="s" type="submit" value="<?=GetMessage("BSF_T_SEARCH_BUTTON");?>" />
    </p>
</form>
</div>