<?
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################

/**
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @global CDatabase $DB
 * @global array $BX_GROUP_POLICY;
 */

require_once(dirname(__FILE__)."/../include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog_user.php");
define("HELP_FILE", "users/group_edit.php");

ClearVars();

if (!$USER->CanDoOperation('view_groups'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);
$modules = COperation::GetAllowedModules();
for($i = 0, $l = count($modules); $i < $l; $i++)
	IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/".$modules[$i]."/admin/task_description.php");

$strError = "";
$ID = intval($_REQUEST["ID"]);
$COPY_ID = intval($_REQUEST["COPY_ID"]);
if($COPY_ID > 0)
	$ID = $COPY_ID;

$modules = CModule::GetList();
$arModules = array();
while ($mr = $modules->Fetch())
	$arModules[] = $mr["ID"];

$arSites = array();
$rsSites = CSite::GetList($by="sort", $order="asc", array("ACTIVE" => "Y"));
while ($arSite = $rsSites->GetNext())
{
	$arSites["reference_id"][] = $arSite["ID"];
	$arSites["reference"][] = "[".$arSite["ID"]."] ".$arSite["NAME"];
}

$USER_COUNT = CUser::GetCount();
$USER_COUNT_MAX = 25;

$arBXGroupPolicy = array(
	"parent" => array(
		"SESSION_TIMEOUT" => "",
		"SESSION_IP_MASK" => "",
		"MAX_STORE_NUM" => "",
		"STORE_IP_MASK" => "",
		"STORE_TIMEOUT" => "",
		"CHECKWORD_TIMEOUT" => "",
		"PASSWORD_LENGTH" => "",
		"PASSWORD_UPPERCASE" => "N",
		"PASSWORD_LOWERCASE" => "N",
		"PASSWORD_DIGITS" => "N",
		"PASSWORD_PUNCTUATION" => "N",
		"LOGIN_ATTEMPTS" => "",
	),
	"low" => array(
		"SESSION_TIMEOUT" => 30, //minutes
		"SESSION_IP_MASK" => "0.0.0.0",
		"MAX_STORE_NUM" => 20,
		"STORE_IP_MASK" => "255.0.0.0",
		"STORE_TIMEOUT" => 60*24*93, //minutes
		"CHECKWORD_TIMEOUT" => 60*24*185, //minutes
		"PASSWORD_LENGTH" => 6,
		"PASSWORD_UPPERCASE" => "N",
		"PASSWORD_LOWERCASE" => "N",
		"PASSWORD_DIGITS" => "N",
		"PASSWORD_PUNCTUATION" => "N",
		"LOGIN_ATTEMPTS" => 0,
	),
	"middle" => array(
		"SESSION_TIMEOUT" => 20, //minutes
		"SESSION_IP_MASK" => "255.255.0.0",
		"MAX_STORE_NUM" => 10,
		"STORE_IP_MASK" => "255.255.0.0",
		"STORE_TIMEOUT" => 60*24*30, //minutes
		"CHECKWORD_TIMEOUT" => 60*24*1, //minutes
		"PASSWORD_LENGTH" => 8,
		"PASSWORD_UPPERCASE" => "Y",
		"PASSWORD_LOWERCASE" => "Y",
		"PASSWORD_DIGITS" => "Y",
		"PASSWORD_PUNCTUATION" => "N",
		"LOGIN_ATTEMPTS" => 0,
	),
	"high" => array(
		"SESSION_TIMEOUT" => 15, //minutes
		"SESSION_IP_MASK" => "255.255.255.255",
		"MAX_STORE_NUM" => 1,
		"STORE_IP_MASK" => "255.255.255.255",
		"STORE_TIMEOUT" => 60*24*3, //minutes
		"CHECKWORD_TIMEOUT" => 60, //minutes
		"PASSWORD_LENGTH" => 10,
		"PASSWORD_UPPERCASE" => "Y",
		"PASSWORD_LOWERCASE" => "Y",
		"PASSWORD_DIGITS" => "Y",
		"PASSWORD_PUNCTUATION" => "Y",
		"LOGIN_ATTEMPTS" => 3,
	),
);

$BX_GROUP_POLICY_CONTROLS = array(
	"SESSION_TIMEOUT"	=>	array("text", 5),
	"SESSION_IP_MASK"	=>	array("text", 20),
	"MAX_STORE_NUM"		=>	array("text", 5),
	"STORE_IP_MASK"		=>	array("text", 20),
	"STORE_TIMEOUT"		=>	array("text", 5),
	"CHECKWORD_TIMEOUT"	=>	array("text", 5),
	"PASSWORD_LENGTH"	=>	array("text", 5),
	"PASSWORD_UPPERCASE"	=>	array("checkbox", "Y"),
	"PASSWORD_LOWERCASE"	=>	array("checkbox", "Y"),
	"PASSWORD_DIGITS"	=>	array("checkbox", "Y"),
	"PASSWORD_PUNCTUATION"	=>	array("checkbox", "Y"),
	"LOGIN_ATTEMPTS"	=>	array("text", 5),
);

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB"), "ICON" => "group_edit", "TITLE" => GetMessage("MAIN_TAB_TITLE")),
	array("DIV" => "edit2", "TAB" => GetMessage("TAB_2"), "ICON" => "group_edit", "TITLE" => GetMessage('MUG_POLICY_TITLE')),
);
if($ID!=1 || $COPY_ID>0 || (COption::GetOptionString("main", "controller_member", "N") == "Y" && COption::GetOptionString("main", "~controller_limited_admin", "N") == "Y"))
{
	$aTabs[] = array("DIV" => "edit3", "TAB" => GetMessage("TAB_3"), "ICON" => "group_edit", "TITLE" => GetMessage("MODULE_RIGHTS"));
}
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if($_SERVER["REQUEST_METHOD"] == "POST" && ($_REQUEST["save"] <> '' || $_REQUEST["apply"] <> '') && $USER->CanDoOperation('edit_groups') && check_bitrix_sessid())
{
	if($ID <= 2 && $ID != 0)
		$ACTIVE = "Y";

	$group = new CGroup;

	$arGroupPolicy = array();
	foreach ($BX_GROUP_POLICY as $key => $value)
	{
		$curVal = ${"gp_".$key};
		$curValParent = ${"gp_".$key."_parent"};

		if ($curValParent != "Y")
			$arGroupPolicy[$key] = $curVal;
	}

	$arFields = array(
		"ACTIVE" => $_POST["ACTIVE"],
		"C_SORT" => $_POST["C_SORT"],
		"NAME" => $_POST["NAME"],
		"DESCRIPTION" => $_POST["DESCRIPTION"],
		"STRING_ID" => $_POST["STRING_ID"],
		"SECURITY_POLICY" => serialize($arGroupPolicy)
	);

	if ($USER_COUNT <= $USER_COUNT_MAX)
	{
		$USER_ID_NUMBER = intval($_REQUEST["USER_ID_NUMBER"]);
		$USER_ID = array();
		$ind = -1;
		for ($i = 0; $i <= $USER_ID_NUMBER; $i++)
		{
			if (${"USER_ID_ACT_".$i} == "Y")
			{
				$ind++;
				$USER_ID[$ind]["USER_ID"] = intval(${"USER_ID_".$i});
				$USER_ID[$ind]["DATE_ACTIVE_FROM"] = ${"USER_ID_FROM_".$i};
				$USER_ID[$ind]["DATE_ACTIVE_TO"] = ${"USER_ID_TO_".$i};
			}
		}

		if ($ID == 1 && $COPY_ID<=0)
		{
			$ind++;
			$USER_ID[$ind]["USER_ID"] = 1;
			$USER_ID[$ind]["DATE_ACTIVE_FROM"] = false;
			$USER_ID[$ind]["DATE_ACTIVE_TO"] = false;
		}

		$arFields["USER_ID"] = $USER_ID;
	}

	if($ID>0 && $COPY_ID<=0)
		$res = $group->Update($ID, $arFields);
	else
	{
		$ID = $group->Add($arFields);
		$res = ($ID>0);
		$new="Y";
	}

	$strError .= $group->LAST_ERROR;

	if (strlen($strError)<=0)
	{
		if (intval($ID) != 1 || (COption::GetOptionString("main", "controller_member", "N") == "Y" && COption::GetOptionString("main", "~controller_limited_admin", "N") == "Y"))
		{
			// set per module rights
			$arTasks = array();
			foreach ($arModules as $MID)
			{
				$moduleName = str_replace(".", "_", $MID);
				if(isset(${"TASKS_".$moduleName}))
				{
					$arTasks[$MID] = ${"TASKS_".$moduleName};
					$rt = CTask::GetLetter($arTasks[$MID]);
				}
				else
				{
					$rt = ${"RIGHTS_".$moduleName};
					$st = ${"SITES_".$moduleName};

// echo "Delete group rights for all sites<br>";
					$APPLICATION->DelGroupRight($MID, array($ID), false);
					foreach($arSites["reference_id"] as $site_id_tmp)
					{
// echo "Delete group rights for site ".$site_id_tmp."<br>";
						$APPLICATION->DelGroupRight($MID, array($ID), $site_id_tmp);
					}
				}

				if (
					is_array($rt)
					&& count($rt) > 0
				)
				{
					foreach ($rt as $i => $right)
					{
						if (strlen($right) > 0 && $right != "NOT_REF")
						{
// echo $MID." ".$ID." ".$right." ".$st[$i]."<br>";						
							$APPLICATION->SetGroupRight($MID, $ID, $right, (array_key_exists($i, $st) && strlen($st[$i]) > 0 && $st[$i] != "NOT_REF" ? $st[$i] : false));
						}
					}
				}
				elseif(!is_array($rt) && strlen($rt) > 0 && $rt != "NOT_REF")
					$APPLICATION->SetGroupRight($MID, $ID, $rt, false);
			}

			$arTasksModules = CTask::GetTasksInModules(false, false, 'module');
			$nID = COperation::GetIDByName('edit_subordinate_users');
			$nID2 = COperation::GetIDByName('view_subordinate_users');
			$arTaskIds = $arTasksModules['main'];
			$handle_subord = false;
			$l = count($arTaskIds);
			for ($i = 0; $i < $l; $i++)
			{
				if ($arTaskIds[$i]['ID'] == $arTasks['main'])
				{
					$arOpInTask = CTask::GetOperations($arTaskIds[$i]['ID']);
					if (in_array($nID, $arOpInTask) || in_array($nID2, $arOpInTask))
						$handle_subord = true;
					break;
				}
			}
			if ($handle_subord)
			{
				$arSubordinateGroups = (isset($_POST['subordinate_groups'])) ? $_POST['subordinate_groups'] : array();
				CGroup::SetSubordinateGroups($ID, $arSubordinateGroups);
			}
			else
			{
				CGroup::SetSubordinateGroups($ID);
			}

			$old_arTasks = CGroup::GetTasks($ID, true);
			if (count(array_diff($old_arTasks, $arTasks)) > 0 || count(array_diff($arTasks, $old_arTasks)) > 0)
				CGroup::SetTasks($ID, $arTasks);
		}

		if($USER->CanDoOperation('edit_groups') && $_REQUEST["save"] <> '')
			LocalRedirect("group_admin.php?lang=".LANGUAGE_ID);
		elseif($USER->CanDoOperation('edit_groups') && $_REQUEST["apply"] <> '')
			LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&ID=".$ID."&".$tabControl->ActiveTabParam());
		elseif($new == "Y")
			LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&ID=".$ID."&".$tabControl->ActiveTabParam());
	}
}

