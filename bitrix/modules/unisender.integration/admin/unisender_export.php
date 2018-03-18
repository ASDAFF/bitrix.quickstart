<?php
set_time_limit(0);
ob_implicit_flush(1);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
if (empty($_POST['export'])) LocalRedirect("/bitrix/admin/unisender_index.php");
$APPLICATION->SetTitle("UniSender Import...");
require_once ($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

require_once $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/unisender.integration/include.php";

IncludeModuleLangFile(__FILE__);

$API_KEY = COption::GetOptionString($module_id, "UNISENDER_API_KEY");
if (empty($API_KEY))
{
	echo "<span class=\"errortext\">".GetMessage("UNI_API_KEY_EMPTY", array("#MODULE_ID#"=>$module_id))."</span>";
}
else
{
	echo "<span class=\"notetext\">".GetMessage("UNI_IMPORT_START")."</span><br/>";
	$API = new UniAPI($API_KEY);
	if (($fields = $API->getFields(array('string', 'text')))!==false)
	{
		$params = array();
		// ????? ???????
		$params['field_names'][] = 'email';
		if (!empty($_POST['phone'])) $params['field_names'][] = "phone";
		if (!empty($_POST['name'])) $params['field_names'][] = $fields[intval($_POST['name'])]['name'];
		if (!empty($_POST['fam'])) $params['field_names'][] = $fields[intval($_POST['fam'])]['name'];
		if (!empty($_POST['otch'])) $params['field_names'][] = $fields[intval($_POST['otch'])]['name'];
		$params['field_names'][] = "email_confirm_time";
		if (!empty($_POST['phone']))
		{
			$params['field_names'][] = "phone_add_time";
			$params['field_names'][] = "phone_list_ids";
		}
		
		$params['field_names'][] = "email_list_ids";
		
		
		$filter = Array
		(
			"ACTIVE"              => "Y",
			"GROUPS_ID"           => $groups
		);
		$rsUsers = CUser::GetList(($by="id"), ($order="desc"), $filter); // ???????? ?????????????
		$i = 0;
		while($user = $rsUsers->Fetch())
		{
			//echo $user['ID']." / ".$user['DATE_REGISTER']."<br/>";
			$date_register = ConvertDateTime($user['DATE_REGISTER'], "YYYY-MM-DD HH:MI:SS", "ru");
			$list_id = intval($_POST['list_id']);
			$data = array();
			$data[] = $user['EMAIL'];
			if (!empty($_POST['phone'])) $data[] = $user['PERSONAL_MOBILE'];
			if (!empty($_POST['name'])) $data[] = $user['NAME'];
			if (!empty($_POST['fam'])) $data[] = $user['LAST_NAME'];
			if (!empty($_POST['otch'])) $data[] = $user['SECOND_NAME'];
			$data[] = $date_register;
			if (!empty($_POST['phone']))
			{
				$data[] = $date_register;
				$data[] = $list_id;
			}
			
			$data[] = $list_id;
			
			$params['data'][] = $data;
			$i++;
			if ($i%400==0)
			{
				if (($res = $API->importContacts($params))!==false)
				{
					echo GetMessage("UNI_IMPORT_STAT", array("#TOTAL#"=>$res['total'], "#INSERTED#"=>$res['inserted'], "#UPDATED#"=>$res['updated'], "#DELETED#"=>$res['deleted'], "#NEW_EMAILS#"=>$res['new_emails']))."<br/>";
					unset($params['data']);
					$params['data'] = array();
					sleep(1);
					flush();
				}
				else
				{
					$API->showError();
					break;
				}
			}
		}
		
		if ($i%400!=0)
		{
			if (($res = $API->importContacts($params))!==false)
			{
				echo GetMessage("UNI_IMPORT_STAT", array("#TOTAL#"=>$res['total'], "#INSERTED#"=>$res['inserted'], "#UPDATED#"=>$res['updated'], "#DELETED#"=>$res['deleted'], "#NEW_EMAILS#"=>$res['new_emails']))."<br/>";
				flush();
			}
			else {
				$API->showError();
			}
		}
		
		echo "<span class=\"notetext\">".GetMessage("UNI_IMPORT_FINISH")."</span><br/><br/>";
		
		echo GetMessage("UNI_END_LINK", array("#LIST_ID#"=>$list_id));
		//print_r($params);
		//echo urldecode(http_build_query($params));
	}
	else
	{
		$API->showError();
	}
}

require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
?>