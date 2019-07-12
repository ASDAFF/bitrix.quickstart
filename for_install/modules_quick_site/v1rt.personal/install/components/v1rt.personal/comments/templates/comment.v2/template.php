<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<?if(count($arResult["ERROR"]) > 0):?>
    <?$errors = '';?>
    <?foreach($arResult["ERROR"] as $i=>$error):?>
        <?$errors .= str_replace("\\", "", $error).'<br />';?>
    <?endforeach;?>
<?endif;?>

<?if($arResult["RESULT"] != ""):?>
    <p><?=$arResult["RESULT"]?></p>
<?endif;?>

<h2><?=GetMessage("V1RT_PERSONAL_COMMENTS")?></h2>
<div class="comments-list">
    <?if(count($arResult["COMMENTS"])):?>
        <?foreach($arResult["COMMENTS"] as $i=>$obj):?>
            <hr />
            <a name="comment-<?=$obj["ID"]?>"></a>
            <p><strong><?=current(explode(" ", $obj["DATE_CREATE"]))?> [<?=$obj["NAME"]?>]</strong></p>
            <p><?=$obj["PREVIEW_TEXT"]?></p>
        <?endforeach;?>
    <?endif;?>
    
    <?if($arResult["RESULT"] == ""):?>
        <hr />
        <a name="form-comment"></a>
        
        <form method="POST" class="post-form" action="#form-comment">
            <input type="hidden" name="send" value="y"/>
            <table>
                <tbody>
                    <tr>
                        <td width="160"><span style="color: tomato;">* </span><?=GetMessage("V1RT_PERSONAL_NAME")?>:</td>
                        <td><input type="text" name="NAME" value="<?=$arResult["NAME"]?>"/></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;"><span style="color: tomato;">* </span><?=GetMessage("V1RT_PERSONAL_MESSAGE")?>:</td>
                        <td><textarea name="MESSAGE"><?=$arResult["MESSAGE"]?></textarea></td>
                    </tr>
                    <?if($arParams["NO_USE_CAPTCHA"] != "Y"):?>
                    <tr>
                        <td style="vertical-align: top;"><span style="color: tomato;">* </span><?=GetMessage("V1RT_PERSONAL_CAPTCHA")?>:</td>
                        <td>
                            <input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" /> 
                            <img src="/bitrix/tools/captcha.php?captcha_code=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /> 
                        </td>
                    </tr>
                    <tr>
                        <td><span style="color: tomato;">* </span><?=GetMessage("V1RT_PERSONAL_CAPTCHA_INPUT")?>:</td>
                        <td><input type="text" name="captcha_word" maxlength="50" value="" /></td>
                    </tr>
                    <?endif;?>
                    <?if($errors != ""):?>
                    <tr>
                        <td colspan="2" style="color: tomato;"><?=str_replace("\\", "", $errors)?></td>
                    </tr>
                    <?endif;?>
                    <tr>
                        <td></td>
                        <td><input type="submit" name="submit" value=" <?=GetMessage("V1RT_PERSONAL_SUBMIT")?> " style="margin-left: 0;"/></td>
                    </tr>
                </tbody>
            </table>
        </form>
    <?endif;?>
</div>