<?php
if (!$USER->IsAdmin()) {
    return;
}
$module_id = 'unisender.integration';

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/options.php');
IncludeModuleLangFile(__FILE__);

require_once $_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/' . $module_id . '/include.php';

$settingOptions = array(
    array('UNISENDER_API_KEY', GetMessage('UNISENDER_API_KEY'), '', array('text', 60)),
);
$regOptions = array(
    array('login', GetMessage('UNI_REG_LOGIN'), '', array('text', 60, 'required')),
    array('email', GetMessage('UNI_REG_EMAIL'), $USER->GetEmail(), array('text', 60, 'required')),
    array('phone', GetMessage('UNI_REG_PHONE'), '', array('text', 60)),
    array('firstname', GetMessage('UNI_REG_FIRSTNAME'), $USER->GetFirstName(), array('text', 60)),
    array('lastname', GetMessage('UNI_REG_LASTNAME'), $USER->GetLastName(), array('text', 60)),
    array('password', GetMessage('UNI_REG_PASS'), '', array('password', 60, 'required')),
    array('passwordRepeat', GetMessage('UNI_REG_PASS_REPEAT'), '', array('password', 60, 'required')),
);

$API_KEY = COption::GetOptionString($module_id, 'UNISENDER_API_KEY');

$aTabs[] = array(
    'DIV' => 'edit1',
    'TAB' => GetMessage('MAIN_TAB_SET'),
    'ICON' => 'ib_settings',
    'TITLE' => GetMessage('MAIN_TAB_TITLE_SET')
);

if (empty($API_KEY)) {
    $aTabs[] = array(
        'DIV' => 'registration',
        'TAB' => GetMessage('UNI_REGISTRATION_TITLE'),
        'ICON' => 'ib_settings',
        'TITLE' => GetMessage('UNI_REGISTRATION_DESCRIPTION')
    );
}
?>

<style type="text/css">
    .error {
        border-color: red !important;
    }
</style>
<form method="post" class="uni_register_form"
      action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($mid) ?>&amp;lang=<?= LANGUAGE_ID ?>">
<?
$tabControl = new CAdminTabControl('tabControl', $aTabs);
$tabControl->Begin();

if ($REQUEST_METHOD == 'POST' && strlen($Update . $RestoreDefaults) > 0 && check_bitrix_sessid()) {
    if (strlen($RestoreDefaults) > 0) {
        COption::RemoveOption($module_id);
    } else {
        foreach ($settingOptions as $option) {
            $name = $option[0];
            if (!isset($_REQUEST[$name])) {
                continue;
            }
            $val = $_REQUEST[$name];
            if ($option[2][0] == 'checkbox' && $val != 'Y') {
                $val = 'N';
            }
            COption::SetOptionString($module_id, $name, $val, $option[1]);
        }
    }

    if (!empty($_POST['is_reg']) && empty($_POST['UNISENDER_API_KEY'])) {
        foreach ($regOptions as $option) {
            if (!empty($option[3][2]) && $option[3][2] === 'required' && empty($_POST[$option[0]])) {
                $errorFields[$option[0]] = GetMessage('UNI_REG_EMPTY_FIELD');
            }
        }

        if ($_POST['password'] !== $_POST['passwordRepeat']) {
            $formMessage = '<span class="errortext">' . GetMessage('UNI_REG_ERROR') . '</span>';
            $errorFields['password'] = GetMessage('UNI_REG_WRONG_PASSWORD');
            $errorFields['passwordRepeat'] = GetMessage('UNI_REG_WRONG_PASSWORD');
        }

        if (empty($errorFields)) {
            $API = new UniAPI(null);
            $newAccount = $API->registerAccount($_POST);
            if ($newAccount != false) {
                COption::SetOptionString($module_id, 'UNISENDER_API_KEY', $newAccount->result->api_key);
                array_shift($aTabs);
                echo '
                    <script type="application/javascript">
                        alert("' . GetMessage('UNI_REG_SUCCESS') . '");</script>';
            } else {
                $response = $API->getError();
                $formMessage = '<span class="errortext">' . GetMessage('UNI_REG_ERROR') . '</span>';
                $formMessage .= '<ul><li><i>' . '(' . $response[1] . ') ' . $response[0] . '</i></li></ul>';
            }
        }
    }

    if (empty($errorFields) && empty($formMessage)) {
        LocalRedirect($APPLICATION->GetCurPage() . '?mid=' . urlencode($mid)
            . '&lang=' . urlencode(LANGUAGE_ID)
            . '&back_url_settings=' . urlencode($_REQUEST['back_url_settings'])
            . '&' . $tabControl->ActiveTabParam());
    }
}

