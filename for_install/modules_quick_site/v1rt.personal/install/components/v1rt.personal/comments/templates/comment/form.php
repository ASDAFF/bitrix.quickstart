<form method="POST" class="post-form" action="#form-comment">
    <input type="hidden" name="send" value="y"/>
    <table>
        <tbody>
            <tr>
                <td width="105"><span style="color: tomato;">*</span> <?=htmlspecialcharsbx($_POST["NAME_STR"])?>:</td>
                <td><input type="text" name="NAME" value="<?=htmlspecialcharsbx($_POST["NAME"])?>"/></td>
            </tr>
            <tr>
                <td style="vertical-align: top;"><span style="color: tomato;">*</span> <?=htmlspecialcharsbx($_POST["MESSAGE_STR"])?>:</td>
                <td><textarea name="MESSAGE"><?=htmlspecialcharsbx($_POST["MESSAGE"])?></textarea></td>
            </tr>
            <?if($_POST["ERROR"] != ""):?>
            <tr>
                <td colspan="2" style="color: tomato;"><?=htmlspecialcharsbx(str_replace("\\", "", $_POST["ERROR"]))?></td>
            </tr>
            <?endif;?>
            <tr>
                <td></td>
                <td><input type="submit" name="submit" value=" <?=htmlspecialcharsbx($_POST["SUBMIT_STR"])?> " style="margin-left: 0;"/></td>
            </tr>
        </tbody>
    </table>
</form>