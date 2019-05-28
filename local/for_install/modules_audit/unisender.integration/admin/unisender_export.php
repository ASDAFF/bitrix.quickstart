<?php
set_time_limit(0);
ob_implicit_flush(1);
$module_id = 'unisender.integration';
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/prolog_admin_after.php');
require_once $_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/'.$module_id.'/include.php';

if (empty($_POST['export'])) {
    LocalRedirect('/bitrix/admin/unisender_index.php');
}

IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage('UNI_EXPORT_START'));
$API_KEY = COption::GetOptionString($module_id, 'UNISENDER_API_KEY');

if ($API_KEY !== '') {
    $API = new UniAPI($API_KEY);
    $lists = $API->getLists();
    $uniFields = $API->getFields();
    $userFields = Unisender::getUserFields();

    if (!is_array($lists) || !is_array($uniFields)) {
        $API->showError();
    } else {

        $fieldIterator = 8;
        $list_id = (int)$_POST['list_id'];
        $response = array();

        $params = array();
        $params['double_optin'] = 1;
        $params['field_names[0]'] = 'email';
        $params['field_names[1]'] = 'email_status';
        $params['field_names[2]'] = 'email_add_time';
        $params['field_names[3]'] = 'email_list_ids';

        if (!empty($_POST['phone'])) {
            $params['field_names[4]'] = 'phone';
            $params['field_names[5]'] = 'phone_status';
            $params['field_names[6]'] = 'phone_add_time';
            $params['field_names[7]'] = 'phone_list_ids';
        }

        $fieldId = $fieldIterator;
        foreach ($_POST['fields'] as $name => $userField) {
            $params['field_names['.$fieldId.']'] = $name;
            $fieldId++;
        }

        $groups = implode(',', $_POST['groups']);
        $filter = array(
            'ACTIVE' => 'Y',
            'GROUPS_ID' => $groups
        );
        $by = 'id';
        $order = 'desc';
        $rsUsers = CUser::GetList($by, $order, $filter, array('SELECT' => array('UF_*')));

        $i = 0;
        while ($user = $rsUsers->Fetch()) {
            $currId = 'data['.$i.']';

            $data = array(
                $currId.'[0]' => $user['EMAIL'],
                $currId.'[1]' => 'active',
                $currId.'[2]' => ConvertDateTime($user['DATE_REGISTER'], 'YYYY-MM-DD HH:MI:SS'),
                $currId.'[3]' => $list_id
            );
            if (!empty($_POST['phone']) && !empty($user[$_POST['phone']])) {
                $data = array_merge($data, array(
                    $currId.'[4]' => $user[$_POST['phone']],
                    $currId.'[5]' => 'active',
                    $currId.'[6]' => $data[$currId.'[2]'],
                    $currId.'[7]' => $list_id
                ));
            }

            $fieldId = $fieldIterator;
            foreach ($_POST['fields'] as $name => $userField) {
                if (!empty($user[$userField])) {
                    $data[$currId.'['.$fieldId.']'] = $user[$userField];
                }
                $fieldId++;
            }

            $params = array_merge($params, $data);

            $i++;
            if ($i >= 500) {
                $result = $API->importContacts($params);
                if ($result === false) {
                    $API->showError();
                    break;
                } else {
                    unset($params['data']);
                    $i = 0;
                    foreach ($result as $name => $value) {
                        if (!isset($response[$name])) {
                            $response[$name] = $value;
                        } else {
                            $response[$name] += $value;
                        }
                    }
                }
            }
        }

        if (!empty($params)) {
            $result = $API->importContacts($params);
            if ($result === false) {
                $API->showError();
            } else {
                unset($params['data']);
                $i = 0;
                foreach ($result as $name => $value) {
                    if (!isset($response[$name])) {
                        $response[$name] = $value;
                    } else {
                        $response[$name] += $value;
                    }
                }
            }
        }

        if (!$API->getError()) {
            echo '<p>' . GetMessage('UNI_EXPORT_STAT', array(
                    '#TOTAL#' => $response['total'],
                    '#INSERTED#' => $response['inserted'],
                    '#UPDATED#' => $response['updated'],
                    '#NEW_EMAILS#' => $response['new_emails']))
                . '</p>';

            if (!empty($response['logs'])) {
                echo '<p><b>' . GetMessage('UNI_EXPORT_LOG_TITLE') . ':</b><ul>';
                foreach ($response['logs'] as $log) {
                    echo '<li>' . $log . '</li>';
                }
                echo '</ul></p>';
            }
            echo '<span class="notetext">' . GetMessage('UNI_EXPORT_FINISH') . '</span><br/>';
            echo GetMessage('UNI_END_LINK', array('#LIST_ID#' => $list_id));
        }
    }

} else {
    echo '<span class="errortext">' . GetMessage('UNI_API_KEY_EMPTY', array('#MODULE_ID#' => $module_id)) . '</span>';
}

require_once($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/epilog_admin.php");
?>