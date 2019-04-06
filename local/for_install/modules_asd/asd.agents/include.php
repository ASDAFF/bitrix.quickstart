<?php

IncludeModuleLangFile(__FILE__);

class CASDagents {

	public static $ok = '';

	public static function OnBeforePrologHandler() {

		if (!array_key_exists('action', $_REQUEST)) {
			return;
		}

		global $DB, $USER, $APPLICATION;

		if ($_REQUEST['action'] == 'asd_agent_run' && $USER->CanDoOperation('view_other_settings') &&
				$GLOBALS['APPLICATION']->GetCurPage() == '/bitrix/admin/agent_list.php' && check_bitrix_sessid() &&
				$_REQUEST['mode'] == 'list' && is_numeric($_REQUEST['agent_id'])
		) {
			$strSql=
				"SELECT "
					. "	ID, NAME, AGENT_INTERVAL, IS_PERIOD, MODULE_ID "
				. "FROM b_agent "
				. "WHERE ID='".intval($_REQUEST['agent_id'])."' "
				. "ORDER BY SORT desc";

			$dbRes = $DB->Query($strSql);
			if ($arAgent = $dbRes->Fetch()) {
				@set_time_limit(0);
				if(strlen($arAgent['MODULE_ID']) > 0 && $arAgent['MODULE_ID'] != 'main'){
					if(!CModule::IncludeModule($arAgent['MODULE_ID'])) {
						return;
					}
				}

				CTimeZone::Disable();
				$eval_result = '';
				eval("\$eval_result=".$arAgent['NAME']);
				CTimeZone::Enable();

				if(strlen($eval_result)){
					CASDagents::$ok = GetMessage('ASD_RUN_OK');
				}
				unset($_REQUEST['action']);
			}
		}
	}

	public static function OnAdminListDisplayHandler(&$list) {

		if($GLOBALS['APPLICATION']->GetCurPage() == '/bitrix/admin/agent_list.php'){

			if (strlen(CASDagents::$ok)) {
				$message = new CAdminMessage(array('TYPE' => 'OK', 'MESSAGE' => CASDagents::$ok));
				echo $message->Show();
			}

			$lAdmin = new CAdminList($list->table_id, $list->sort);

			foreach ($list->aRows as $id => $v) {
				$arNewActions = array();
				foreach ($v->aActions as $i => $act) {
							if ($act['ICON'] == 'delete') {
								$arNewActions[] = array(
									'ICON' => '',
									'TEXT' => GetMessage('ASD_ACTION_RUN'),
									'ACTION' => $lAdmin->ActionDoGroup($v->id, 'asd_agent_run','&lang='.LANG.'&agent_id='.$v->id),
								);
							}
							$arNewActions[] = $act;
				}
				$v->aActions = $arNewActions;
			}
		}
	}
}