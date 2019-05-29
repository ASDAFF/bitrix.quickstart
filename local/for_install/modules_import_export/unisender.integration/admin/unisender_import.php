<?php
$module_id = 'unisender.integration';
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
require_once $_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/' . $module_id . '/include.php';
IncludeModuleLangFile(__FILE__);
require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/prolog_admin_after.php');
echo '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>';
echo '<script type="text/javascript" src="/bitrix/js/' . $module_id . '/js.js"></script>';
echo '<link rel="stylesheet" type="text/css" href="/bitrix/themes/.default/unisender.integration.css">';
$APPLICATION->SetTitle(GetMessage('UNI_TITLE'));
$API_KEY = COption::GetOptionString($module_id, 'UNISENDER_API_KEY');

if ($API_KEY !== '') {
    $API = new UniAPI($API_KEY);
    $lists = $API->getLists();
    $uniFields = $API->getFields();
    $userFields = Unisender::getUserFields();

    if (!is_array($lists) || !is_array($uniFields)) {
        $API->showError();
    } else {
        if (empty($_POST)) {
            ?>

            <div class="uni_export_form">
                <form method="post" id="export_form" action="unisender_import.php">
                    <fieldset>
                        <legend><?= GetMessage('UNI_IMPORT_SETTINGS') ?></legend>
                        <table class="uni_fields_table">
                            <tr>
                                <td width="200px">
                                    <?= GetMessage('UNI_LIST') ?>
                                </td>
                                <td>
                                    <select name="list_id">
                                        <? foreach ($lists as $list): ?>
                                            <option value="<?= $list['id'] ?>"><?= $list['title'] ?></option>
                                        <? endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="isChangePassLetter"><?= GetMessage('UNI_SEND_LETTER_NEW_USER') ?></label></td>
                                <td><input type="checkbox" name="isChangePassLetter" id="isChangePassLetter" class="groups" value="1" checked /></td>
                            </tr>
                            <tr title="<?= GetMessage('UNI_SUPPLEMENT_GROUP_WARNING') ?>">
                                <td><label for="isSupplementGroups"><?= GetMessage('UNI_SUPPLEMENT_GROUP') ?></label></td>
                                <td>
                                    <div class="adm-info-message">
                                        <input type="checkbox" name="isSupplementGroups" id="isSupplementGroups" class="groups" value="1" />
                                        <?=GetMessage('UNI_SUPPLEMENT_GROUP_WARNING')?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <br>

                    <fieldset>
                        <legend><?= GetMessage('UNI_DATA') ?></legend>
                        <div class="uni_fieldset_content">
                            <table class="uni_fields_table">
                                <col width="200px">
                                <tr align="left">
                                    <th>Bitrix</th>
                                    <th>UniSender</th>
                                </tr>
                                <tr>
                                    <td><?= GetMessage('EMAIL') ?></td>
                                    <td>
                                        <select name="email" disabled="disabled">
                                            <option value="1"><?= GetMessage('UNI_FIELDS_INVARIABLE') ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?= GetMessage('PERSONAL_PHONE') ?></td>
                                    <td>
                                        <select name="phone">
                                            <option value="0"><?= GetMessage('UNI_FIELDS_NOTIMPORT') ?></option>
                                            <option value="PERSONAL_PHONE"><?= GetMessage('PERSONAL_PHONE') ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <? foreach ($userFields as $name => $userField) : ?>
                                    <? if ($name === 'EMAIL' || $name === 'PERSONAL_PHONE') {
                                        continue;
                                    } ?>
                                    <? $isEmpty = true ?>
                                    <tr>
                                        <td><?= !empty($userField['title']) ? $userField['title'] : $name ?></td>
                                        <td>
                                            <select name="fields[<?= $name ?>]" class="fields_group"
                                                    id="uni_fields_<?= $name ?>">
                                                <option
                                                    value="<?= $name !== 'LOGIN' ? '' : 'email' ?>"><?= GetMessage($name !== 'LOGIN' ? 'UNI_FIELDS_NOTIMPORT' : 'UNI_LOGIN_AS_EMAIL') ?></option>
                                                <? foreach ($uniFields as $field): ?>
                                                    <? if ($userField['type'] === $field['type']) : ?>
                                                        <? $isEmpty = false ?>
                                                        <option value="<?= $field['name'] ?>">
                                                            <?= $field['public_name'] ?> (<?= $field['name'] ?>)
                                                        </option>
                                                    <? endif ?>
                                                <? endforeach ?>
                                                <? if ($isEmpty === true) : ?>
                                                    <option
                                                        disabled><?= GetMessage('UNI_EMPTY_FIELD_SELECT') ?></option>
                                                <? endif ?>
                                            </select>
                                        </td>
                                    </tr>
                                <? endforeach ?>
                            </table>
                        </div>
                    </fieldset>
                    <br>

                    <fieldset>
                        <legend><?= GetMessage('UNI_GROUPS') ?></legend>
                        <div class="uni_fieldset_content">
                            <?
                            $filter = array(
                                'ACTIVE' => 'Y'
                            );
                            $by = 'c_sort';
                            $order = 'asc';
                            $rsGroups = CGroup::GetList($by, $order, $filter, 'Y');
                            $i = 1;
                            while ($group = $rsGroups->Fetch()) {
                                ?>
                                <input type="checkbox" name="groups[]" id="group<?= $group['ID'] ?>"
                                       class="groups" value="<?= $group['ID'] ?>"/>
                                <label for="group<?= $group['ID'] ?>">
                                    <?= $group['NAME'] ?> (
                                    <a
                                        href="<?= BX_ROOT ?>/admin/user_admin.php?lang=ru&find_group_id[]=<?= $group['ID'] ?>&set_filter=Y"
                                        title="<?= GetMessage('UNI_USERS_LINK_TITLE') ?>"
                                        target="_blank"><?= $group['USERS'] ?></a>)</label><br/>
                                <?
                                $i++;
                            }
                            ?>
                        </div>
                    </fieldset>

                    <dl class="submit_bt">
                        <dt><input type="submit" name="export" value="<?= GetMessage('UNI_IMPORT_BT') ?>"/></dt>
                    </dl>
                </form>
            </div>

            <?
        } else {
            $APPLICATION->SetTitle(GetMessage('UNI_IMPORT_START'));
            $errors = array();
            $response = array(
                'total' => 0,
                'inserted' => 0,
                'updated' => 0,
                'errors' => 0
            );

            $params = array(
                'list_id' => (int)$_POST['list_id'],
                'offset' => 0,
                'limit' => 1000
            );

            while (1) {
                $contacts = $API->exportContacts($params);
                if (count($contacts->result->data) === 0) {
                    break;
                }

                foreach ($contacts->result->data as $contact) {
                    $data = array(
                        'GROUP_ID' => $_POST['groups']
                    );
                    foreach ($contacts->result->field_names as $id => $fieldName) {
                        if ($fieldName === 'email_status') {
                            if (!in_array($contact[$id], array('new', 'active'), true)) {
                                unset($data);
                                break;
                            }
                        } elseif ($fieldName === 'email') {
                            $data['EMAIL'] = $contact[$id];
                            if ($_POST['fields']['LOGIN'] === 'email') {
                                $data['LOGIN'] = $contact[$id];
                            }
                        } elseif ($fieldName === 'phone' && !empty($_POST['phone'])) {
                            $data[$_POST['phone']] = $contact[$id];
                        } else {
                            foreach ($_POST['fields'] as $userField => $uniField) {
                                if (empty($uniField)) {
                                    continue;
                                }
                                if ($fieldName === $uniField && !empty($contact[$id])) {
                                    if ($userFields[$userField]['type'] === 'date') {
                                        $contact[$id] = ConvertTimeStamp(strtotime($contact[$id]));
                                    }
                                    $data[$userField] = $contact[$id];
                                }
                            }
                        }
                    }
                    if (!isset($data)) {
                        continue;
                    }

                    $filter = array(
                        'EMAIL' => $data['EMAIL'],
                    );
                    $by = 'id';
                    $order = 'desc';
                    $user = CUser::GetList($by, $order, $filter);
                    $user = $user->Fetch();

                    if (!empty($user)) {
                        //UPDATE
                        unset($data['LOGIN']);
                        $updatedUser = new CUser();

                        if (empty($_POST['isSupplementGroups'])) {
                            $data['GROUP_ID'] = array_unique(
                                array_merge(
                                    $data['GROUP_ID'],
                                    CUser::GetUserGroup($user['ID'])),
                                SORT_NUMERIC
                            );
                        }

                        if (!$updatedUser->Update($user['ID'], $data)) {
                            $errors[$data['EMAIL']] = $updatedUser->LAST_ERROR;
                            $response['errors']++;
                        } else {
                            $response['updated']++;
                        }
                    } else {
                        //ADD
                        $addedUser = new CUser();
                        $password_chars = array(
                            'abcdefghijklnmopqrstuvwxyz',
                            'ABCDEFGHIJKLNMOPQRSTUVWXYZ',
                            '0123456789',
                        );
                        $data['PASSWORD'] = $data['CONFIRM_PASSWORD'] = randString(6, $password_chars);

                        if (!$addedUser->Add($data)) {
                            $errors[$data['EMAIL']] = $addedUser->LAST_ERROR;
                            $response['errors']++;
                        } else {
                            if (!empty($_POST['isChangePassLetter'])) {
                                $USER->SendPassword($data['LOGIN'], $data['EMAIL']);
                            }
                            $response['inserted']++;
                        }
                    }

                    $response['total']++;
                    $params['offset'] += 1000;
                }
            }

            echo '<p>' . GetMessage('UNI_IMPORT_STAT', array(
                    '#TOTAL#' => $response['total'],
                    '#INSERTED#' => $response['inserted'],
                    '#UPDATED#' => $response['updated']))
                . '</p>';

            if (!empty($errors)) {
                echo '<p><b>' . GetMessage('UNI_IMPORT_ERROR_HEADER') . ':</b><ul>';
                foreach ($errors as $email => $error) {
                    echo '<li>' . $email . ': ' . $error . '</li>';
                }
                echo '</ul></p>';
            }
            ShowMessage(array('TYPE' => 'OK', 'MESSAGE' => GetMessage('UNI_IMPORT_FINISH')));
        }
    }
} else {
    echo '<span class="errortext">' . GetMessage('UNI_API_KEY_EMPTY', array('#MODULE_ID#' => $module_id)) . '</span>';
}

require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/epilog_admin.php');



// Debug function
function D($var, $exit = 0) {
    print '<div style="background-color: #ffffff; padding: 3px; z-index: 5000;"><pre style="text-align: left; font: normal 11px Courier; color: #000000;">';
    if ( is_array($var) || is_object($var) ) print_r($var);
    else var_dump($var);
    print '</pre></div>';
    if ( $exit ) exit;
}

?>