$str_USER_ID = array();

$z = CGroup::GetByID($ID, "N");
if(!$z->ExtractFields("str_"))
{
	$ID=0;
	$str_ACTIVE="Y";
	$str_C_SORT = 100;
}
else
{
	$dbUserGroup = CGroup::GetGroupUserEx($ID);
	while ($arUserGroup = $dbUserGroup->Fetch())
	{
		$str_USER_ID[intval($arUserGroup["USER_ID"])]["DATE_ACTIVE_FROM"] = $arUserGroup["DATE_ACTIVE_FROM"];
		$str_USER_ID[intval($arUserGroup["USER_ID"])]["DATE_ACTIVE_TO"] = $arUserGroup["DATE_ACTIVE_TO"];
	}
}

if (strlen($strError)>0)
{
	$DB->InitTableVarsForEdit("b_group", "", "str_");

	$USER_ID_NUMBER = intval($_REQUEST["USER_ID_NUMBER"]);
	$str_USER_ID = array();
	for ($i = 0; $i <= $USER_ID_NUMBER; $i++)
	{
		if (${"USER_ID_ACT_".$i} == "Y")
		{
			$str_USER_ID[intval(${"USER_ID_".$i})]["DATE_ACTIVE_FROM"] = ${"USER_ID_FROM_".$i};
			$str_USER_ID[intval(${"USER_ID_".$i})]["DATE_ACTIVE_TO"] = ${"USER_ID_TO_".$i};
		}
	}
}

