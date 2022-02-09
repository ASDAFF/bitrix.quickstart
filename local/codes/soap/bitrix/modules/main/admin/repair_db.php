<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2013 Bitrix

 * @global CMain $APPLICATION
 * @global CUser $USER
 * @global CDatabase $DB
 */
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/bx_root.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/tools.php");

function repair_db()
{
	/**
	 * Defined in dbconn.php
	 *
	 * @param $DBType
	 * @param $DBHost
	 * @param $DBName
	 * @param $DBLogin
	 * @param $DBPassword
	 */
	@include($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/dbconn.php");
	if($DBType == "mysql")
	{
		function microtime_float()
		{
			list($usec, $sec) = explode(" ", microtime());
			return ((float)$usec + (float)$sec);
		}

		$start = microtime_float();
		if(DBPersistent)
			$link_rdb = mysql_pconnect($DBHost, $DBLogin, $DBPassword);
		else
			$link_rdb = mysql_connect($DBHost, $DBLogin, $DBPassword);

		if (!$link_rdb)
			die('<span style="color:red;">'.GetMessage("RDB_CONNECT_ERROR").': '. mysql_error().'</span>');

		mysql_select_db($DBName, $link_rdb);

		$result = mysql_query('show table status');
		?>
		<table cellspacing="0" cellpadding="0" border="0" class="list-table" width="0%">
			<tr class="head">
				<td><?=GetMessage("RDB_TABLE_NAME")?></td>
				<td><?=GetMessage("RDB_ROWS_COUNT")?></td>
				<td><?=GetMessage("RDB_TABLE_SIZE")?></td>
				<td><?=GetMessage("RDB_CHECK_RESULT")?></td>
				<td><?=GetMessage("RDB_REPAIR_RESULT")?></td>
			</tr>

		<?
		while($arResult = mysql_fetch_array($result))
		{
			echo "<tr>";
			echo "<td>".htmlspecialcharsbx($arResult["Name"])."</td>";
			echo "<td align='right'>".$arResult["Rows"]."</td>";
			echo "<td align='right'>".number_format($arResult["Data_length"], 0, ',', ' ')."</td>";

			if(
				(empty($arResult["Type"]) && strlen($arResult["Comment"]) > 0 && empty($arResult["Engine"]))
				|| (isset($arResult["Type"]) && (strtoupper($arResult["Type"]) == "MYISAM" || strtoupper($arResult["Type"]) == "INNODB"))
				|| (isset($arResult["Engine"]) && (strtoupper($arResult["Engine"]) == "MYISAM" || strtoupper($arResult["Engine"]) == "INNODB"))
			)
			{
				echo "<td>";
				$query = 'CHECK TABLE `'.$arResult["Name"].'`';
				$status = mysql_query($query);
				$i=0;
				$toRepair = "";
				while($arStatus = mysql_fetch_array($status))
				{
					if($i>0) echo "<br>";
					$i++;
					echo "[".$arStatus["Msg_type"]."]&nbsp;";
					if($arStatus["Msg_type"]=="status" || $arStatus["Msg_type"]=="info")
						echo "<span style='color:green;'>";
					else
						echo "<span style='color:red;'>";
					echo htmlspecialcharsbx($arStatus["Msg_text"])."</span>";
					flush();

					if($arStatus["Msg_type"]=="error" || $arStatus["Msg_type"]=="warning")
						$toRepair = $arResult["Name"];
				}
				echo "</td>";
				if(!empty($toRepair))
				{
					echo "<td>";
					$j=0;

					$queryR = 'REPAIR TABLE `'.$toRepair.'`';
					$repair = mysql_query($queryR);
					$toCheck = "";
					while($repair && ($arRepair = mysql_fetch_array($repair)))
					{
						if($j>0) echo "<br>";
						echo "[Repair&nbsp;".$arRepair["Msg_type"]."]&nbsp;";
						if($arRepair["Msg_type"]=="status" || $arRepair["Msg_type"]=="info")
							echo "<span style='color:green;'>";
						else
							echo "<span style='color:red;'>";
						echo htmlspecialcharsbx($arRepair["Msg_text"])."</span>";
						$j++;
						flush();
						$toCheck = $toRepair;
					}
					if(!empty($toCheck))
					{
						$queryC = 'CHECK TABLE `'.$toCheck.'`';
						$statusC = mysql_query($queryC);
						while($arStatusC = mysql_fetch_array($statusC))
						{
							echo "<br>";
							echo "[Check&nbsp;".$arStatusC["Msg_type"]."]&nbsp;";
							if($arStatusC["Msg_type"]=="status" || $arStatusC["Msg_type"]=="info")
								echo "<span style='color:green;'>";
							else
								echo "<span style='color:red;'>";
							echo htmlspecialcharsbx($arStatusC["Msg_text"])."</span>";
							flush();
						}
					}
					echo "</td>";
				}
				else
					echo "<td>&nbsp;</td>";
				flush();
			}
			else
			{
				if(!empty($arResult["Type"]))
					echo "<td>".$arResult["Type"]."</td>";
				else
					echo "<td>".$arResult["Engine"]."</td>";
				echo "<td>&nbsp;</td>";
			}
			echo "</tr>";
		}
		?>
		<tr class="head"><td colspan="5"><?echo "<b>".GetMessage("RDB_EXEC_TIME")." </b>".round((microtime_float()-$start),5).GetMessage("RDB_SEC");?></td></tr>
		</table>
		<?

	}
	else
		echo "<span style='color:red;'>".GetMessage('RDB_DATABASE_ERROR')."</span>";
}

function show_tip()
{
	?>
	<form name="check" action="">
	<input type="submit" value="<?=GetMessage("RDB_CHECK_TABLES")?>" class="adm-btn-save">
	<input type="hidden" value="Y" name="check_tables">
	<?
	if(!isset($_REQUEST["login"]) && !isset($_REQUEST["password"]))
		echo bitrix_sessid_post();
	if(isset($_REQUEST["login"]))
		echo '<input type="hidden" value="'.htmlspecialcharsbx($_REQUEST["login"]).'" name="login">';
	if(isset($_REQUEST["password"]))
		echo '<input type="hidden" value="'.htmlspecialcharsbx($_REQUEST["password"]).'" name="password">';
	if(isset($_REQUEST["lang"]))
		echo '<input type="hidden" value="'.htmlspecialcharsbx($_REQUEST["lang"]).'" name="lang">';
	echo '</form>';
}

if(isset($_REQUEST["login"]) && isset($_REQUEST["password"]) && $_REQUEST["login"] <> '' && $_REQUEST["password"] <> '')
{
	/**
	 * Defined in dbconn.php
	 *
	 * @param $DBType
	 * @param $DBLogin
	 * @param $DBPassword
	 */
	include($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/dbconn.php");

	if($_REQUEST["login"] == $DBLogin && $_REQUEST["password"] == $DBPassword)
	{
		include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/lang/en/admin/repair_db.php");
		?>
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
		<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
		<title><?=GetMessage("RDB_REPAIR_DATABASE")?></title>
		<link rel="stylesheet" type="text/css" href="/bitrix/themes/.default/adminstyles.css">
		</head>
		<body>
		<div style="padding:10px;">
		<?
		if($DBType == "mysql")
		{
			if(isset($_REQUEST["check_tables"]) && $_REQUEST["check_tables"]=="Y")
			{
				repair_db();
			}
			else
			{
				?>
				<p><?=GetMessage("RDB_TIP_1")?></p>
				<p><span style="color:red;"><?=GetMessage("RDB_TIP_2")?><br><?=GetMessage("RDB_TIP_3")?></span></p>
				<?
				show_tip();
			}
		}
		else
			echo "<span style='color:red;'>".GetMessage('RDB_DATABASE_ERROR')."</span>";

		?>
		</div>
		</body></html>
<?
	}
}
else
{
	require_once(dirname(__FILE__)."/../include/prolog_admin_before.php");
	define("HELP_FILE", "utilities/repair_db.php");
	IncludeModuleLangFile(__FILE__);

	if(!$USER->CanDoOperation('edit_php'))
	{
		$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
	}
	elseif(isset($_REQUEST["table_name"]) && check_bitrix_sessid())
	{
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");

		@set_time_limit(0);

		$table_name = trim($_REQUEST["table_name"]);

		if(strlen($table_name) > 0)
		{
			$arTables = array();
			$rsTables = $DB->Query("show table status");
			while($arTable = $rsTables->Fetch())
			{
				$arTables[$arTable["Name"]] = $arTable;
			}
			ksort($arTables);
			$tables_count = count($arTables);

			if($table_name=="start|")
			{
				$arTable = array_shift($arTables);
				CAdminMessage::ShowMessage(array("MESSAGE"=>htmlspecialcharsbx($arTable["Name"]), "TYPE"=>"OK", "HTML"=>true, "DETAILS"=>GetMessage("RDB_OPTIMIZE_PROGRESS", array("#done#" => 1, "#todo#" => $tables_count))));
				?>
				<script>setTimeout("Optimize('<?echo CUtil::JSEscape($arTable["Name"])?>')", 100);</script>
				<?
			}
			else
			{
				if(substr($table_name, 0, 2) === "o|")
				{
					$op = "optimize";
					$table_name = substr($table_name, 2);
				}
				elseif(substr($table_name, 0, 2) === "a|")
				{
					$op = "analyze";
					$table_name = substr($table_name, 2);
				}
				else
				{
					$op = "check";
				}

				foreach($arTables as $TableName => $arTable)
				{
					if($TableName == $table_name)
						break;
					unset($arTables[$TableName]);
				}

				if(count($arTables) > 0)
				{
					$arTable = array_shift($arTables);

					$bCheckOK = true;
					$start_check = getmicrotime();
					if($op == "check")
					{
						$rsStatus = $DB->Query('check table `'.$arTable["Name"].'`');
						if($arStatus = $rsStatus->Fetch($rsStatus))
						{
							if($arStatus["Msg_type"]=="error" || $arStatus["Msg_type"]=="warning")
								$bCheckOK = false;
						}
					}
					$end_check = getmicrotime();
					$check_time = $end_check - $start_check;

					//When check time was less when 5 seconds we'll optimize and analyze table on the same hit
					if($bCheckOK && ($check_time < 5))
					{
						if($op == "check" || $op == "optimize")
							$rsStatus = $DB->Query('optimize table `'.$arTable["Name"].'`');
						if($op == "check" || $op == "analyze")
							$rsStatus = $DB->Query('analyze table `'.$arTable["Name"].'`');
					}

					if(!$bCheckOK)
					{
						CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("RDB_OPTIMIZE_ERROR"), "TYPE"=>"ERROR", "HTML"=>true, "DETAILS"=>GetMessage("RDB_OPTIMIZE_CHECK_FIRST", array("#table_name#" => htmlspecialcharsbx($arTable["Name"])))));
					}
					elseif(count($arTables) > 0)
					{
						if($check_time < 5)
						{
							$arTable = array_shift($arTables);
							CAdminMessage::ShowMessage(array("MESSAGE"=>htmlspecialcharsbx($arTable["Name"]), "TYPE"=>"OK", "HTML"=>true, "DETAILS"=>GetMessage("RDB_OPTIMIZE_PROGRESS", array("#done#" => $tables_count-count($arTables), "#todo#" => $tables_count))));
							?><script>setTimeout("Optimize('<?echo CUtil::JSEscape($arTable["Name"])?>')", 100);</script><?
						}
						elseif($op == "check") //otherwise step optimize
						{
							CAdminMessage::ShowMessage(array("MESSAGE"=>htmlspecialcharsbx($arTable["Name"])." - ".GetMessage("RDB_OPTIMIZE_OPTIMIZE"), "TYPE"=>"OK", "HTML"=>true, "DETAILS"=>GetMessage("RDB_OPTIMIZE_PROGRESS", array("#done#" => $tables_count-count($arTables), "#todo#" => $tables_count))));
							?><script>setTimeout("Optimize('o|<?echo CUtil::JSEscape($arTable["Name"])?>')", 100);</script><?
						}
						elseif($op == "optimize") //and step analyze
						{
							CAdminMessage::ShowMessage(array("MESSAGE"=>htmlspecialcharsbx($arTable["Name"])." - ".GetMessage("RDB_OPTIMIZE_ANALYZE"), "TYPE"=>"OK", "HTML"=>true, "DETAILS"=>GetMessage("RDB_OPTIMIZE_PROGRESS", array("#done#" => $tables_count-count($arTables), "#todo#" => $tables_count))));
							?><script>setTimeout("Optimize('a|<?echo CUtil::JSEscape($arTable["Name"])?>')", 100);</script><?
						}
						else
						{
							$arTable = array_shift($arTables);
							CAdminMessage::ShowMessage(array("MESSAGE"=>htmlspecialcharsbx($arTable["Name"]), "TYPE"=>"OK", "HTML"=>true, "DETAILS"=>GetMessage("RDB_OPTIMIZE_PROGRESS", array("#done#" => $tables_count-count($arTables), "#todo#" => $tables_count))));
							?><script>setTimeout("Optimize('<?echo CUtil::JSEscape($arTable["Name"])?>')", 100);</script><?
						}
					}
					else
					{
						COption::SetOptionInt("main", "LAST_DB_OPTIMIZATION_TIME", time());
						CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("RDB_OPTIMIZE_DONE"), "TYPE"=>"OK", "HTML"=>true, "DETAILS"=>GetMessage("RDB_OPTIMIZE_ALL_DONE")));
						?>
						<script>
						document.getElementById('opt_start').disabled = false;
						document.getElementById('opt_pause').disabled = true;
						document.getElementById('opt_continue').disabled = true;
						</script>
						<?
					}
				}
				else
				{
					CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("RDB_OPTIMIZE_ERROR"), "TYPE"=>"ERROR", "HTML"=>true, "DETAILS"=>GetMessage("RDB_OPTIMIZE_TABLE_NOT_FOUND")));
				}
			}
		}
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
	}
	else
	{
		$APPLICATION->SetTitle(GetMessage("RDB_REPAIR_DATABASE"));
		require_once(dirname(__FILE__)."/../include/prolog_admin_after.php");
		if(strtolower($DB->type) == "mysql")
		{
			if($_REQUEST["check_tables"]=="Y" && check_bitrix_sessid())
			{
				repair_db();
			}
			elseif($_REQUEST["optimize_tables"]=="Y")
			{
				?>
				<?echo BeginNote(), GetMessage("RDB_OPTIMIZE_TIP"), EndNote();?>
				<div id="optimize_result">
				<?CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("RDB_OPTIMIZE_WARNING_TITLE"), "TYPE"=>"ERROR", "HTML"=>true, "DETAILS"=>GetMessage("RDB_OPTIMIZE_WARNING_DETAILS")));?>
				</div>
				<input type="button" name="opt_start" id="opt_start" value="<?echo GetMessage("RDB_OPTIMIZE_BTN_START")?>" OnClick="Optimize('start|');">
				<input type="button" name="opt_pause" id="opt_pause" value="<?echo GetMessage("RDB_OPTIMIZE_BTN_PAUSE")?>" OnClick="Pause(true);" disabled>
				<input type="button" name="opt_continue" id="opt_continue" value="<?echo GetMessage("RDB_OPTIMIZE_BTN_CONTINUE")?>" OnClick="Pause(false);" disabled>
				<script>
				var pause = false;
				function Pause(flag)
				{
					pause = flag;
					if(pause)
					{
						document.getElementById('opt_start').disabled = true;
						document.getElementById('opt_pause').disabled = true;
						document.getElementById('opt_continue').disabled = false;
					}
					else
					{
						document.getElementById('opt_start').disabled = true;
						document.getElementById('opt_pause').disabled = false;
						document.getElementById('opt_continue').disabled = true;
					}
				}
				function Optimize(table_name)
				{
					if(pause)
					{
						setTimeout("Optimize('"+table_name+"')", 500);
					}
					else
					{
						CHttpRequest.Action = function(result)
						{
							CloseWaitWindow();
							document.getElementById('optimize_result').innerHTML = result;
						};
						ShowWaitWindow();
						if(table_name == 'start|')
						{
							document.getElementById('opt_start').disabled = true;
							document.getElementById('opt_pause').disabled = false;
							document.getElementById('opt_continue').disabled = true;
						}
						var url = 'repair_db.php?lang=<?echo LANGUAGE_ID?>&<?echo bitrix_sessid_get()?>&table_name='+table_name;
						CHttpRequest.Send(url);
					}
				}
				</script>
				<?
			}
			else
			{
				?>
				<p><?=GetMessage("RDB_TIP_1")?></p>
				<?CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("RDB_TIP_2"), "TYPE"=>"ERROR", "HTML"=>true, "DETAILS"=>GetMessage("RDB_TIP_3")));
				show_tip();
			}
		}
		else
			CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("RDB_DATABASE_ERROR"), "TYPE"=>"ERROR"));
		require_once(dirname(__FILE__)."/../include/epilog_admin.php");
	}
}
