<?
if (isset($_REQUEST['work_start']))
{
	define("NO_AGENT_STATISTIC", true);
	define("NO_KEEP_STATISTIC", true);
}
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

IncludeModuleLangFile(__FILE__);

$POST_RIGHT = $APPLICATION->GetGroupRight("main");
if ($POST_RIGHT == "D")
	$APPLICATION->AuthForm("Доступ запрещен");

if($_REQUEST['work_start'] && check_bitrix_sessid())
{
	$lastID = intval($_REQUEST["lastid"]);
	$table = "otherdb.b_user";

	$rs = $DB->Query("select * from $table where ID>$lastID order by ID asc limit 500;");
	while ($ar = $rs->Fetch())
	{
		/*
		 * do something
		 */
		$lastID = intval($ar["ID"]);
	}

	$rsLeftBorder = $DB->Query("select ID from $table where ID <= $lastID order by ID asc", false, "FILE: ".__FILE__."<br /> LINE: ".__LINE__);
	$leftBorderCnt = $rsLeftBorder->SelectedRowsCount();

	$rsAll = $DB->Query("select ID from $table;", false, "FILE: ".__FILE__."<br /> LINE: ".__LINE__);
	$allCnt = $rsAll->SelectedRowsCount();

	$p = round(100*$leftBorderCnt/$allCnt, 2);

	echo 'CurrentStatus = Array('.$p.',"'.($p < 100 ? '&lastid='.$lastID : '').'","Обрабатываю запись с ID #'.$lastID.'");';

	die();
}

$clean_test_table = '<table id="result_table" cellpadding="0" cellspacing="0" border="0" width="100%" class="internal">'.
						'<tr class="heading">'.
							'<td>Текущее действие</td>'.
							'<td width="1%">&nbsp;</td>'.
						'</tr>'.
					'</table>';

$aTabs = array(array("DIV" => "edit1", "TAB" => "Перенос данных"));
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$APPLICATION->SetTitle("Перенос данных");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

?>
<script type="text/javascript">

	var bWorkFinished = false;
	var bSubmit;

	function set_start(val)
	{
		document.getElementById('work_start').disabled = val ? 'disabled' : '';
		document.getElementById('work_stop').disabled = val ? '' : 'disabled';
		document.getElementById('progress').style.display = val ? 'block' : 'none';

		if (val)
		{
			ShowWaitWindow();
			document.getElementById('result').innerHTML = '<?=$clean_test_table?>';
			document.getElementById('status').innerHTML = 'Работаю...';

			document.getElementById('percent').innerHTML = '0%';
			document.getElementById('indicator').style.width = '0%';

			CHttpRequest.Action = work_onload;
			CHttpRequest.Send('<?= $_SERVER["PHP_SELF"]?>?work_start=Y&lang=<?=LANGUAGE_ID?>&<?=bitrix_sessid_get()?>');
		}
		else
			CloseWaitWindow();
	}

	function work_onload(result)
	{
		try
		{
			eval(result);

			iPercent = CurrentStatus[0];
			strNextRequest = CurrentStatus[1];
			strCurrentAction = CurrentStatus[2];

			document.getElementById('percent').innerHTML = iPercent + '%';
			document.getElementById('indicator').style.width = iPercent + '%';

			document.getElementById('status').innerHTML = 'Работаю...';

			if (strCurrentAction != 'null')
			{
				oTable = document.getElementById('result_table');
				oRow = oTable.insertRow(-1);
				oCell = oRow.insertCell(-1);
				oCell.innerHTML = strCurrentAction;
				oCell = oRow.insertCell(-1);
				oCell.innerHTML = '';
			}

			if (strNextRequest && document.getElementById('work_start').disabled)
				CHttpRequest.Send('<?= $_SERVER["PHP_SELF"]?>?work_start=Y&lang=<?=LANGUAGE_ID?>&<?=bitrix_sessid_get()?>' + strNextRequest);
			else
			{
				set_start(0);
				bWorkFinished = true;
			}

		}
		catch(e)
		{
			CloseWaitWindow();
			document.getElementById('work_start').disabled = '';
			alert('Сбой в получении данных');
		}
	}

</script>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>" enctype="multipart/form-data" name="post_form" id="post_form">
<?
echo bitrix_sessid_post();

$tabControl->Begin();
$tabControl->BeginNextTab();
?>
	<tr>
		<td colspan="2">

			<input type=button value="Старт" id="work_start" onclick="set_start(1)" />
			<input type=button value="Стоп" disabled id="work_stop" onclick="bSubmit=false;set_start(0)" />
			<div id="progress" style="display:none;" width="100%">
			<br />
				<div id="status"></div>
				<table border="0" cellspacing="0" cellpadding="2" width="100%">
					<tr>
						<td height="10">
							<div style="border:1px solid #B9CBDF">
								<div id="indicator" style="height:10px; width:0%; background-color:#B9CBDF"></div>
							</div>
						</td>
						<td width=30>&nbsp;<span id="percent">0%</span></td>
					</tr>
				</table>
			</div>
			<div id="result" style="padding-top:10px"></div>

		</td>
	</tr>
<?
$tabControl->End();
?>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>