if($ID <= 0 || $COPY_ID > 0)
	$APPLICATION->SetTitle(GetMessage("NEW_GROUP_TITLE"));
elseif($USER->CanDoOperation('edit_groups'))
	$APPLICATION->SetTitle(GetMessage("EDIT_GROUP_TITLE", array("#ID#" => $ID)));
else
	$APPLICATION->SetTitle(GetMessage("EDIT_GROUP_TITLE_VIEW", array("#ID#" => $ID)));
/***************************************************************************
HTML form
****************************************************************************/

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

$aMenu = array(
	array(
		"TEXT"	=> GetMessage("RECORD_LIST"),
		"TITLE"	=> GetMessage("RECORD_LIST_TITLE"),
		"LINK"	=> "/bitrix/admin/group_admin.php?lang=".LANGUAGE_ID."&set_default=Y",
		"ICON"	=> "btn_list"

	)
);

if($USER->CanDoOperation('edit_groups'))
{
	if(intval($ID)>0 && $COPY_ID<=0)
	{
		$aMenu[] = array("SEPARATOR"=>"Y");

		$aMenu[] = array(
			"TEXT"	=> GetMessage("MAIN_NEW_RECORD"),
			"TITLE"	=> GetMessage("MAIN_NEW_RECORD_TITLE"),
			"LINK"	=> "/bitrix/admin/group_edit.php?lang=".LANGUAGE_ID,
			"ICON"	=> "btn_new"
		);
		if($ID>1)
		{
			$aMenu[] = array(
				"TEXT"	=> GetMessage("MAIN_COPY_RECORD"),
				"TITLE"	=> GetMessage("MAIN_COPY_RECORD_TITLE"),
				"LINK"	=> "/bitrix/admin/group_edit.php?lang=".LANGUAGE_ID."&amp;COPY_ID=".$ID,
				"ICON"	=> "btn_copy"
			);
		}

		if($ID>2)
		{
			$aMenu[] = array(
				"TEXT"	=> GetMessage("MAIN_DELETE_RECORD"),
				"TITLE"	=> GetMessage("MAIN_DELETE_RECORD_TITLE"),
				"LINK"	=> "javascript:if(confirm('".CUtil::JSEscape(GetMessage("MAIN_DELETE_RECORD_CONF"))."')) window.location='/bitrix/admin/group_admin.php?ID=".$ID."&action=delete&lang=".LANGUAGE_ID."&".bitrix_sessid_get()."';",
				"ICON"	=> "btn_delete"
			);
		}
	}
}

