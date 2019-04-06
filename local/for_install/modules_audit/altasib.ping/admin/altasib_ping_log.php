<?
#################################################
#   Company developer: ALTASIB                  #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2011 ALTASIB             #
#################################################

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/csv_data.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.ping/general/ping.php");

global $APPLICATION;
global $DB;
$PING_RIGHT = $APPLICATION->GetGroupRight("altasib.ping");
if($PING_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$RIGHT = $APPLICATION->GetGroupRight("altasib.ping");
if($RIGHT=="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);
$err_mess = "File: ".__FILE__."<br>Line: ";

$sTableID = "altasib_ping_log";
$oSort = new CAdminSorting($sTableID, "NAME", "ASC");
$lAdmin = new CAdminList($sTableID, $oSort);
$arFilterFields = array(
	"filter_SITE_ID",
	"filter_COUNT",
	"filter_ID",
	"filter_NAME",
	"filter_SEACH",
	"filter_RESULT",
	"filter_date_active_from",
	"filter_date_active_to"
);

$lAdmin->InitFilter($arFilterFields);

$arFilter = array();

if (!empty($filter_SITE_ID) && $filter_SITE_ID!='NOT_REF')
	$arFilter["SITE_ID"] = $filter_SITE_ID;
if (!empty($filter_COUNT))
	$arFilter["COUNT"] = $filter_COUNT;
if (!empty($filter_ID))
	$arFilter["ID"] = $filter_ID;
if (!empty($filter_NAME))
	$arFilter["?NAME"] = $filter_NAME;
if (!empty($filter_SEACH))
	$arFilter["SEACH"] = $filter_SEACH;
if (!empty($filter_RESULT))
	$arFilter["RESULT"] = $filter_RESULT;
if (!empty($filter_date_active_from)){
	$arData = explode(".",$filter_date_active_from);

	$newData = $arData[2]."-".$arData[1]."-".$arData[0];
		$arFilter[">=DATE"] = $newData;

}
if (!empty($filter_date_active_to)){
	$arData = explode(".",$filter_date_active_to);
	$newData = $arData[2]."-".$arData[1]."-".$arData[0];
		$arFilter["<=DATE"] = $newData;
	
}
	
if ($arID = $lAdmin->GroupAction() || count($_REQUEST['ID']>0))
{

if(!is_array($arID)) $arID = $_REQUEST['ID'];

	if ($_REQUEST['action_target']=='selected')
	{
		$arID = array();
	}
	
	if(!empty($arID)){
		foreach ($arID as $ID)
		{
			if (strlen($ID) <= 0)
				continue;
	
			switch ($_REQUEST['action'])
			{
				case "delete":
					$res = $DB->Query("DELETE FROM `altasib_ping_log` WHERE `COUNT`='".$DB->ForSql($ID)."'");
						break;
				default:
					break;
			}
		}
	}
}


$arHeaders = array(
	array("id"=>"ID", "content"=>GetMessage("ALX_PING_POS"), "sort"=>"COUNT", "default"=>true),
	array("id"=>"SITE_ID", "content"=> GetMessage("ALX_PING_SITE"), "sort"=>"SITE_ID", "default"=>true),
	array("id"=>"DATE", "content"=> GetMessage("ALX_PING_DATE"), "sort"=>"DATE", "default"=>true),
	array("id"=>"TIME", "content"=> GetMessage("ALX_PING_TIME"), "sort"=>"TIME", "default"=>true),
	array("id"=>"COUNT", "content"=> GetMessage("ALX_PING_ID_ELEM"), "sort"=>"ID", "default"=>true),
	array("id"=>"URL", "content"=> GetMessage("ALX_PING_LINK"), "sort"=>"NAME", "default"=>true),
	array("id"=>"SEACH", "content"=> GetMessage("ALX_PING_SEACH"), "sort"=>"SEACH", "default"=>true),
	array("id"=>"RESULT", "content"=> GetMessage("ALX_PING_RESULT"), "sort"=>"RESULT", "default"=>true),
);

$lAdmin->AddHeaders($arHeaders);


global $DB;
//$dbResultList = $res = $DB->Query("SELECT * FROM `altasib_ping_log` ORDER BY ".$DB->ForSql($by)." ".$DB->ForSql($order));
$dbResultList = CAltasibping::GetList($sTableID, $arFilter, array('*'), array($by=>strtoupper($order)));
$dbResultList = new CAdminResult($dbResultList, $sTableID);
$dbResultList->NavStart();
$lAdmin->NavText($dbResultList->GetNavPrint('Page'));

while($arList = $dbResultList->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($arList["COUNT"], $arList);
	
	$row->AddField("ID", $arList["COUNT"]);
	$row->AddCheckField("ACTIVE");
	$row->AddField("SITE_ID", $arList["SITE_ID"]);
	$row->AddField("DATE", $arList["DATE"]);
	$row->AddField("TIME", $arList["TIME"]);
	$row->AddField("COUNT", $arList["ID"]);
	$row->AddField("URL", '<a target="_blank" href="'.$arList["URL"].'">'.$arList["NAME"].'</a>');
	$row->AddField("SEACH", $arList["SEACH"]);
	$row->AddField("RESULT", $arList["RESULT"]);

	$arActions = Array();
	$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("ALX_DEL"), "ACTION"=>$lAdmin->ActionRedirect("altasib_ping_log.php?action=delete&ID[]=".$arList["COUNT"]."&lang=".LANG.GetFilterParams("filter_", false).""), "DEFAULT"=>true);
	$row->AddActions($arActions);
}


if($dbResultList)
{
	$lAdmin->AddFooter(
		array(
			array(
				"title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"),
				"value" => $dbResultList->SelectedRowsCount()
			),
			array(
				"counter" => true,
				"title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"),
				"value" => "0"
			),
		)
	);
}



