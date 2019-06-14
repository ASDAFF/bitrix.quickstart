<?php
$module_id = 'unisender.integration';
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
require_once $_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/unisender.integration/include.php';
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
        ?>

        <div class="uni_export_form">
            <form method="post" id="export_form" action="unisender_export.php">
                <fieldset>
                    <legend><?= GetMessage('UNI_GROUPS') ?></legend>
                    <div class="uni_fieldset_content">
                        <?
                        $filter = array(
                            'ACTIVE' => 'Y',
                            'USERS_1' => 1
                        );
                        $by = 'c_sort';
                        $order = 'asc';
                        $rsGroups = CGroup::GetList($by, $order, $filter, 'Y');
                        $i = 1;
                        while ($group = $rsGroups->Fetch()) {
                            if ($i == 2) {
                                $checked = 'checked="checked"';
                            } else {
                                $checked = '';
                            }
                            ?>
                            <input type="checkbox" name="groups[]" id="group<?= $group['ID'] ?>" <?= $checked ?>
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
                <br/>
                <fieldset>
                    <legend><?= GetMessage('UNI_DATA') ?></legend>
                    <div class="uni_fieldset_content">
                        <table class="uni_fields_table">
                            <col width="200px">
                            <tr align="left">
                                <th>UniSender</th>
                                <th>Bitrix</th>
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
                                        <option value="PERSONAL_MOBILE"><?= GetMessage('PERSONAL_MOBILE') ?></option>
                                    </select>
                                </td>
                            </tr>
                            <? foreach ($uniFields as $field): ?>
                                <? $isEmpty = true ?>
                                <tr>
                                    <td><?= $field['public_name'] ?></td>
                                    <td>
                                        <select name="fields[<?= $field['name'] ?>]" class="fields_group"
                                                id="uni_fields_<?= $field['name'] ?>">
                                            <option value=""><?= GetMessage('UNI_FIELDS_NOTIMPORT') ?></option>
                                            <? foreach ($userFields as $name => $userField) : ?>
                                                <? if ($userField['type'] === $field['type']) : ?>
                                                    <? $isEmpty = false ?>
                                                    <option value="<?= $name ?>">
                                                        <?= (!empty($userField['title']) ? $userField['title'] : $name) ?>
                                                    </option>
                                                <? endif ?>
                                            <? endforeach ?>
                                            <? if ($isEmpty === true) : ?>
                                                <option disabled><?= GetMessage('UNI_EMPTY_FIELD_SELECT') ?></option>
                                            <? endif ?>
                                        </select>
                                    </td>
                                </tr>
                            <? endforeach ?>
                        </table>
                        <div class="uni_notetext">
                            <div><?= GetMessage('UNI_FIELDS_LINK') ?></div>
                            <div style="font-style: italic;"><?= GetMessage('UNI_AFTER_CREATE_ENTITY_NOTICE') ?></div>
                        </div>
                    </div>
                </fieldset>
                <br>

                <fieldset>
                    <legend><?= GetMessage('UNI_FS_LISTS') ?></legend>
                    <div class="uni_fieldset_content">
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
                        </table>
                        <div class="uni_notetext">
                            <div><?= GetMessage('UNI_LISTS_LINK') ?></div>
                            <div style="font-style: italic;"><?= GetMessage('UNI_AFTER_CREATE_ENTITY_NOTICE') ?></div>
                        </div>
                    </div>
                </fieldset>

                <dl class="submit_bt">
                    <dt><input type="submit" name="export" value="<?= GetMessage('UNI_EXPORT_BT') ?>"/></dt>
                </dl>
            </form>
        </div>

        <?
    }
} else {
    echo '<span class="errortext">' . GetMessage('UNI_API_KEY_EMPTY', array('#MODULE_ID#' => $module_id)) . '</span>';
}

require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/epilog_admin.php');
?>