$context = new CAdminContextMenu($aMenu);
$context->Show();
?>

<?CAdminMessage::ShowMessage($strError);?>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?" name="form1">
<?=bitrix_sessid_post()?>
<input type="hidden" name="lang" value="<?echo LANG?>">
<input type="hidden" name="ID" value="<?echo $ID?>">
<?if(strlen($COPY_ID)>0):?><input type="hidden" name="COPY_ID" value="<?echo htmlspecialcharsbx($COPY_ID)?>"><?endif?>
<?
$tabControl->Begin();

$tabControl->BeginNextTab();
?>
	<?if(strlen($str_TIMESTAMP_X)>0):?>
	<tr>
		<td><?echo GetMessage('LAST_UPDATE')?></td>
		<td><?echo $str_TIMESTAMP_X?></td>
	</tr>
	<? endif; ?>
	<?
	if ($ID > 0 && $ID != 2 && $COPY_ID<=0)
	{
		$dbGroupTmp = CGroup::GetByID($ID, "Y");
		if ($arGroupTmp = $dbGroupTmp->Fetch())
		{
			?>
			<tr>
				<td><?echo GetMessage('MAIN_TOTAL_USERS')?></td>
				<td><a href="user_admin.php?lang=<?=LANG?>&find_group_id[]=<?=$ID?>&set_filter=Y" title="<?=GetMessage("MAIN_VIEW_USER_GROUPS")?>"><?= intval($arGroupTmp["USERS"]) ?></a></td>
			</tr>
			<?
		}
	}
	?>
	<?if($ID>2 || $ID==0):?>
	<tr>
		<td><?echo GetMessage('ACTIVE')?></td>
		<td><input type="checkbox" name="ACTIVE" value="Y"<?if($str_ACTIVE=="Y")echo " checked"?>></td>
	</tr>
	<?endif;?>
	<tr>
		<td width="40%"><?=GetMessage("MAIN_C_SORT")?></td>
		<td width="60%"><input type="text" name="C_SORT" size="5" maxlength="18" value="<?echo $str_C_SORT?>"></td>
	</tr>
	<tr class="adm-detail-required-field">
		<td><?echo GetMessage('NAME')?></td>
		<td><input type="text" name="NAME" size="40" maxlength="255" value="<?=$str_NAME?>"></td>
	</tr>
	<tr>
		<td><?echo GetMessage('STRING_ID')?></td>
		<td><input type="text" name="STRING_ID" size="40" maxlength="255" value="<?=$str_STRING_ID?>"></td>
	</tr>
	<tr>
		<td class="adm-detail-valign-top"><?echo GetMessage('DESCRIPTION')?></td>
		<td><textarea name="DESCRIPTION" cols="30" rows="5"><?echo $str_DESCRIPTION?></textarea>
		</td>
	</tr>
	<?if($USER_COUNT<=$USER_COUNT_MAX && $ID!=2):?>
	<tr class="heading">
		<td colspan="2"><?echo GetMessage('USERS');?></td>
	<tr>
		<td colspan="2" align="center">
		<table border="0" cellpadding="0" cellspacing="0" class="internal">
			<tr class="heading">
				<td>&nbsp;</td>
				<td><?echo GetMessage("USER_LIST")?></td>
				<td><?=GetMessage('TBL_GROUP_DATE')?></td>
			</tr>
			<script>
			function CatGroupsActivate(obj, id)
			{
				var ed = eval("document.form1.USER_ID_FROM_" + id);
				var ed1 = eval("document.form1.USER_ID_TO_" + id);
				ed.disabled = !obj.checked;
				ed1.disabled = !obj.checked;
			}
			</script>
			<?
			$ind = -1;
			$dbUsers = CUser::GetList(($b="id"), ($o="asc"), array("ACTIVE" => "Y"));
			while ($arUsers = $dbUsers->Fetch())
			{
				$ind++;
				?>
				<tr>
					<td>
						<input type="hidden" name="USER_ID_<?=$ind?>" value="<?=$arUsers["ID"] ?>">
						<input type="checkbox" name="USER_ID_ACT_<?=$ind?>" id="USER_ID_ACT_ID_<?=$ind?>" value="Y" <?
							if (array_key_exists($arUsers["ID"], $str_USER_ID))
								echo " checked";
							?> OnChange="CatGroupsActivate(this, <?=$ind?>)"></td>
					<td><label for="USER_ID_ACT_ID_<?=$ind?>">[<a href="/bitrix/admin/user_edit.php?ID=<?=$arUsers["ID"]?>&lang=<?=LANGUAGE_ID?>" title="<?=GetMessage("MAIN_VIEW_USER")?>"><?=$arUsers["ID"]?></a>] (<?=htmlspecialcharsbx($arUsers["LOGIN"])?>) <?=htmlspecialcharsbx($arUsers["NAME"])?> <?=htmlspecialcharsbx($arUsers["LAST_NAME"])?></label></td>
					<td>
						<?=CalendarDate("USER_ID_FROM_".$ind, (array_key_exists($arUsers["ID"], $str_USER_ID) ? htmlspecialcharsbx($str_USER_ID[$arUsers["ID"]]["DATE_ACTIVE_FROM"]) : ""), "form1", "10", (array_key_exists($arUsers["ID"], $str_USER_ID) ? " " : " disabled"))?>
						<?=CalendarDate("USER_ID_TO_".$ind, (array_key_exists($arUsers["ID"], $str_USER_ID) ? htmlspecialcharsbx($str_USER_ID[$arUsers["ID"]]["DATE_ACTIVE_TO"]) : ""), "form1", "10", (array_key_exists($arUsers["ID"], $str_USER_ID) ? " " : " disabled"))?>
					</td>
				</tr>
				<?
			}
			?>
		</table><input type="hidden" name="USER_ID_NUMBER" value="<?= $ind ?>"><?
		//echo SelectBoxM("USER_ID[]", CUser::GetDropDownList(), $str_USER_ID, "", false, 20);
		?></td>
	</tr>
	<?endif?>
