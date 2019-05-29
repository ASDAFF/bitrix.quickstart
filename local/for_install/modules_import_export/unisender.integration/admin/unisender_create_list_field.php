<?php
$module_id = 'unisender.integration';
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/prolog_admin_after.php');
require_once $_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/unisender.integration/include.php';

IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage('UNI_CREATE_LIST_FIELD_TITLE'));
$API_KEY = COption::GetOptionString($module_id, 'UNISENDER_API_KEY');
CModule::IncludeModule('form');

if ($API_KEY !== '') {
    $API = new UniAPI($API_KEY);

    if (!empty($_POST['create'])) {
        if ($_POST['create'] === 'list') {
            $isListCreated = $API->createList($_POST['title']);
            if ($isListCreated === false) {
                $API->showError();
            }
        } elseif ($_POST['create'] === 'field') {
            $isFieldCreated = $API->createField($_POST);
            if ($isFieldCreated === false) {
                $API->showError();
            }
        } else {
            echo '<span class="errortext">Wrong parameters. Can\'t create unexisting entity</span>';
        }
    }

    ?>
    <div class="uni_export_form">
        <form method="post" id="list_form" action="unisender_create_list_field.php">
            <input type="hidden" name="create" value="list" />
            <fieldset>
                <legend><?= GetMessage('UNI_CREATE_LIST_TITLE') ?></legend>
                <table class="uni_fields_table">
                    <tr>
                        <td align="right"><label for="list_title"><?=GetMessage('UNI_CREATE_LIST_LIST')?></label></td>
                        <td align="left"><input type="text" name="title" id="list_title" class="groups" value="" style="width: 250px;" required /></td>
                        <td><input type="submit" name="createForm" value="<?= GetMessage('UNI_CREATE_LIST_BUTTON') ?>"/></td>
                    </tr>
                    <? if (!empty($isListCreated)) {
                        ShowMessage(array('TYPE' => 'OK', 'MESSAGE' => GetMessage('UNI_CREATE_LIST_SUCCESS')));
                    } ?>
                </table>
            </fieldset>
        </form>
        <br>
        <form method="post" id="field_form" action="unisender_create_list_field.php">
            <input type="hidden" name="create" value="field" />
            <fieldset>
                <legend><?= GetMessage('UNI_CREATE_FIELD_TITLE') ?></legend>
                <table class="uni_fields_table">
                    <tr>
                        <td align="right">
                            <label for="field_name"><?=GetMessage('UNI_CREATE_FIELD_NAME')?></label>
                        </td>
                        <td align="left">
                            <input type="text" name="name" id="field_name" class="groups" value="" style="width: 250px;" required />
                        </td>
                    </tr>
                    <tr>
                        <td align="right">
                            <label for="field_public_name"><?=GetMessage('UNI_CREATE_FIELD_PUBLIC_NAME')?></label>
                        </td>
                        <td align="left">
                            <input type="text" name="public_name" id="field_public_name" class="groups" value="" style="width: 250px;" required />
                        </td>
                    </tr>
                    <tr>
                        <td align="right">
                            <label for="field_type"><?=GetMessage('UNI_CREATE_FIELD_TYPE')?></label>
                        </td>
                        <td align="left">
                            <select name="type" id="field_type" required>
                                <option value="string"><?=GetMessage('UNI_FIELD_TYPE_STRING')?></option>
                                <option value="text"><?=GetMessage('UNI_FIELD_TYPE_TEXT')?></option>
                                <option value="number"><?=GetMessage('UNI_FIELD_TYPE_NUMBER')?></option>
                                <option value="date"><?=GetMessage('UNI_FIELD_TYPE_DATE')?></option>
                                <option value="bool"><?=GetMessage('UNI_FIELD_TYPE_BOOL')?></option>
                            </select>
                        </td>
                    </tr>
                </table>
                <dl class="submit_bt">
                    <dt><input type="submit" name="createForm" value="<?= GetMessage('UNI_CREATE_FIELD_BUTTON') ?>"/></dt>
                </dl>
                <? if (!empty($isFieldCreated)) {
                    ShowMessage(array('TYPE' => 'OK', 'MESSAGE' => GetMessage('UNI_CREATE_FIELD_SUCCESS')));
                } ?>
            </fieldset>
        </form>
    </div>
    <?
} else {
    echo '<span class="errortext">' . GetMessage('UNI_API_KEY_EMPTY', array('#MODULE_ID#' => $module_id)) . '</span>';
}

require_once($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/epilog_admin.php");
?>

