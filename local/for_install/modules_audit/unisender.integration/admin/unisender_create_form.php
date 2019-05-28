<?php
$module_id = 'unisender.integration';
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/prolog_admin_after.php');
require_once $_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/unisender.integration/include.php';

IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage('UNI_NEW_FORM'));
$API_KEY = COption::GetOptionString($module_id, 'UNISENDER_API_KEY');
CModule::IncludeModule('form');

if ($API_KEY !== '') {
    $API = new UniAPI($API_KEY);
    $uniFields = $API->getFields();

    if (!empty($_POST['createForm'])) {
        unset($_POST['createForm']);

        $SID = !empty($_POST['SID']) ? $_POST['SID'] : 'UNISENDER_SUBSCRIBE_FORM_1';
        $NAME = !empty($_POST['TITLE']) ? $_POST['TITLE'] : GetMessage('UNI_FORM_TITLE_DEFAULT');
        $BUTTON = !empty($_POST['BUTTON']) ? $_POST['BUTTON'] : GetMessage('UNI_FORM_BUTTON_DEFAULT');

        $form = CForm::GetBySID($SID);
        $form = $form->Fetch();

        $formId = CForm::Set(array(
            'NAME' => $NAME,
            'SID' => $SID,
            'BUTTON' => $BUTTON,
            'arMENU' => array('ru' => $NAME)
        ), !empty($form['ID']) ? $form['ID'] : false);

        if ($formId > 0) {
            CFormStatus::Set(array(
                'FORM_ID' => $formId,
                'C_SORT' => 100,
                'ACTIVE' => 'Y',
                'TITLE' => GetMessage('UNI_FORM_STATUS_TITLE'),
                'DESCRIPTION' => GetMessage('UNI_FORM_STATUS_DESCRIPTION'),
                'CSS' => 'statusgreen',
                'HANDLER_OUT' => '',
                'HANDLER_IN' => '',
                'DEFAULT_VALUE' => 'Y',
                'arPERMISSION_VIEW' => array(),
                'arPERMISSION_MOVE' => array(),
                'arPERMISSION_EDIT' => array(),
                'arPERMISSION_DELETE' => array(),
            ));

            array_unshift($_POST['fields'], 'email');
            array_unshift($_POST['required'], 'email');
            $sortIterator = 0;
            if (!empty($_POST['fields']['phone'])) {
                $uniFields = array_merge(array(
                    'phone' => array(
                        'name' => 'email',
                        'public_name' => GetMessage('UNI_FORM_PHONE'),
                        'type' => 'text',
                        'is_visible' => 1,
                        'view_pos' => 1
                    )
                ), $uniFields);
            }
            $uniFields = array_merge(array(
                'email' => array(
                    'name' => 'email',
                    'public_name' => 'E-mail',
                    'type' => 'email',
                    'is_visible' => 1,
                    'view_pos' => 1,
                    'required' => 1
                )
            ), $uniFields);

            foreach ($_POST['fields'] as $fieldName) {
                if (!isset($uniFields[$fieldName])) {
                    continue;
                }
                $uniField = $uniFields[$fieldName];
                $answer = array();
                switch ($uniField['type']) {
                    case 'email':
                        $type = 'email';
                        break;
                    case 'text':
                        $type = 'textarea';
                        break;
                    case 'bool':
                        $type = 'checkbox';
                        break;
                    case 'date':
                        $type = 'date';
                        break;
                    case 'string':
                    case 'number':
                    default:
                        $type = 'text';
                        break;
                }
                $answer[] = array(
                    'MESSAGE' => $uniField['public_name'],
                    'ACTIVE' => 'Y',
                    'FIELD_TYPE' => $type
                );

                $field = CFormField::GetBySID($fieldName, $formId);
                $field = $field->Fetch();
                if (!empty($field['ID'])) {
                    $oldAnswers = CFormAnswer::GetList($field['ID']);
                    while ($oldAnswer = $oldAnswers->Fetch()) {
                        CFormAnswer::Delete($oldAnswer['ID']);
                    }
                }
                $fieldId = CFormField::Set(array(
                    'SID' => $fieldName,
                    'FORM_ID' => $formId,
                    'ACTIVE' => 'Y',
                    'REQUIRED' => isset($_POST['required'][$fieldName]) ? 'Y' : 'N',
                    'C_SORT' => ++$sortIterator,
                    'ADDITIONAL' => 'N',
                    'TITLE_TYPE' => 'text',
                    'TITLE' => $uniField['public_name'],
                    'FILTER_TITLE' => $uniField['public_name'],
                    'RESULTS_TABLE_TITLE' => $uniField['public_name'],
                    'IN_RESULTS_TABLE' => 'Y',
                    'IN_EXCEL_TABLE' => 'Y',
                    'arANSWER' => $answer
                ), !empty($field['ID']) ? $field['ID'] : false);
                if (!$fieldId) {
                    unset($_POST['fields'][$fieldName]);
                }
            }

            $formTemplate = '<div><?=$FORM->ShowFormTitle()?></div>';
            foreach ($_POST['fields'] as $fieldName) {
                $formTemplate .= '<div><?=$FORM->ShowInput("' . $fieldName . '")?></div>
';
            }
            $formTemplate .= '<div><?=$FORM->ShowFormErrors()?><?=$FORM->ShowFormNote()?></div>
<div><?=$FORM->ShowSubmitButton()?></div>';
            CForm::Set(array('FORM_TEMPLATE' => $formTemplate), $formId);

            LocalRedirect('form_edit.php?lang=' . LANGUAGE_ID . '&ID=' . $formId);
        } else {
            echo '<span class="errortext">' . $strError . '</span><br>';
        }
    }

    ?>
    <div class="uni_export_form">
        <form method="post" id="export_form" action="unisender_create_form.php">
            <fieldset>
                <legend><?= GetMessage('UNI_FORM_TITLES') ?></legend>
                <table class="uni_fields_table">
                    <tr>
                        <td align="right"><label for="SID"><?=GetMessage('UNI_FORM_SID')?></label></td>
                        <td align="left"><input type="text" name="SID" id="SID" class="groups" value="UNISENDER_SUBSCRIBE_FORM_1" style="width: 250px;" /></td>
                    </tr>
                    <tr>
                        <td align="right"><label for="TITLE"><?=GetMessage('UNI_FORM_TITLE')?></label></td>
                        <td align="left"><input type="text" name="TITLE" id="TITLE" class="groups" value="<?=GetMessage('UNI_FORM_TITLE_DEFAULT')?>" style="width: 250px;" /></td>
                    </tr>
                    <tr>
                        <td align="right"><label for="BUTTON"><?=GetMessage('UNI_FORM_BUTTON')?></label></td>
                        <td align="left"><input type="text" name="BUTTON" id="BUTTON" class="groups" value="<?=GetMessage('UNI_FORM_BUTTON_DEFAULT')?>" style="width: 250px;" /></td>
                    </tr>
                </table>
            </fieldset>
            <br/>
            <fieldset>
                <legend><?= GetMessage('UNI_FORM_FIELDS') ?></legend>
                <table class="uni_fields_table">
                    <tr>
                        <th><?=GetMessage('UNI_FORM_FIELD')?></th>
                        <th><?=GetMessage('UNI_FORM_INCLUDE')?></th>
                        <th><?=GetMessage('UNI_FORM_REQUIRED')?></th>
                    </tr>
                    <tr>
                        <td align="right"><label for="email">E-mail (email)</label></td>
                        <td align="center"><input type="checkbox" name="fields[email]" id="email" class="groups" value="email" checked disabled /></td>
                        <td align="center"><input type="checkbox" name="required[email]" id="r_email" class="groups" value="1" checked disabled /></td>
                    </tr>
                    <tr>
                        <td align="right"><label for="phone"><?=GetMessage('UNI_FORM_PHONE')?> (phone)</label></td>
                        <td align="center"><input type="checkbox" name="fields[phone]" id="phone" class="groups" value="phone" /></td>
                        <td align="center"><input type="checkbox" name="required[phone]" id="r_phone" class="groups" value="1" /></td>
                    </tr>
                    <?
                    foreach ($uniFields as $uniField) {
                        echo '<tr><td align="right"><label for="'. $uniField['name'] . '">' . $uniField['public_name'] . ' (' . $uniField['name'] . ')</label></td>'
                            . '<td align="center"><input type="checkbox" name="fields[' . $uniField['name'] . ']" id="' . $uniField['name'] . '" class="groups" value="' . $uniField['name'] . '" /></td>'
                            . '<td align="center"><input type="checkbox" name="required[' . $uniField['name'] . ']" id="r_' . $uniField['name'] . '" class="groups" value="1" /></td>';
                    }
                    ?>
                </table>
            </fieldset>
            <br/>

            <dl class="submit_bt">
                <dt><input type="submit" name="createForm" value="<?= GetMessage('UNI_FORM_BT') ?>"/></dt>
            </dl>
        </form>
    </div>
    <?
} else {
    echo '<span class="errortext">' . GetMessage('UNI_API_KEY_EMPTY', array('#MODULE_ID#' => $module_id)) . '</span>';
}

require_once($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/epilog_admin.php");
?>

