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


global $APPLICATION, $DB;
$PING_RIGHT = $APPLICATION->GetGroupRight("altasib.ping");

if($PING_RIGHT=="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$RIGHT = $APPLICATION->GetGroupRight("altasib.ping");
if($RIGHT=="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);
$err_mess = "File: ".__FILE__."<br>Line: ";

$sTableID = "altasib_table_ping";
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
	if(!is_array($arID))
		$arID = $_REQUEST['ID'];

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
					$res = $DB->Query("DELETE FROM `altasib_table_ping` WHERE `COUNT`='".$DB->ForSql($ID)."'");		
						break;

				case "ping":

					$res = $DB->Query("SELECT * FROM `altasib_table_ping` WHERE `COUNT`='".$DB->ForSql($ID)."'");
					$arDataall = array();
					$arbadping = array();
					$argoodpingsite = array();
					while($arping = $res->Fetch()){
						
						$result = CAltasibping::SendPing($arping["NAME"], $arping["URL"], array()/*, $arping["SITE_ID"], $arping["ERROR"]*/);
						$arURL = $result["URL"];
						unset($result["URL"]);
						$i=0;
						$j=0;
					
						foreach ($result as $key => $ping)
						{
							if($ping == "OK"){
								$j++;
							} else {
								$arbadping[$arping["COUNT"]][] = $arURL[$key];
							}
							$arping["RESULT"] = $ping;
							$arping["SEACH"] = $arURL[$key];
							$arping["DATE"] = date('Y-m-d');
							$arping["TIME"] = date("H:i:s");
							$arDataall[] = $arping;
							$i++;
						}
						if ($j == $i){
							$argoodpingsite = $arping["COUNT"];
						}
					}
					
					if(!empty($arDataall)){
						foreach ($arDataall as $data){
							$res = $DB->Query("INSERT INTO `altasib_ping_log` (
										ID,
										SITE_ID,
										DATE,
										TIME,
										NAME,
										URL ,
										SEACH,
										RESULT
								)
								VALUES
								(".intval($data["ID"]).",'".$DB->ForSql($data["SITE_ID"])."','".$DB->ForSql($data["DATE"])."', '".$DB->ForSql($data["TIME"])."', '".$DB->ForSql($data["NAME"])."', '".$DB->ForSql($data["URL"])."','".$DB->ForSql($data["SEACH"])."', '".$DB->ForSql($data['RESULT'])."')
							");
						}
					}
					
					if(count($argoodpingsite) > 0){
						$res = $DB->Query("DELETE FROM `altasib_table_ping` WHERE `COUNT`='".$DB->ForSql($argoodpingsite)."'");
					}
					if(count($arbadping)>0){
				
						foreach ($arbadping as $key => $data){
							$error = serialize($data);
					  
							$res = $DB->Query("UPDATE `altasib_table_ping` SET `ERROR`='".$error."' WHERE `COUNT`=".intval($key));
						}
					}
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
	array("id"=>"ERROR", "content"=> GetMessage("ALX_PING_ERROR"), "sort"=>"ERROR", "default"=>true),
);

$lAdmin->AddHeaders($arHeaders);

global $DB;

//$dbResultList = $DB->Query("SELECT * FROM `altasib_table_ping` WHERE `A` = 1 ORDER BY ".$DB->ForSql($by)." ".$DB->ForSql($order));
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
	if(!empty($arList["ERROR"])){
		$row->AddField("ERROR", implode('<br />', unserialize($arList["ERROR"])));
	}

	$arActions = Array();
	$arActions[] = array("ICON"=>"move", "TEXT"=>GetMessage("ALX_PING_SET"), "ACTION"=>$lAdmin->ActionRedirect("altasib_ping.php?action=ping&ID[]=".$arList["COUNT"]."&lang=".LANG.GetFilterParams("filter_", false).""), "DEFAULT"=>true);
	$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("ALX_DEL"), "ACTION"=>$lAdmin->ActionRedirect("altasib_ping.php?action=delete&ID[]=".$arList["COUNT"]."&lang=".LANG.GetFilterParams("filter_", false).""), "DEFAULT"=>true);
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

$arGroupActionsTmp['ping'] = Array(
	'value' => 'ping',
	'name' => GetMessage("ALX_PING_SET")
);
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