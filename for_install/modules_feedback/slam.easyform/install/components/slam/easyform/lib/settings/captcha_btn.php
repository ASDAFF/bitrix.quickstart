<?
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_js.php");


$obJSPopup = new CJSPopup('',
    array(
        'TITLE' => GetMessage('SLAM_EASYFORM_FIELD_CAPTCHA_POPUP_TITLE'),
        'SUFFIX' => '',
        'ARGS' => ''
    )
);
$obJSPopup->ShowTitlebar();
$obJSPopup->StartDescription('');

?>
<p style="font-size: 14px;">
    <b><? echo GetMessage('SLAM_EASYFORM_FIELD_CAPTCHA_POPUP_TITLE_INNER') ?></b>
</p><!-- Заголовок диалогового окна-->
<br>

<!-- Инструкция по подключению reCaptcha-->
<p class="note"><? echo GetMessage('SLAM_EASYFORM_FIELD_CAPTCHA_POPUP_INSTRUCTION_STEP_ONE') ?></p>
<br>

<p class="note"><? echo GetMessage('SLAM_EASYFORM_FIELD_CAPTCHA_POPUP_INSTRUCTION_STEP_TWO') ?></p>
<br>
<img style="border: 1px solid;margin-right: 30px;"
     src="/bitrix/components/slam/easyform/images/instructions_step_2.png" alt="instructions"/>
<img style="border: 1px solid;" src="/bitrix/components/slam/easyform/images/instructions_step_2_1.png"
     alt="instructions"/>
<br>
<br>

<p class="note"><? echo GetMessage('SLAM_EASYFORM_FIELD_CAPTCHA_POPUP_INSTRUCTION_STEP_THREE') ?></p>
<br>

<p class="note"><? echo GetMessage('SLAM_EASYFORM_FIELD_CAPTCHA_POPUP_INSTRUCTION_STEP_FOUR') ?></p>
<br>
<img style="border: 1px solid;" src="/bitrix/components/slam/easyform/images/instructions_step_4.png"
     alt="instructions"/>


<?
$obJSPopup->StartContent();

$key = \COption::GetOptionString('slam.easyform', 'CAPTCHA_KEY', '', SITE_ID);
$secretKey = \COption::GetOptionString('slam.easyform', 'CAPTCHA_SECRET_KEY', '', SITE_ID);
?>

<div class='adm-workarea'>
    <div class='bxcompprop-content'>
        <table style="width: 100%;">
            <tbody>
            <tr>
                <td><? echo GetMessage('SLAM_EASYFORM_FIELD_CAPTCHA_POPUP_KEY') ?></td>
                <td nowrap="">
                    <input type="text" size="25" maxlength="255" value="<?= $key ?>" name="captcha_key_val">
                </td>
                <td><? echo GetMessage('SLAM_EASYFORM_FIELD_CAPTCHA_POPUP_SECRET_KEY') ?></td>
                <td nowrap="">
                    <input type="text" size="25" maxlength="255" value="<?= $secretKey ?>" name="captcha_secret_key_val">
                </td>
            </tr>
            </tbody>
        </table>

    </div>
</div>

<?
$obJSPopup->StartButtons();
?>

<input type="submit" class="submit_captcha" value="<?= GetMessage('SLAM_EASYFORM_FIELD_CAPTCHA_POPUP_SAVE') ?>"
       onclick="return  JCCustomFormOpen.__saveData()"/>

<?
$obJSPopup->ShowStandardButtons(array('cancel'));
$obJSPopup->EndButtons();
?>

<script>
    $(document).ready(function () {

        $(".submit_captcha").on("click", function () {

            var this_element = $(this);
            var captcha_secret_key_val = this_element.parents(".bx-core-adm-dialog-content-wrap").find('input[name="captcha_secret_key_val"]').val();
            var captcha_key_val = this_element.parents(".bx-core-adm-dialog-content-wrap").find('input[name="captcha_key_val"]').val();

            var data = {
                captcha_secret_key_val: captcha_secret_key_val,
                captcha_key_val: captcha_key_val
            };

            $.ajax({
                url: "/bitrix/components/slam/easyform/lib/settings/captcha_btn.php",
                type: 'POST',
                data: data
            });
        });
    });
</script>



<?
if ($REQUEST_METHOD == 'POST') {
    $_POST['captcha_key_val'] = $_POST['captcha_key_val'] ? $_POST['captcha_key_val'] : "";
    $_POST['captcha_secret_key_val'] = $_POST['captcha_secret_key_val'] ? $_POST['captcha_secret_key_val'] : "";

    \COption::SetOptionString('slam.easyform', 'CAPTCHA_KEY', htmlspecialchars($_POST['captcha_key_val']), false, SITE_ID);
   \COption::SetOptionString('slam.easyform', 'CAPTCHA_SECRET_KEY', htmlspecialchars($_POST['captcha_secret_key_val']), false, SITE_ID);
}
?>