<?$tabControl->BeginNextTab();?>
	<script>
	var arGroupPolicy = <?echo CUtil::PhpToJSObject($arBXGroupPolicy)?>;

	function gpLevel()
	{
		var i;

		var el = document.form1.gp_level;
		if (el.selectedIndex > 0)
		{
			var sel = el.options[el.selectedIndex].value;

			for(i in arGroupPolicy[sel])
			{
				var el1 = eval("document.form1.gp_" + i + "_parent");
				var el2 = eval("document.form1.gp_" + i + "");

				if (sel == "parent")
					el1.checked = true;
				else
					el1.checked = false;

				gpChangeParent(i);

				if(el2.type.toLowerCase() == 'checkbox')
					el2.checked = arGroupPolicy[sel][i] == "Y";
				else
					el2.value = arGroupPolicy[sel][i];
			}
		}
	}

	function gpChangeParent(key)
	{
		var el1 = eval("document.form1.gp_" + key + "_parent");
		var el2 = eval("document.form1.gp_" + key + "");
		el2.disabled = el1.checked;
	}

	function gpSetLevel(level)
	{
		var el = document.form1.gp_level;
		for (var i=0, len = el.options.length; i<len; i++)
			if(el.options[i].value == level)
				el.selectedIndex = i;
		return el.options[el.selectedIndex].value;
	}

	function ip2long(ip)
	{
		var bytes = [];
		var res = false;
		if (ip.match(/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/))
		{
			bytes = ip.split('.');
			res =
				bytes[0] * 16777216 +
				bytes[1] * 65536 +
				bytes[2] * 256 +
				bytes[3] * 1;
		}

		return res;
	}

	function gpSync()
	{
		var el = document.form1.gp_level;
		var level = {
			low: 0,
			middle: 0,
			high: 0,
			disabled: 0,
			total: 0
		};
		for(var key in arGroupPolicy['parent'])
		{
			var el1 = eval("document.form1.gp_" + key + "_parent");
			var el2 = eval("document.form1.gp_" + key + "");
			if(el1.checked)
			{
				level.disabled++;
				level.total++;
			}
			else
			{
				switch(key)
				{
				case "SESSION_TIMEOUT":
				case "MAX_STORE_NUM":
				case "STORE_TIMEOUT":
				case "CHECKWORD_TIMEOUT":
					level.total++;
					if(parseInt(el2.value) <= parseInt(arGroupPolicy['high'][key]))
						level.high++;
					else if(parseInt(el2.value) <= parseInt(arGroupPolicy['middle'][key]))
						level.middle++;
					else
						level.low++;
					break;
				case "PASSWORD_LENGTH":
					level.total++;
					if(parseInt(el2.value) >= parseInt(arGroupPolicy['high'][key]))
						level.high++;
					else if(parseInt(el2.value) >= parseInt(arGroupPolicy['middle'][key]))
						level.middle++;
					else
						level.low++;
					break;
				case "LOGIN_ATTEMPTS":
					level.total++;
					if(parseInt(el2.value) > 0)
					{
						if(parseInt(el2.value) <= parseInt(arGroupPolicy['high'][key]))
							level.high++;
						else if(parseInt(el2.value) <= parseInt(arGroupPolicy['middle'][key]))
							level.middle++;
						else
							level.low++;
					}
					else
					{
						if(parseInt(arGroupPolicy['high'][key]) <= 0)
							level.high++;
						else if(parseInt(arGroupPolicy['middle'][key]) <= 0)
							level.middle++;
						else
							level.low++;
					}
					break;
				case "PASSWORD_UPPERCASE":
				case "PASSWORD_LOWERCASE":
				case "PASSWORD_DIGITS":
				case "PASSWORD_PUNCTUATION":
					level.total++;
					if(el2.checked)
					{
						if(arGroupPolicy['high'][key] == 'Y')
							level.high++;
						else if(arGroupPolicy['middle'][key] == 'Y')
							level.middle++;
						else
							level.low++;
					}
					else
					{
						if(arGroupPolicy['high'][key] == 'N')
							level.high++;
						else if(arGroupPolicy['middle'][key] == 'N')
							level.middle++;
						else
							level.low++;
					}
					break;
				case "SESSION_IP_MASK":
				case "STORE_IP_MASK":
					level.total++;
					var gp_ip = ip2long(el2.value);
					var high_ip = ip2long(arGroupPolicy['high'][key]);
					var middle_ip = ip2long(arGroupPolicy['middle'][key]);
					if((gp_ip & high_ip) == (0xFFFFFFFF & high_ip))
						level.high++;
					else if((gp_ip & middle_ip) == (0xFFFFFFFF & middle_ip))
						level.middle++;
					else
						level.low++;
				default:
					break;
				}
			}
		}

		if(level.low > 0)
			gpSetLevel('low');
		else if(level.middle > 0)
			gpSetLevel('middle');
		else if(level.high > 0 && level.high == level.total)
			gpSetLevel('high');
		else if(level.disabled > 0 && level.disabled == level.total)
			gpSetLevel('parent');
		else
			gpSetLevel('');
	}
	</script>
	<tr>
		<td width="40%"><?=GetMessage('MUG_PREDEFINED_FIELD')?>:</td>
		<td width="60%">
			<select name="gp_level" OnChange="gpLevel()">
				<option value=""><?=GetMessage('MUG_SELECT_LEVEL1')?></option>
				<option value="parent"><?=GetMessage('MUG_PREDEFINED_PARENT')?></option>
				<option value="low"><?=GetMessage('MUG_PREDEFINED_LOW')?></option>
				<option value="middle"><?=GetMessage('MUG_PREDEFINED_MIDDLE')?></option>
				<option value="high"><?=GetMessage('MUG_PREDEFINED_HIGH')?></option>
			</select>
		</td>
	</tr>
	<?
	$arGroupPolicy = unserialize(htmlspecialcharsback($str_SECURITY_POLICY));
	if (!is_array($arGroupPolicy))
		$arGroupPolicy = array();

	foreach ($BX_GROUP_POLICY as $key => $value)
	{
		$curVal = $arGroupPolicy[$key];
		$curValParent = !array_key_exists($key, $arGroupPolicy);
		if (strlen($strError) > 0)
		{
			$curVal = ${"gp_".$key};
			$curValParent = ((${"gp_".$key."_parent"} == "Y") ? True : False);
		}
		?>
		<tr valign="top">
			<td><label for="gp_<?echo $key?>"><?
			$gpTitle = GetMessage("GP_".$key);
			if (strlen($gpTitle) <= 0)
				$gpTitle = $key;

			echo $gpTitle;
			?></label>:</td>
			<td>

				<input type="checkbox" name="gp_<?= $key ?>_parent" OnClick="gpChangeParent('<?= $key ?>'); gpSync();" id="id_gp_<?= $key ?>_parent" value="Y"<?if ($curValParent) echo "checked";?>><label for="id_gp_<?= $key ?>_parent"><?=GetMessage('MUG_GP_PARENT')?></label><br>
				<?$arControl = $BX_GROUP_POLICY_CONTROLS[$key];
				switch($arControl[0])
				{
				case "checkbox":
					?>
					<input type="checkbox" onclick="gpSync();" id="gp_<?= $key ?>" name="gp_<?= $key ?>" value="<?= htmlspecialcharsbx($arControl[1]) ?>" <?if($curVal === $arControl[1]) echo "checked"?> <?if ($curValParent) echo "disabled";?>>
					<?
					break;
				default:
					?>
					<input type="text" onchange="gpSync();" name="gp_<?= $key ?>" value="<?= htmlspecialcharsbx($curVal) ?>" size="<?echo ($arControl[1] > 0? $arControl[1]: "30")?>" <?if ($curValParent) echo "disabled";?>>
					<?
				}
				?>
			</td>
		</tr>
		<?
	}
	?>

	<?if (intval($ID)!=1 || $COPY_ID>0 || (COption::GetOptionString("main", "controller_member", "N") == "Y" && COption::GetOptionString("main", "~controller_limited_admin", "N") == "Y")) :?>
	<?$tabControl->BeginNextTab();?>
	<tr>
		<td width="40%"><?=GetMessage("KERNEL")?></td>
		<td width="60%">
			<script>var arSubordTasks = [];</script>
			<?
			$arTasksModules = CTask::GetTasksInModules(true,false,'module');
			$arTasks = CGroup::GetTasks($ID,true);
			$nID = COperation::GetIDByName('edit_subordinate_users');
			$nID2 = COperation::GetIDByName('view_subordinate_users');
			if($strError <> '')
				$v = $_REQUEST["TASKS_main"];
			else
				$v = (isset($arTasks['main'])) ? $arTasks['main'] : false;
			echo SelectBoxFromArray("TASKS_main", $arTasksModules['main'], $v, GetMessage("DEFAULT"));

			$show_subord = false;
			$arTaskIds = $arTasksModules['main']['reference_id'];
			$l = count($arTaskIds);
			for ($i=0;$i<$l;$i++)
			{
				$arOpInTask = CTask::GetOperations($arTaskIds[$i]);
				if (in_array($nID, $arOpInTask) || in_array($nID2, $arOpInTask))
				{
					?><script>
					arSubordTasks.push(<?=$arTaskIds[$i]?>);
					</script><?
					if ($arTaskIds[$i] == $v)
						$show_subord = true;
				}
			}
			?>
			<script>
			document.getElementById('TASKS_main').onchange = function()
			{
				var show = false;
				for (var s = 0; s < arSubordTasks.length; s++)
				{
					if (arSubordTasks[s].toString() == this.value)
					{
						show = true;
						break;
					}
				}
				var row = document.getElementById('__subordinate_groups_tr');
				if (show)
				{
					try{row.style.display = 'table-row';}
					catch(e){row.style.display = 'block';}
				}
				else
					row.style.display = 'none';
			};
			</script>
		</td>
	</tr>
	<tr valign="top" id="__subordinate_groups_tr" <?echo $show_subord ? '' : 'style="display:none"';?>>
		<td width="50%"><?=GetMessage('SUBORDINATE_GROUPS');?>:</td>
		<td width="50%">
			<select id="subordinate_groups" name="subordinate_groups[]" multiple size="6">
			<?
			$arSubordinateGroups = CGroup::GetSubordinateGroups($ID);
			$rsData = CGroup::GetList($by, $order, array(), "Y");
			while($arRes = $rsData->Fetch())
			{
				if ($arRes['ID'] == 1 || $arRes['ID'] == $ID)
					continue;
				if($strError <> '' && is_array($_REQUEST["subordinate_groups"]))
					$bSel = (in_array($arRes['ID'], $_REQUEST["subordinate_groups"]));
				else
					$bSel = (in_array($arRes['ID'], $arSubordinateGroups) || $arRes['ID'] == 2);
				?><option value="<?=$arRes['ID']?>"<?echo ($bSel? ' selected' : '')?>><? echo '['.$arRes['ID'].'] '.htmlspecialcharsbx($arRes['NAME'])?></option><?
			}
			?>
			</select>
			<script>
			document.getElementById('subordinate_groups').onblur = function(e)
			{
				for (var i=0, len = this.options.length; i<len; i++)
				{
					if (this.options[i].value == 2)
					{
						this.options[i].selected = 'selected';
						break;
					}
				}
			};

			function settingsAddRights(a)
			{
				var tbl = BX.findPreviousSibling(a, { 'tag': 'table'});
				tbl = BX.findChild(tbl, {'tag': 'tbody'});

				var tableRow = tbl.rows[tbl.rows.length-1].cloneNode(true);

				tableRow.style.display = "table-row";
				tbl.insertBefore(tableRow, tbl.rows[tbl.rows.length-1]);

				var selRights = BX.findChild(tableRow.cells[1], { 'tag': 'select'}, true);
				selRights.selectedIndex = 0;

				selSites = BX.findChild(tableRow.cells[0], { 'tag': 'select'}, true);
				selSites.selectedIndex = 0;
			}

			function settingsDeleteRow(el)
			{
				BX.remove(BX.findParent(el, {'tag': 'tr'}));
				return false;
			}

			</script>
			
		</td>
	</tr>
	<?
	foreach($arModules as $MID):
		if($MID == "main")
			continue;
		/** @var CModule $module */
		if (($module = CModule::CreateModuleObject($MID))):
			if ($module->MODULE_GROUP_RIGHTS == "Y") :
				$moduleName = str_replace(".", "_", $MID);
	?>
	<tr>
		<td><?=$module->MODULE_NAME.":"?></td>
		<td>
		<?
			$ar = array();
			if (isset($arTasksModules[$MID]))
			{
				if($strError <> '')
					$v = $_REQUEST["TASKS_".$moduleName];
				else
					$v = (isset($arTasks[$MID])) ? $arTasks[$MID] : false;

				echo SelectBoxFromArray("TASKS_".$moduleName, $arTasksModules[$MID], $v, GetMessage("DEFAULT"));
			}
			else
			{
				?><table><tbody><?

				if (method_exists($module, "GetModuleRightList"))
					$ar = call_user_func(array($module, "GetModuleRightList"));
				else
					$ar = $APPLICATION->GetDefaultRightList();

				if($strError <> '')
				{
					$k_site = 0;
					if (array_key_exists("SITES_".$moduleName, $_REQUEST) && is_array($_REQUEST["SITES_".$moduleName]))
						foreach($_REQUEST["SITES_".$moduleName] as $k => $site_id_k)
							if ($site_id_k == "")
							{
								$k_site = $k;
								break;
							}

					$v = $_REQUEST["RIGHTS_".$moduleName][$k_site];
				}
				else
					$v = $APPLICATION->GetGroupRight($MID, array($ID), "N", "N", false);

				?><tr><?
				$use_padding = false;
				if (
					array_key_exists("use_site", $ar)
					&& is_array($ar["use_site"])
					&& count($ar["use_site"]) > 0
				)
				{
				
					$arRightsUseSites = array("reference_id" => array(), "reference" => array());
					foreach ($ar["reference_id"] as $i => $right_tmp)
					{
						if (in_array($right_tmp, $ar["use_site"]))
						{
							$arRightsUseSites["reference_id"][] = $ar["reference_id"][$i];
							$arRightsUseSites["reference"][] = $ar["reference"][$i];
						}
					}

					$use_padding = true;
					?><td style="padding: 3px;"><input type="hidden" name="SITES_<?=$moduleName?>[]" value=""><?
						echo GetMessage("ALL_SITES");
					?></td><?
				}

				?><td <?if ($use_padding):?>style="padding: 3px;"<?endif;?>><?
					echo SelectBoxFromArray("RIGHTS_".$moduleName."[]", $ar, htmlspecialcharsbx($v), GetMessage("DEFAULT"));
				?></td>
				<td></td><?
				
				?></tr><?

				if (
					array_key_exists("use_site", $ar)
					&& is_array($ar["use_site"])
					&& count($ar["use_site"]) > 0
				)
				{
					foreach ($arSites["reference_id"] as $i => $site_id_tmp)
					{
						$site_selected = false;
						if($strError <> '')
						{
							if (array_key_exists("SITES_".$moduleName, $_REQUEST) && is_array($_REQUEST["SITES_".$moduleName]))
							{
								$k_site = false;
								foreach($_REQUEST["SITES_".$moduleName] as $k => $site_id_k)
									if ($site_id_k == $site_id_tmp)
									{
										$k_site = $k;
										$site_selected = $site_id_k;
										break;
									}
							}

							if ($k_site === false)
								$v = false;
							else
								$v = $_REQUEST["RIGHTS_".$moduleName][$k_site];
						}
						else
						{
							$v = $APPLICATION->GetGroupRight($MID, array($ID), "N", "N", $site_id_tmp);
							$site_selected = $site_id_tmp;
						}

						if (strlen($v) > 0)
						{
							?><tr>
								<td style="padding: 3px;">
								<? echo SelectBoxFromArray("SITES_".$moduleName."[]", $arSites, $site_selected, GetMessage("SITE_SELECT")); ?>
								</td><?
								?><td style="padding: 3px;"><?
									echo SelectBoxFromArray("RIGHTS_".$moduleName."[]", $arRightsUseSites, htmlspecialcharsbx($v), GetMessage("DEFAULT"));
								?></td>
								<td style="padding: 3px;"><a href="javascript:void(0)" onClick="settingsDeleteRow(this)"><img src="/bitrix/themes/.default/images/actions/delete_button.gif" border="0" width="20" height="20"></a></td>
							</tr><?					
						}
					}
					
					?>
					<tr id="hidden-rights-row" style="display: none;">
						<td style="padding: 3px;"><? echo SelectBoxFromArray("SITES_".$moduleName."[]", $arSites, "", GetMessage("SITE_SELECT")); ?></td>
						<td style="padding: 3px;"><? echo SelectBoxFromArray("RIGHTS_".$moduleName."[]", $arRightsUseSites, "", GetMessage("DEFAULT"));?></td>
						<td><a href="javascript:void(0)" onClick="settingsDeleteRow(this)"><img src="/bitrix/themes/.default/images/actions/delete_button.gif" border="0" width="20" height="20"></a></td>
					</tr>
					<?
				}

				?></tbody></table><?				

			}

		if (
			array_key_exists("use_site", $ar)
			&& is_array($ar["use_site"])
			&& count($ar["use_site"]) > 0
		)
		{
			?><a href="javascript:void(0)" onclick="settingsAddRights(this)" class="bx-action-href"><?echo GetMessage("RIGHTS_ADD")?></a><?
		}
		?></td>
	</tr>
	<?
			endif;
		endif;
	endforeach;
	?>
	<?endif;?>
<?
$tabControl->Buttons(array("disabled" => !$USER->CanDoOperation('edit_groups'), "back_url"=>"group_admin.php?lang=".LANGUAGE_ID));
$tabControl->End();
?>

</form>
<script>
	gpSync();
</script>

<?require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");?>
