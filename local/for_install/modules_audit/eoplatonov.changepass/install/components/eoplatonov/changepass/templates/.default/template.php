<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="changepass">
    <?if($_POST[changepass]=="Y") $APPLICATION->RestartBuffer();?>
    <?if($arResult[IS_AUTHORIZED]){?>
        <div class="changepass_caption"><?=GetMessage("CHPASS_CAPTION");?></div>
        <div class="changepass_answer"><?=$arResult[ANSWER]?></div>
        <form method="POST">
            <?=bitrix_sessid_post();?>
            <input type="hidden" name="changepass" value="Y"/>
            <?if($arParams[LAST_PASS]=="Y"){?>
            <div class="changepass_input_box">
                <div class="changepass_name_field changepass_last_pass"><?=GetMessage("CHPASS_LAST_PASS");?></div>
                <input type="password" name="last_pass" />
                <div class="clear"></div>
            </div>
            <?}?>
            <div class="changepass_input_box">
                <div class="changepass_name_field changepass_new_pass"><?=GetMessage("CHPASS_NEW_PASS");?></div>
                <input type="password" name="new_pass" />
                <div class="clear"></div>
            </div>
            <div class="changepass_input_box">
                <div class="changepass_name_field"><?=GetMessage("CHPASS_NEW_PASS2");?></div>
                <input type="password" name="new_pass2" />
                <div class="clear"></div>
            </div>
            <input type="submit" value="<?=GetMessage("CHPASS_SUBMIT");?>" />
            <div class="clear"></div>
        </form>
    <?}else print GetMessage("CHPASS_NEED_AUTH");?>
    <?if($_POST[changepass]=="Y") die();?>
</div>