<?php
define("ADMIN_MODULE_NAME", "multiline.ml2webforms");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile(__DIR__.'/menu.php');

/** @var CMain $APPLICATION */
$module_right = $APPLICATION->GetGroupRight(ADMIN_MODULE_NAME);
if ($module_right == "D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if (!\CModule::IncludeModule(ADMIN_MODULE_NAME))
{
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Page\Asset;
use \Ml2WebForms\WebForm;
use \Ml2WebForms\MlAdminPanelBuilder;

$folders = array(
    "/local/modules",
    "/bitrix/modules",
);

class Ml2WebFormsEdit {
    public static function fillFormFiles($dir, $tplKeys) {
        $files = scandir($dir);

        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            $newfilename = basename(str_replace(array_keys($tplKeys), array_values($tplKeys), $file), '.tpl');
            rename($dir . '/' . $file, $dir . '/' . $newfilename);

            if (is_dir($dir . '/' . $newfilename)) {
                self::fillFormFiles($dir . '/' . $newfilename, $tplKeys);
            } else {
                file_put_contents(
                    $dir . '/' . $newfilename,
                    str_replace(
                        array_keys($tplKeys),
                        array_values($tplKeys),
                        file_get_contents($dir . '/' . $newfilename)
                    )
                );
            }
        }
    }

    public static function createTable($formID, $formFields) {
        $query = Ml2WebFormsEdit::generateCreateTableQuery($formID, $formFields);
        Application::getConnection()->query($query);
    }

    public static function reCreateTable($formID, $formFields) {
        Application::getConnection()->query("ALTER TABLE  `ml2webforms_{$formID}` RENAME  `ml2webforms_{$formID}_old" . date('YmdHis') . "`");
        $query = Ml2WebFormsEdit::generateCreateTableQuery($formID, $formFields);
        Application::getConnection()->query($query);
    }

    public static function generateCreateTableQuery($formID, $formFields) {
        $queryFields = '';
        $indexFields = '';
        $fieldValuesTypes = array(
            WebForm::FIELD_VALUE_TYPE_TEXT => 'TEXT NOT NULL',
            WebForm::FIELD_VALUE_TYPE_STRING => 'VARCHAR(500) NOT NULL',
            WebForm::FIELD_VALUE_TYPE_INTEGER => 'INT NOT NULL',
            WebForm::FIELD_VALUE_TYPE_REAL => 'FLOAT NOT NULL',
            WebForm::FIELD_VALUE_TYPE_DATE => 'DATE NOT NULL',
            WebForm::FIELD_VALUE_TYPE_DATETIME => 'DATETIME NOT NULL',
        );
        foreach ($formFields as $field => $params) {
            if (!$field) {
                continue;
            }
            if ((int)$params['type'] == WebForm::FIELD_TYPE_SELECT_MULTIPLE) {
                if ($params['filterable']) {
                    $fieldType = 'VARCHAR(500) NOT NULL';
                } else {
                    $fieldType = 'TEXT NOT NULL';
                }
            } else {
                $fieldType = $fieldValuesTypes[(int)$params['value_type']];
            }
            $queryFields .= '`' . $field . '` ' . $fieldType . ', ';
            if ((int)$params['value_type'] > 0 && $params['filterable']) {
                $indexFields .= ', INDEX ( `' . $field . '`)';
            }
        }
        $query = "
            CREATE TABLE IF NOT EXISTS `ml2webforms_{$formID}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `datetime` datetime NOT NULL,
                {$queryFields}
                PRIMARY KEY ( `id` ) ,
                INDEX ( `datetime` ){$indexFields}
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ";

        return $query;
    }

    public static function generatePostEvent($form_data, $formFields) {
        $eventName = 'ML2WEBFORMS_' . strtoupper($form_data['ID']) . '_WEBFORM_FILL';
        $description = "#ID# - ID запроса\r\n#DATETIME# - время запроса\r\n";
        $description_en = "#ID# - result ID\r\n#DATETIME# - result time\r\n";
        $template_message = "ID запроса: #ID#\r\nВремя запроса: #DATETIME#\r\n";
        $template_message_en = "Result ID: #ID#\r\nResult time: #DATETIME#\r\n";
        foreach ($formFields as $field => $params) {
            $description .= "#" . strtoupper($field) ."# - {$params['title']['ru']}\r\n";
            $description_en .= "#" . strtoupper($field) ."# - {$params['title']['en']}\r\n";

            if (in_array($params['type'], array(WebForm::FIELD_TYPE_SELECT, WebForm::FIELD_TYPE_SELECT_MULTIPLE, WebForm::FIELD_TYPE_RADIO))) {
                $description .= "#" . strtoupper($field) ."_EN# - {$params['title']['ru']} [en]\r\n";
                $description_en .= "#" . strtoupper($field) ."_EN# - {$params['title']['en']} [en]\r\n";
            }

            $template_message .= "{$params['title']['ru']}: #" . strtoupper($field) ."#\r\n";
            if (in_array($params['type'], array(WebForm::FIELD_TYPE_SELECT, WebForm::FIELD_TYPE_SELECT_MULTIPLE, WebForm::FIELD_TYPE_RADIO))) {
                $template_message_en .= "{$params['title']['en']}: #" . strtoupper($field) ."_EN#\r\n";
            } else {
                $template_message_en .= "{$params['title']['en']}: #" . strtoupper($field) ."#\r\n";
            }
        }

        return array(
            'ru' => array(
                "EVENT_NAME"    => $eventName,
                "NAME"          => "Multiline: Веб-формы. Заполнена форма \"{$form_data['NAME_RU']}\"",
                "LID"           => "ru",
                "DESCRIPTION"   => $description
            ),
            'en' => array(
                "EVENT_NAME"    => $eventName,
                "NAME"          => "Multiline: Web-forms. Form filled \"{$form_data['NAME_EN']}\"",
                "LID"           => "en",
                "DESCRIPTION"   => $description_en
            ),
            'template_message' => $template_message,
            'template_message_en' => $template_message_en,
        );
    }

    public static function generatePostTemplate($form_data, $eventData) {
        $lid = "s1";
        $rsSites = \CSite::GetList($order = "sort", $by = "desc", $filter = array("ACTIVE" => "Y"));
        if ($arSite = $rsSites->Fetch()) {
            $lid = $arSite["LID"];
        }
        return array(
            "ACTIVE" => "Y",
            "EVENT_NAME" => $eventData['ru']['EVENT_NAME'],
            "LID" => array($lid),
            "EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
            "EMAIL_TO" => "#DEFAULT_EMAIL_FROM#",
            "BCC" => "",
            "SUBJECT" => "#SITE_NAME#: Заполнена форма \"{$form_data['NAME_RU']}\"",
            "BODY_TYPE" => "text",
            "MESSAGE" => $eventData['template_message'] . "\r\n\r\n" . $eventData['template_message_en'],
        );
    }

    public static function getFieldsCodeStr($formID) {
        $fieldsCodeStr = '';
        $formClassContents = file_get_contents(__DIR__ . '/../lib/forms/' . $formID . '/class.php');
        preg_match('/public\s+function\s+getFields\(\)\s*\{\s*return\s+array\s*\((.*)/is', $formClassContents, $formClassFieldsContents);
        $formClassFieldsContents = $formClassFieldsContents[1];
        $cnt = 0;
        for ($i = 0; $i < mb_strlen($formClassFieldsContents, 'utf-8'); $i++) {
            if (mb_substr($formClassFieldsContents, $i, 1, 'utf-8') === '{') {
                $cnt++;
            }
            if (mb_substr($formClassFieldsContents, $i, 1, 'utf-8') === '}') {
                $cnt--;
                if ($cnt < 0) break;
            }
            $fieldsCodeStr .= mb_substr($formClassFieldsContents, $i, 1, 'utf-8');
        }

        return $fieldsCodeStr;
    }

    public static function getFieldRequiredFunctionStr($field, $formID) {
        $formFieldsOldStr = Ml2WebFormsEdit::getFieldsCodeStr($formID);
        preg_match('/\'' . $field . '\'\s*=>\s*array\s*\((.*)\)/is', $formFieldsOldStr, $formFieldParamsMatch);
        $formFieldParamsMatch = $formFieldParamsMatch[1];
        $fieldParamsStr = '';
        $cnt = 0;
        for ($i = 0; $i < mb_strlen($formFieldParamsMatch, 'utf-8'); $i++) {
            if (mb_substr($formFieldParamsMatch, $i, 1, 'utf-8') === '(') {
                $cnt++;
            }
            if (mb_substr($formFieldParamsMatch, $i, 1, 'utf-8') === ')') {
                $cnt--;
                if ($cnt < 0) break;
            }
            $fieldParamsStr .= mb_substr($formFieldParamsMatch, $i, 1, 'utf-8');
        }

        preg_match('/\'required\'\s*=>\s*(function\s*\(\)\s*\{.*\})/is', $fieldParamsStr, $fieldRequiredMatch);
        $fieldRequiredMatch = $fieldRequiredMatch[1];
        $fieldRequiredStr = '';
        $cnt = 0;
        for ($i = 0; $i < mb_strlen($fieldRequiredMatch, 'utf-8'); $i++) {
            if (mb_substr($fieldRequiredMatch, $i, 1, 'utf-8') === '{') {
                $cnt++;
            }
            if (mb_substr($fieldRequiredMatch, $i, 1, 'utf-8') === '}') {
                $cnt--;
                if ($cnt < 1) {
                    $fieldRequiredStr .= mb_substr($fieldRequiredMatch, $i, 1, 'utf-8');
                    break;
                }
            }
            $fieldRequiredStr .= mb_substr($fieldRequiredMatch, $i, 1, 'utf-8');
        }

        return $fieldRequiredStr;
    }
}

$ID = htmlspecialchars (str_replace ("/", "", $_REQUEST["id"]));

$obEventType = new \CEventType();
$obTemplate = new \CEventMessage();

$form_data = array();
$errors = array();
$errorFatal = false;
$folder = strpos(__DIR__, 'local' . DIRECTORY_SEPARATOR . 'modules') !== false ? $folders[0] : $folders[1];

if (strlen($ID) > 0) {
    include __DIR__ . '/../lib/forms/' . $ID . '/class.php';
    $name = include __DIR__ . '/../lib/forms/' . $ID . '/name.php';
    $postTemplates = array();
    $tBy = "id";
    $tOrder = "asc";
    $eventName = 'ML2WEBFORMS_' . strtoupper($ID) . '_WEBFORM_FILL';
    $rsPostTemplates = $obTemplate->GetList($tBy, $tOrder, array(
        'TYPE_ID' => $eventName,
    ));
    while ($postTemplate = $rsPostTemplates->Fetch()) {
        $postTemplates[] = $postTemplate;
    }
    $webFromClass = '\Ml2WebForms\\' . ucfirst($ID) . 'WebForm';
    $webFrom = new $webFromClass();
    $form_data = array(
        'ID' => $ID,
        'NAME_RU' => $name['ru'],
        'NAME_EN' => $name['en'],
        'FIELDS' => $webFrom->getFields(),
        'POST_EVENT' => $obEventType->GetByID($eventName, 'ru')->Fetch(),
        'POST_TEMPLATES' => $postTemplates,
    );
} else {
    $form_data = array(
        'ID' => '',
        'FIELDS' => array(),
    );

    $APPLICATION->SetTitle(Loc::getMessage('ml2webforms_form_create_title'));
}
Asset::getInstance()->addJs('/bitrix/js/main/jquery/jquery-1.8.3.min.js');
Asset::getInstance()->addString('<script type="text/javascript">' . file_get_contents($_SERVER['DOCUMENT_ROOT'] . $folder .'/multiline.ml2webforms/js/script.js') . '</script>');
Asset::getInstance()->addString('<style type="text/css">' . file_get_contents($_SERVER['DOCUMENT_ROOT'] . $folder . '/multiline.ml2webforms/css/style.css') . '</style>');

$aTabs = array();
$aTabs[] = array(
    "DIV" => "tab_edit_ml2webforms_edit_general",
    "TAB" => Loc::getMessage("ml2webforms_edit_general"),
    "ICON" => "main_user_edit",
    "TITLE" => Loc::getMessage("ml2webforms_edit_general_settings"),
);

$tabControl = new \CAdminTabControl("tabControl", $aTabs);

if ($_SERVER["REQUEST_METHOD"] == "POST" && $module_right == "W" && strlen($_REQUEST['Update']) > 0 && check_bitrix_sessid() && !$errorFatal) {
    $form_data['ID'] = MlAdminPanelBuilder::PrepareFieldValue($_POST['ID']);
    $form_data['NAME_RU'] = trim(MlAdminPanelBuilder::PrepareFieldValue($_POST['NAME_RU']));
    $form_data['NAME_EN'] = trim(MlAdminPanelBuilder::PrepareFieldValue($_POST['NAME_EN']));
    //print_r($_POST);exit;

    if (strlen($form_data['ID']) == 0) {
        $errors[] = Loc::getMessage('ml2webforms_edit_error_form_id_empty');
    }
    if (strlen($form_data['NAME_RU']) == 0) {
        $errors[] = Loc::getMessage('ml2webforms_edit_error_form_name_ru_empty');
    }
    if (strlen($form_data['NAME_EN']) == 0) {
        $errors[] = Loc::getMessage('ml2webforms_edit_error_form_name_en_empty');
    }

    // Prepare replace keys for creating new module
    $tplKeys = array(
        '##webform_id##' => $form_data['ID'],
        '##webform_id_upper##' => strtoupper($form_data['ID']),
        '##webform_id_uc_first##' => ucfirst($form_data['ID']),
        '##webform_name_ru##' => $form_data['NAME_RU'],
        '##webform_name_en##' => $form_data['NAME_EN'],
        '##webform_fields##' => 'array()',
        '##webform_post_event_id##' => '',
        '##webform_post_template_id##' => 'array()',
    );

    if (isset($_POST['new'])) {
        // Prepare and check form properties
        if (file_exists(__DIR__ . '/../lib/forms/' . $form_data['ID'])) {
            $errors[] = Loc::getMessage('ml2webforms_edit_error_form_id_used_already');
        }
    } else {
    }

    $formFields = array();
    $replaceRequiredFunctions = array();
    foreach ($_POST['name'] as $nn => $fieldName) {
        if (!$fieldName) {
            continue;
        }
        if (isset($_POST['new'])) {
            $formFields[$fieldName] = array(
                'validators' => array(),
                'errorText' => array(),
            );
/*
            if (in_array((int)$formFields[$fieldName]['type'], array(WebForm::FIELD_TYPE_SELECT, WebForm::FIELD_TYPE_SELECT_MULTIPLE, WebForm::FIELD_TYPE_RADIO))) {
                $formFields[$fieldName]['list'] = array(
                    array('title' => array('ru' => 'Значение 1', 'en' => 'Value 1'), 'default' => true),
                    array('title' => array('ru' => 'Значение 2', 'en' => 'Value 2')),
                    array('title' => array('ru' => 'Значение 3', 'en' => 'Value 3')),
                );
            }
*/
        } else {
            $formFields[$fieldName] = $form_data['FIELDS'][$fieldName];
        }
        $formFields[$fieldName]['type'] = (int)$_POST['type'][$nn];
        $formFields[$fieldName]['value_type'] = (int)$_POST['value_type'][$nn];
        if (in_array((int)$formFields[$fieldName]['type'], array(WebForm::FIELD_TYPE_SELECT, WebForm::FIELD_TYPE_SELECT_MULTIPLE, WebForm::FIELD_TYPE_RADIO))) {
            $formFields[$fieldName]['list'] = array();
            $items = explode("|||", $_POST["type_list"][$nn]);
            $itemsEn = explode("|||", $_POST["type_list_en"][$nn]);
            $def = 0;
            if ($_POST["type_list_def"][$nn] > 0) {
                $def = $_POST["type_list_def"][$nn] + 0;
            }
            $cnt = max(count($items), count($itemsEn));
            for ($nn2=0;$nn2<$cnt;$nn2++) {
                $formFields[$fieldName]['list'][] = array('title'=>array('ru'=>$items[$nn2], 'en'=>$itemsEn[$nn2]));
                if ($nn2 == $def) {
                    $formFields[$fieldName]['list'][$nn2]['default'] = true;
                }
            }
            $formFields[$fieldName]['value_type'] = WebForm::FIELD_VALUE_TYPE_INTEGER;
        }
        
        $formFields[$fieldName]['filterable'] = (bool)!(int)$_POST['filterable'][$nn];
        if ((int)$_POST['required'][$nn] == 0 || (int)$_POST['required'][$nn] == 1) {
            $formFields[$fieldName]['required'] = (bool)(int)$_POST['required'][$nn];
        } elseif ((int)$_POST['required'][$nn] == 2) {
            if (is_object($formFields[$fieldName]['required'])) {
                $formFieldRequiredStr = Ml2WebFormsEdit::getFieldRequiredFunctionStr($fieldName, $form_data['ID']);
                $replaceRequiredFunctions[] = $formFieldRequiredStr;
                $formFields[$fieldName]['required'] = $formFieldRequiredStr;
            } else {
                $formFields[$fieldName]['required'] = 'function() { return false; }';
            }
        } else {
            $formFields[$fieldName]['required'] = false;
        }
        $formFields[$fieldName]['title'] = array(
            'ru' => $_POST['title_ru'][$nn],
            'en' => $_POST['title_en'][$nn],
        );
/*
        if (!isset($_POST['new']) && in_array((int)$formFields[$fieldName]['type'], array(WebForm::FIELD_TYPE_SELECT, WebForm::FIELD_TYPE_SELECT_MULTIPLE, WebForm::FIELD_TYPE_RADIO)) && !isset($formFields[$fieldName]['list'])) {
            $formFields[$fieldName]['list'] = array(
                array('title' => array('ru' => 'Значение 1', 'en' => 'Value 1'), 'default' => true),
                array('title' => array('ru' => 'Значение 2', 'en' => 'Value 2')),
                array('title' => array('ru' => 'Значение 3', 'en' => 'Value 3')),
            );
        }
*/
    }

    $typeStr = array(
        0 => 'WebForm::FIELD_TYPE_TEXT',
        1 => 'WebForm::FIELD_TYPE_SELECT',
        2 => 'WebForm::FIELD_TYPE_TEXTAREA',
        3 => 'WebForm::FIELD_TYPE_RADIO',
        4 => 'WebForm::FIELD_TYPE_HIDDEN',
        5 => 'WebForm::FIELD_TYPE_CHECKBOX',
        6 => 'WebForm::FIELD_TYPE_SELECT_MULTIPLE',
        7 => 'WebForm::FIELD_TYPE_FILE',
    );
    $valueTypeStr = array(
        0 => 'WebForm::FIELD_VALUE_TYPE_TEXT',
        1 => 'WebForm::FIELD_VALUE_TYPE_STRING',
        2 => 'WebForm::FIELD_VALUE_TYPE_INTEGER',
        3 => 'WebForm::FIELD_VALUE_TYPE_REAL',
        4 => 'WebForm::FIELD_VALUE_TYPE_DATE',
        5 => 'WebForm::FIELD_VALUE_TYPE_DATETIME',
    );
    $formFieldsStr = var_export($formFields, true);
    $formFieldsStr = str_replace('\'function() { return false; }\'', 'function() { return false; }', $formFieldsStr);
    foreach ($replaceRequiredFunctions as $functionStr) {
        $formFieldsStr = str_replace('\'' . $functionStr . '\'', $functionStr, $formFieldsStr);
    }
    foreach ($typeStr as $key => $value) {
        $formFieldsStr = str_replace('\'type\' => ' . $key, '\'type\' => ' . $value, $formFieldsStr);
    }
    foreach ($valueTypeStr as $key => $value) {
        $formFieldsStr = str_replace('\'value_type\' => ' . $key, '\'value_type\' => ' . $value, $formFieldsStr);
    }

    $tplKeys['##webform_fields##'] = $formFieldsStr;
    //print_r($tplKeys);exit;

    if ((isset($_POST['new']) || !isset($_POST['new']) && !$form_data['POST_EVENT']) && isset($_POST['CREATE_POST_EVENT'])) {
        $eventData = Ml2WebFormsEdit::generatePostEvent($form_data, $formFields);
        $obEventType->Add($eventData['ru']);
        $obEventType->Add($eventData['en']);
        $tplKeys['##webform_post_event_id##'] = $eventData['ru']['EVENT_NAME'];

        $eventTemplate = Ml2WebFormsEdit::generatePostTemplate($form_data, $eventData);

        $eventTemplateId = $obTemplate->Add($eventTemplate);
        $tplKeys['##webform_post_template_id##'] = 'array(' . (int)$eventTemplateId . ')';
    } else {
        $eventData = Ml2WebFormsEdit::generatePostEvent($form_data, $formFields);
        $tplKeys['##webform_post_event_id##'] = $eventData['ru']['EVENT_NAME'];
        if ($form_data['POST_EVENT']) {
            $obEventType->Update(array("EVENT_NAME" => $eventData['ru']['EVENT_NAME'], "LID" => 'ru'), $eventData['ru']);
            $obEventType->Update(array("EVENT_NAME" => $eventData['en']['EVENT_NAME'], "LID" => 'en'), $eventData['en']);
        }
        if (isset($_POST['CREATE_POST_EVENT'])) {
            $eventTemplate = Ml2WebFormsEdit::generatePostTemplate($form_data, $eventData);
            $eventTemplateId = $obTemplate->Add($eventTemplate);
            $tplIds = array();
            foreach ($form_data['POST_TEMPLATES'] as $postTemplate) {
                $tplIds[] = (int)$postTemplate['ID'];
            }
            if ((int)$eventTemplateId > 0) {
                $tplIds[] = (int)$eventTemplateId;
            }
            $tplKeys['##webform_post_template_id##'] = var_export($tplIds, true);
        }
    }

    if (isset($_POST['new'])) {
        Ml2WebFormsEdit::createTable($form_data['ID'], $formFields);

        if (count($errors) == 0) {
            // Copy and fill files for form
            $tplDir = __DIR__ . '/../lib/form_tpl';
            $formDir = __DIR__ . '/../lib/forms/' . $form_data['ID'];
            mkdir($formDir);
            CopyDirFiles($tplDir, $formDir, true, true);
            Ml2WebFormsEdit::fillFormFiles($formDir, $tplKeys);
        }
    } else {
        Ml2WebFormsEdit::reCreateTable($form_data['ID'], $formFields);

        if (count($errors) == 0) {
            // Copy and fill files for form
            $formDir = __DIR__ . '/../lib/forms/' . $form_data['ID'];
            copy($formDir . '/class.php', $formDir . '/class.php.' . date('YmdHis') . '.bak');

            $formClassStr = file_get_contents($formDir . '/class.php');
            $formFieldsStr = Ml2WebFormsEdit::getFieldsCodeStr($form_data['ID']);
            $formClassStr = str_replace($formFieldsStr, '##webform_fields##', $formClassStr);
            $formClassStr = preg_replace('/array\s*\(\s*##webform_fields##/Uis', "##webform_fields##;\r\n    ", $formClassStr);
            $formClassStr = preg_replace('/(protected\s+function\s+getPostEventId\s*\(\)\s*\{\s*return\s*\'|")([^\']*)(\'|";\s*})/Uis', '$1##webform_post_event_id##$3', $formClassStr);
            $formClassStr = preg_replace('/(protected\s+function\s+getPostEventTemplates\s*\(\)\s*\{\s*return\s*)([^\']*)(;\s*})/Uis', '$1 ##webform_post_template_id##$3', $formClassStr);
            file_put_contents($formDir . '/class.php', $formClassStr);

            rename($formDir . '/class.php', $formDir . '/class.php.tpl');

            Ml2WebFormsEdit::fillFormFiles($formDir, $tplKeys);
        }
    }

    if (count($errors) == 0) {
        LocalRedirect("ml2webforms_edit.php?id={$form_data['ID']}&lang=" . LANG);
    }
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aContext = array(
    array(
        "ICON" => "btn_list",
        "TEXT" => Loc::getMessage("MAIN_ADMIN_MENU_LIST"),
        "LINK" => "ml2webforms_admin.php?lang=".LANG,
        "TITLE" => Loc::getMessage("MAIN_ADMIN_MENU_LIST")
    ),
);

$context = new \CAdminContextMenu($aContext);
$context->Show();

if (count($errors) > 0) {
    $oAdminMessage = new \CAdminMessage(join("\n", $errors));
    echo $oAdminMessage->Show();
}

if (!$errorFatal) {
?>
    <form method="post" action="<?=$APPLICATION->GetCurUri()?>" name="form1_ml2webforms_edit" id="form1_ml2webforms_edit" enctype="multipart/form-data">
        <?=bitrix_sessid_post()?>
        <?echo GetFilterHiddens("filter_");?>
        <input type="hidden" name="Update" value="Y">
        <input type="hidden" name="from" value="<?echo htmlspecialchars($_REQUEST['from'])?>">
        <?if (strlen($_REQUEST['return_url']) > 0):?><input type="hidden" name="return_url" value="<?=htmlspecialchars($_REQUEST['return_url'])?>"><?endif?>
        <?
        $tabControl->Begin();

        $tabControl->BeginNextTab();

        if (strlen($ID) == 0) {
            ?>
            <input type="hidden" name="new">
            <?
        }
        echo MlAdminPanelBuilder::PrepareFieldHtml(
            Loc::getMessage('ml2webforms_edit_ID'),
            'ID',
            $form_data['ID'],
            'input_text',
            array(),
            array(),
            "",
            true,
            strlen($ID) > 0
        );

        echo MlAdminPanelBuilder::PrepareFieldHtml(
            Loc::getMessage('ml2webforms_edit_NAME_RU'),
            'NAME_RU',
            $form_data['NAME_RU'],
            'input_text'
        );

        echo MlAdminPanelBuilder::PrepareFieldHtml(
            Loc::getMessage('ml2webforms_edit_NAME_EN'),
            'NAME_EN',
            $form_data['NAME_EN'],
            'input_text'
        );

        if (count($form_data['POST_TEMPLATES']) > 0) {
            $tpls = '';
            foreach ($form_data['POST_TEMPLATES'] as $tpl) {
                $tpls .= '<a target="_blank" href="/bitrix/admin/message_edit.php?lang=' . LANGUAGE_ID . '&ID=' . $tpl['ID'] . '">[' . $tpl['ID'] . '] ' . $tpl['SUBJECT'] . '</a><br>';
            }
            ?>
            <tr id="tr_post_templates">
                <td><?=Loc::getMessage('ml2webforms_edit_POST_TEMPLATES')?>:</td>
                <td><?=$tpls?></td>
            </tr>
            <?
        }
        echo MlAdminPanelBuilder::PrepareFieldHtml(
            (strlen($ID) > 0 && is_array($form_data['POST_EVENT']) ? Loc::getMessage('ml2webforms_edit_ADD_POST_EVENT') : Loc::getMessage('ml2webforms_edit_CREATE_POST_EVENT')),
            'CREATE_POST_EVENT',
            !(int)(count($form_data['POST_TEMPLATES']) > 0),
            'checkbox',
            false,
            false,
            '',
            false
        );
        ?>
        <tr class="heading"><td colspan="2"><?=Loc::getMessage('ml2webforms_edit_fields')?></td></tr>
        <tr><td colspan="2">
            <div class="fields">
                <?if (count($form_data['FIELDS']) > 0) {
                    foreach ($form_data['FIELDS'] as $field => $params) {
                        //print_r($params);
                        $itemsList = array();
                        $itemsListEn = array();
                        $itemsListDef = 0;
                        if(isset($params["list"]) and is_array($params["list"])) {
                            foreach ($params["list"] as $item) {
                                if (isset($item["default"]) and $item["default"]) {
                                    $itemsListDef = count($itemsList);
                                }
                                $itemsList[] = htmlEntities($item["title"]["ru"], ENT_QUOTES);
                                $itemsListEn[] = htmlEntities($item["title"]["en"], ENT_QUOTES);
                            }
                            $itemsList = implode("|||", $itemsList);
                            $itemsListEn = implode("|||", $itemsListEn);
                        }
                        ?>
                        <div class="field">
                            <input type="text" name="name[]" placeholder="<?=Loc::getMessage('ml2webforms_edit_field_name')?>" value="<?=$field?>">
                            <input type="text" name="title_ru[]" placeholder="<?=Loc::getMessage('ml2webforms_edit_field_title_ru')?>" value="<?=$params['title']['ru']?>">
                            <input type="text" name="title_en[]" placeholder="<?=Loc::getMessage('ml2webforms_edit_field_title_en')?>" value="<?=$params['title']['en']?>">
                            <select name="type[]">
                                <option value=""><?=Loc::getMessage('ml2webforms_edit_field_type')?></option>
                                <option<?=$params['type'] == WebForm::FIELD_TYPE_TEXT ? ' selected="selected"' : ''?> value="<?=WebForm::FIELD_TYPE_TEXT?>"><?=Loc::getMessage('ml2webforms_edit_field_type_' . WebForm::FIELD_TYPE_TEXT)?></option>
                                <option<?=$params['type'] == WebForm::FIELD_TYPE_CHECKBOX ? ' selected="selected"' : ''?> value="<?=WebForm::FIELD_TYPE_CHECKBOX?>"><?=Loc::getMessage('ml2webforms_edit_field_type_' . WebForm::FIELD_TYPE_CHECKBOX)?></option>
                                <option<?=$params['type'] == WebForm::FIELD_TYPE_TEXTAREA ? ' selected="selected"' : ''?> value="<?=WebForm::FIELD_TYPE_TEXTAREA?>"><?=Loc::getMessage('ml2webforms_edit_field_type_' . WebForm::FIELD_TYPE_TEXTAREA)?></option>
                                <option<?=$params['type'] == WebForm::FIELD_TYPE_SELECT ? ' selected="selected"' : ''?> value="<?=WebForm::FIELD_TYPE_SELECT?>"><?=Loc::getMessage('ml2webforms_edit_field_type_' . WebForm::FIELD_TYPE_SELECT)?></option>
                                <option<?=$params['type'] == WebForm::FIELD_TYPE_SELECT_MULTIPLE ? ' selected="selected"' : ''?> value="<?=WebForm::FIELD_TYPE_SELECT_MULTIPLE?>"><?=Loc::getMessage('ml2webforms_edit_field_type_' . WebForm::FIELD_TYPE_SELECT_MULTIPLE)?></option>
                                <option<?=$params['type'] == WebForm::FIELD_TYPE_RADIO ? ' selected="selected"' : ''?> value="<?=WebForm::FIELD_TYPE_RADIO?>"><?=Loc::getMessage('ml2webforms_edit_field_type_' . WebForm::FIELD_TYPE_RADIO)?></option>
                                <option<?=$params['type'] == WebForm::FIELD_TYPE_FILE ? ' selected="selected"' : ''?> value="<?=WebForm::FIELD_TYPE_FILE?>"><?=Loc::getMessage('ml2webforms_edit_field_type_' . WebForm::FIELD_TYPE_FILE)?></option>
                                <option<?=$params['type'] == WebForm::FIELD_TYPE_HIDDEN ? ' selected="selected"' : ''?> value="<?=WebForm::FIELD_TYPE_HIDDEN?>"><?=Loc::getMessage('ml2webforms_edit_field_type_' . WebForm::FIELD_TYPE_HIDDEN)?></option>
                            </select>
                            <select name="value_type[]"<?=(in_array($params['type'], array(WebForm::FIELD_TYPE_SELECT, WebForm::FIELD_TYPE_SELECT_MULTIPLE, WebForm::FIELD_TYPE_RADIO))?' disabled="disabled"':'');?>>
                                <option value=""><?=Loc::getMessage('ml2webforms_edit_field_value_type')?></option>
                                <option<?=$params['value_type'] == WebForm::FIELD_VALUE_TYPE_TEXT ? ' selected="selected"' : ''?> value="<?=WebForm::FIELD_VALUE_TYPE_TEXT?>"><?=Loc::getMessage('ml2webforms_edit_field_value_type_' . WebForm::FIELD_VALUE_TYPE_TEXT)?></option>
                                <option<?=$params['value_type'] == WebForm::FIELD_VALUE_TYPE_STRING ? ' selected="selected"' : ''?> value="<?=WebForm::FIELD_VALUE_TYPE_STRING?>"><?=Loc::getMessage('ml2webforms_edit_field_value_type_' . WebForm::FIELD_VALUE_TYPE_STRING)?></option>
                                <option<?=$params['value_type'] == WebForm::FIELD_VALUE_TYPE_INTEGER ? ' selected="selected"' : ''?> value="<?=WebForm::FIELD_VALUE_TYPE_INTEGER?>"><?=Loc::getMessage('ml2webforms_edit_field_value_type_' . WebForm::FIELD_VALUE_TYPE_INTEGER)?></option>
                                <option<?=$params['value_type'] == WebForm::FIELD_VALUE_TYPE_REAL ? ' selected="selected"' : ''?> value="<?=WebForm::FIELD_VALUE_TYPE_REAL?>"><?=Loc::getMessage('ml2webforms_edit_field_value_type_' . WebForm::FIELD_VALUE_TYPE_REAL)?></option>
                                <option<?=$params['value_type'] == WebForm::FIELD_VALUE_TYPE_DATE ? ' selected="selected"' : ''?> value="<?=WebForm::FIELD_VALUE_TYPE_DATE?>"><?=Loc::getMessage('ml2webforms_edit_field_value_type_' . WebForm::FIELD_VALUE_TYPE_DATE)?></option>
                                <option<?=$params['value_type'] == WebForm::FIELD_VALUE_TYPE_DATETIME ? ' selected="selected"' : ''?> value="<?=WebForm::FIELD_VALUE_TYPE_DATETIME?>"><?=Loc::getMessage('ml2webforms_edit_field_value_type_' . WebForm::FIELD_VALUE_TYPE_DATETIME)?></option>
                            </select>
                            <select name="required[]">
                                <option<?=$params['required'] === false ? ' selected="selected"' : ''?> value="0"><?=Loc::getMessage('ml2webforms_edit_field_required_0')?></option>
                                <option<?=$params['required'] === true ? ' selected="selected"' : ''?> value="1"><?=Loc::getMessage('ml2webforms_edit_field_required_1')?></option>
                                <option<?=is_object($params['required']) ? ' selected="selected"' : ''?> value="2"><?=Loc::getMessage('ml2webforms_edit_field_required_2')?></option>
                            </select>
                            <select name="filterable[]">
                                <option<?=$params['filterable'] ? ' selected="selected"' : ''?> value="0"><?=Loc::getMessage('ml2webforms_edit_field_filterable_0')?></option>
                                <option<?=!$params['filterable'] ? ' selected="selected"' : ''?> value="1"><?=Loc::getMessage('ml2webforms_edit_field_filterable_1')?></option>
                            </select>
                            <input class="del" type="button" value="&times;">
                            <input type="hidden" name="type_list[]" value="<?if($itemsList):echo $itemsList;endif;?>">
                            <input type="hidden" name="type_list_en[]" value="<?if($itemsListEn):echo $itemsListEn;endif;?>">
                            <input type="hidden" name="type_list_def[]" value="<?=$itemsListDef;?>">
                            <?/*<div class="field_values">
                                <div class="value">
                                    <input type="text" name="name[]" placeholder="<?=Loc::getMessage('ml2webforms_edit_field_name')?>">
                                </div>
                            </div>*/?>
                        </div>
                        <?
                    }
                } else {
                ?>
                <div class="field">
                    <input type="text" name="name[]" placeholder="<?=Loc::getMessage('ml2webforms_edit_field_name')?>">
                    <input type="text" name="title_ru[]" placeholder="<?=Loc::getMessage('ml2webforms_edit_field_title_ru')?>">
                    <input type="text" name="title_en[]" placeholder="<?=Loc::getMessage('ml2webforms_edit_field_title_en')?>">
                    <select name="type[]">
                        <option value=""><?=Loc::getMessage('ml2webforms_edit_field_type')?></option>
                        <option value="<?=WebForm::FIELD_TYPE_TEXT?>"><?=Loc::getMessage('ml2webforms_edit_field_type_' . WebForm::FIELD_TYPE_TEXT)?></option>
                        <option value="<?=WebForm::FIELD_TYPE_CHECKBOX?>"><?=Loc::getMessage('ml2webforms_edit_field_type_' . WebForm::FIELD_TYPE_CHECKBOX)?></option>
                        <option value="<?=WebForm::FIELD_TYPE_TEXTAREA?>"><?=Loc::getMessage('ml2webforms_edit_field_type_' . WebForm::FIELD_TYPE_TEXTAREA)?></option>
                        <option value="<?=WebForm::FIELD_TYPE_SELECT?>"><?=Loc::getMessage('ml2webforms_edit_field_type_' . WebForm::FIELD_TYPE_SELECT)?></option>
                        <option value="<?=WebForm::FIELD_TYPE_SELECT_MULTIPLE?>"><?=Loc::getMessage('ml2webforms_edit_field_type_' . WebForm::FIELD_TYPE_SELECT_MULTIPLE)?></option>
                        <option value="<?=WebForm::FIELD_TYPE_RADIO?>"><?=Loc::getMessage('ml2webforms_edit_field_type_' . WebForm::FIELD_TYPE_RADIO)?></option>
                        <option value="<?=WebForm::FIELD_TYPE_FILE?>"><?=Loc::getMessage('ml2webforms_edit_field_type_' . WebForm::FIELD_TYPE_FILE)?></option>
                        <option value="<?=WebForm::FIELD_TYPE_HIDDEN?>"><?=Loc::getMessage('ml2webforms_edit_field_type_' . WebForm::FIELD_TYPE_HIDDEN)?></option>
                    </select>
                    <select name="value_type[]">
                        <option value=""><?=Loc::getMessage('ml2webforms_edit_field_value_type')?></option>
                        <option value="<?=WebForm::FIELD_VALUE_TYPE_TEXT?>"><?=Loc::getMessage('ml2webforms_edit_field_value_type_' . WebForm::FIELD_VALUE_TYPE_TEXT)?></option>
                        <option value="<?=WebForm::FIELD_VALUE_TYPE_STRING?>"><?=Loc::getMessage('ml2webforms_edit_field_value_type_' . WebForm::FIELD_VALUE_TYPE_STRING)?></option>
                        <option value="<?=WebForm::FIELD_VALUE_TYPE_INTEGER?>"><?=Loc::getMessage('ml2webforms_edit_field_value_type_' . WebForm::FIELD_VALUE_TYPE_INTEGER)?></option>
                        <option value="<?=WebForm::FIELD_VALUE_TYPE_REAL?>"><?=Loc::getMessage('ml2webforms_edit_field_value_type_' . WebForm::FIELD_VALUE_TYPE_REAL)?></option>
                        <option value="<?=WebForm::FIELD_VALUE_TYPE_DATE?>"><?=Loc::getMessage('ml2webforms_edit_field_value_type_' . WebForm::FIELD_VALUE_TYPE_DATE)?></option>
                        <option value="<?=WebForm::FIELD_VALUE_TYPE_DATETIME?>"><?=Loc::getMessage('ml2webforms_edit_field_value_type_' . WebForm::FIELD_VALUE_TYPE_DATETIME)?></option>
                    </select>
                    <select name="required[]">
                        <option value="0"><?=Loc::getMessage('ml2webforms_edit_field_required_0')?></option>
                        <option value="1"><?=Loc::getMessage('ml2webforms_edit_field_required_1')?></option>
                        <option value="2"><?=Loc::getMessage('ml2webforms_edit_field_required_2')?></option>
                    </select>
                    <select name="filterable[]">
                        <option value="0"><?=Loc::getMessage('ml2webforms_edit_field_filterable_0')?></option>
                        <option value="1"><?=Loc::getMessage('ml2webforms_edit_field_filterable_1')?></option>
                    </select>
                    <input class="del" type="button" value="&times;">
                    <input type="hidden" name="type_list[]" value="">
                    <input type="hidden" name="type_list_en[]" value="">
                    <input type="hidden" name="type_list_def[]" value="">
                    <?/*<div class="field_values">
                        <div class="value">
                            <input type="text" name="name[]" placeholder="<?=Loc::getMessage('ml2webforms_edit_field_name')?>">
                        </div>
                    </div>*/?>
                </div>
                <?
                }
                ?>
                <input class="add" type="button" value="<?=Loc::getMessage('ml2webforms_edit_field_add')?>">
            </div>
        </td></tr>
        <?
        $tabControl->EndTab();

        $tabControl->Buttons(
            array(
                "disabled" => $module_right < "W",
                "back_url" => "ml2webforms_admin.php?lang=".LANG
            )
        );

        $tabControl->End();
        ?>
    </form>
<?
}

if ($note = Loc::getMessage("ml2webforms_edit_note")) {
    echo BeginNote();
    echo $note;
    echo EndNote();
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
