<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<?if(count($arResult["ERROR"]) > 0):?>
    <p style="color: tomato;">
    <?foreach($arResult["ERROR"] as $i=>$error):?>
        <?=$error?><br />
    <?endforeach;?>
    </p>
<?endif;?>

<?if($arResult["RESULT"] == ""):?>
    <form method="POST" class="post-form">
    <input type="hidden" name="send" value="y"/>
    <table>
    <tbody><tr>
    <td width="180"><span style="color: tomato;">*</span> Ваше имя</td>
    <td><input type="text" name="NAME" value="<?=$arResult["NAME"]?>"/></td>
    </tr>
    <tr>
    <td><span style="color: tomato;">*</span> Сообщение</td>
    <td><textarea name="MESSAGE"><?=$arResult["MESSAGE"]?></textarea></td>
    </tr>
    <tr>
    <td></td>
    <td><input type="submit" name="submit" value="Отправить" style="margin-left: 0;"/></td>
    </tr>
    </tbody></table>
    </form>
<?else:?>
    <p><?=$arResult["RESULT"]?></p>
<?endif;?>

<hr />

<?foreach($arResult["COMMENTS"] as $i=>$obj):?>
    <div id="comment-<?=$obj["ID"]?>" style="margin-bottom: 20px;">
        <p><strong><?=$obj["NAME"]?></strong></p>
        <p><?=$obj["PREVIEW_TEXT"]?></p>
    </div>
<?endforeach;?>