$arGroupActionsTmp['del'] = Array(
	'value' => 'delete', 
	'name' => GetMessage("ALX_DEL")
);


$lAdmin->AddGroupActionTable($arGroupActionsTmp);

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("PING_MENU_SET_PING_TITLE"));
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
?>
<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<?
$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		GetMessage("ALX_PING_POS_TITLE"),
		GetMessage("ALX_PING_PERIOD"),
		GetMessage("ALX_PING_ID_ELEM_TITLE"),
		GetMessage("ALX_PING_NAME"),
		GetMessage("ALX_PING_SEACH"),
		GetMessage("ALX_PING_RESULT"),
	)
);

$oFilter->Begin();
?>
	<tr>
		<td><?= GetMessage("ALX_PING_SITE") ?>:</td>
		<td>
			<?echo CSite::SelectBox("filter_SITE_ID", $filter_SITE_ID, "(".GetMessage("ALX_PING_ALL").")"); ?>
		</td>
	</tr>
	<tr>
		<td><?= GetMessage("ALX_PING_POS_TITLE") ?>:</td>
		<td>
			<input type="text" name="filter_COUNT" value="<?echo htmlspecialcharsex($filter_COUNT)?>" size="50">
		</td>
	</tr>
	
	<tr>
		<td><?echo GetMessage("ALX_PING_PERIOD").":"?></td>
		<td><?echo CalendarPeriod("filter_date_active_from", $filter_date_active_from, "filter_date_active_to", $filter_date_active_to, "find_form")?></td>
	</tr>
	
	<tr>
		<td><?= GetMessage("ALX_PING_ID_ELEM_TITLE") ?>:</td>
		<td>
			<input type="text" name="filter_ID" size="50" value="<?echo htmlspecialcharsex($filter_ID)?>" size="50"> 
		</td>
	</tr>
	<tr>
		<td><?= GetMessage("ALX_PING_NAME") ?>:</td>
		<td>
			<input type="text" name="filter_NAME" size="50" value="<?echo htmlspecialcharsex($filter_NAME)?>" size="30"> 
		</td>
	</tr>
	<tr>
		<td><?= GetMessage("ALX_PING_SEACH") ?>:</td>
		<td>
		<?
		$urls = COption::GetOptionString("altasib.ping", "send_blog_ping_address");
		if(count($arURLping)>0){
			$arUrls = $arURLping;
		} else {
			$arUrls = explode("\r\n", $urls);
		}
		
		
		?><select name="filter_SEACH">
					<option value=""><?= htmlspecialcharsex("(".GetMessage("ALX_PING_ALL").")") ?></option>
				<?foreach($arUrls as $pos=>$v){?>	
					<option value="<?=$v?>"<?if ($filter_active===$pos) echo " selected"?>><?=htmlspecialcharsex($v) ?></option>
				<?}?>

			</select>
		</td>
	</tr>
	<tr>
		<td><? echo GetMessage('ALX_PING_RESULT'); ?>:</td>
		<td>
			<select name="filter_RESULT">
				<option value=""><?= htmlspecialcharsex("(".GetMessage("ALX_PING_ALL").")") ?></option>
				<option value="OK"<?if ($filter_active=="Y") echo " selected"?>><?= htmlspecialcharsex(GetMessage("ALX_PING_ÎÊ_PING")) ?></option>
				<option value="Server error ping"<?if ($filter_active=="N") echo " selected"?>><?= htmlspecialcharsex(GetMessage("ALX_PING_ERROR_PING")) ?></option>
			</select>
		</td>
	</tr>
<?
$oFilter->Buttons(
	array(
		"table_id" => $sTableID,
		"url" => $APPLICATION->GetCurPage(),
		"form" => "find_form"
	)
);
$oFilter->End();
?>
</form>

<?$lAdmin->DisplayList();?>
<?
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
?>