$tabControl->BeginNextTab();
foreach ($settingOptions as $option) {
    $val = COption::GetOptionString($module_id, $option[0], $option[2]);
    $type = $option[3];
    ?>
    <tr>
        <td valign="top" width="30%">
            <label for="<?= $option[0] ?>"><?= $option[1] ?></label>
        </td>
        <td valign="top" width="70%">
            <?
            switch ($type[0]) {
                case 'checkbox':
                    echo '<input type="checkbox" id="' . $option[0] . '" name="' . $option[0]
                        . '" value="Y"' . ($val == 'Y' ? ' checked' : '') . '>';
                    break;
                case 'textarea':
                    echo '<textarea rows="' . $type[1] . '" cols="' . $type[2]
                        . '" name="' . $option[0] . '">' . $val . '</textarea>';
                    break;
                default:
                    echo '<input type="' . $type[0] . '" size="' . $type[1] . '" value="' . $val . '" name="' . $option[0] . '">';
                    break;
            }
            ?>
        </td>
    </tr>
    <?
}

if (empty($API_KEY)) {
$tabControl->BeginNextTab();
?>
    <input type="hidden" name="is_reg" value="1">
<?
    if (!empty($formMessage)) {
        echo '<tr><td colspan="2">' . $formMessage . '</td></tr>';
    }
    foreach ($regOptions as $option) {
        $type = $option[3];
        ?>
        <tr>
            <td valign="top" width="30%">
                <label
                    for="<?= $option[0] ?>" <?= (!empty($type[2]) && $type[2] === 'required' ? ' style="font-weight: bold;"' : '') ?>><?= $option[1] ?></label>
            </td>
            <td valign="top" width="70%">
                <?
                $required = $class = $value = '';
                if (!empty($errorFields[$option[0]])) {
                    $required = ' required="required"';
                    $class = ' class="error"';
                    $title = ' title="' . $errorFields[$option[0]] . '"';
                }
                if (!empty($_POST[$option[0]])) {
                    $value = $type[0] === 'checkbox' ? ' checked' : $_POST[$option[0]];
                }
                switch ($type[0]) {
                    case 'checkbox':
                        echo '<input ' . $class . 'type="checkbox" id="' . $option[0] . '" name="' . $option[0] . '" value="Y"' . $required . $value . $title . '>';
                        break;
                    case 'textarea':
                        echo '<textarea ' . $class . 'rows="' . $type[1] . '" cols="' . $type[2]
                            . '" name="' . $option[0] . '"' . $required . $title . '>' . (!empty($_POST['is_reg']) ? $value : $option[2]) . '</textarea>';
                        break;
                    default:
                        echo '<input ' . $class . 'type="' . $type[0] . '" size="' . $type[1]
                            . '" value="' . (!empty($_POST['is_reg']) ? $value : $option[2]) . '" name="' . $option[0] . '"' . $required . $title . '>';
                        break;
                }
                ?>
            </td>
        </tr>
        <?
    }
}

$tabControl->Buttons();
?>
    <input type="submit" name="Update" value="<?= GetMessage('MAIN_SAVE') ?>"
           title="<?= GetMessage('MAIN_OPT_SAVE_TITLE') ?>">
    <input type="reset" name="RestoreDefaults" title="<?= GetMessage('MAIN_HINT_RESTORE_DEFAULTS') ?>"
           value="<?= GetMessage('MAIN_RESTORE_DEFAULTS') ?>">
    <?= bitrix_sessid_post(); ?>
    <? $tabControl->End(); ?>
</form>
