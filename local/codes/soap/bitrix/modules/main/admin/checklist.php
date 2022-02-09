<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");

if(!defined('NOT_CHECK_PERMISSIONS') || NOT_CHECK_PERMISSIONS !== true)
{
	if (!$USER->CanDoOperation('view_other_settings'))
		$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/checklist.php");
IncludeModuleLangFile(__FILE__);
$APPLICATION->AddHeadString(
	'<link type="text/css" rel="stylesheet" href="/bitrix/themes/.default/check-list-style.css">'
);
$APPLICATION->SetTitle(GetMessage("CL_TITLE_CHECKLIST"));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

CUtil::InitJSCore(Array('ajax','window','popup','fx'));
$arStates = array();

$showHiddenReports =  CUserOptions::GetOption("checklist","show_hidden","N",false);
if ((($res = CCheckListResult::GetList(Array(),Array("REPORT"=>"N"))->Fetch()) || ($_POST["bx_start_test"] == "Y") || $_REQUEST["ACTION"]) && check_bitrix_sessid())
{
	?><div class="checklist-body-1024"><?

	if (isset($_REQUEST['report_id']))
	{
		$checklist = new CCheckList($_REQUEST['report_id']);
	}
	else
		$checklist = new CCheckList();

	$isFisrtTime = CUserOptions::GetOption("checklist","autotest_start","N",false);
	CUserOptions::SetOption("checklist","autotest_start","Y");

	$arStructure = $checklist->GetStructure();
	$arPoints = $checklist->GetPoints();
	if ($_POST["ACTION"] == "update")
	{
		$arTestID = $_POST["TEST_ID"];
		if ($_POST["autotest"]=="Y")//start autotest
		{
			$arStep = intval($_POST["STEP"]);
			$arResult = $checklist->AutoCheck($arTestID,Array("STEP"=>$arStep));
		}
		else
		{
			if ($_POST["COMMENTS"] == "Y")//update only comments
			{
				$arPointFields["COMMENTS"] = $arPoints[$arTestID]["STATE"]["COMMENTS"];
				if ($_POST["perfomer_comment"] && strlen(trim($_POST["perfomer_comment"]))>1)
				$arPointFields["COMMENTS"]["PERFOMER"] = $_POST["perfomer_comment"];
				else
					unset($arPointFields["COMMENTS"]["PERFOMER"]);
				if ($_POST["custom_comment"] && strlen(trim($_POST["custom_comment"]))>1)
					$arPointFields["COMMENTS"]["CUSTOMER"] = $_POST["custom_comment"];
				else
					unset($arPointFields["COMMENTS"]["CUSTOMER"]);

				if (ToUpper(SITE_CHARSET) != "UTF-8" && $arPointFields["COMMENTS"])
				{
					if ($arPointFields["COMMENTS"]["PERFOMER"])
						$arPointFields["COMMENTS"]["PERFOMER"] = $APPLICATION->ConvertCharsetArray($arPointFields["COMMENTS"]["PERFOMER"],"UTF-8",SITE_CHARSET);
					if($arPointFields["COMMENTS"]["CUSTOMER"])
						$arPointFields["COMMENTS"]["CUSTOMER"] = $APPLICATION->ConvertCharsetArray($arPointFields["COMMENTS"]["CUSTOMER"],"UTF-8",SITE_CHARSET);
				}

				$arPointFields["STATUS"] = $arPoints[$arTestID]["STATE"]["STATUS"];
			}
			if ($_POST["STATUS"])//update only status
				$arPointFields["STATUS"] = $_POST["STATUS"];

				$checklist->PointUpdate($arTestID,$arPointFields);
			if ($checklist->Save())
			{
				$arResult = Array(
					"STATUS"=>$arPointFields["STATUS"],
					"IS_REQUIRE"=>$arPoints[$arTestID]["REQUIRE"],
					"COMMENTS_COUNT" =>count($arPointFields["COMMENTS"]),
				);
			}
			else
				$arResult = Array("RESULT"=>"ERROR");
		}

		$arTotal = $checklist->GetSectionStat();
		$arCode = $checklist->checklist["CATEGORIES"][$arPoints[$arTestID]["PARENT"]]["PARENT"];
		if ($arCode)
		{
			$arParentCode = $arCode;
			$arSubParentCode = $arPoints[$arTestID]["PARENT"];
		}
		else
			$arParentCode = $arSubParentCode = $arPoints[$arTestID]["PARENT"];

		$arSubParentStat = $checklist->GetSectionStat($arSubParentCode);
		$arParentStat = $checklist->GetSectionStat($arParentCode);

		//////////////////////////////////////////
		//////////////JSON ANSWER/////////////////
		//////////////////////////////////////////
		$arParentStat["ID"] = $arParentCode;
		$arSubParentStat["ID"] = $arSubParentCode;
		$arResultAdditional = Array(
				"PARENT"=>$arParentStat,
				"SUB_PARENT"=>$arSubParentStat,
				"TEST_ID"=>$arTestID,
				"CAN_CLOSE_PROJECT"=>(!$_POST["CAN_SHOW_CP_MESSAGE"])?"N":$arTotal["CHECKED"],
				"TOTAL"=>$arTotal["TOTAL"],
				"FAILED"=>$arTotal["FAILED"],
				"SUCCESS"=>$arTotal["CHECK"],
				"SUCCESS_R"=>$arTotal["CHECK_R"],
				"REQUIRE"=>$arTotal["REQUIRE"],
				"REQUIRE_CHECK"=>$arTotal["REQUIRE_CHECK"],
				"WAITING"=>$arTotal["WAITING"],
				"MAIN_STAT"=>Array(
					"TOTAL"=>$arTotal["FAILED"]+$arTotal["CHECK"],
					"SUCCESS"=>$arTotal["CHECK"],
					"SUCCESS_R"=>$arTotal["CHECK_R"],
					"FAILED"=>$arTotal["FAILED"],
					"REQUIRE"=>$arTotal["REQUIRE"],
					"REQUIRE_CHECK"=>$arTotal["REQUIRE_CHECK"]
				)
			);
		$arResult = array_merge($arResultAdditional,$arResult);
		$APPLICATION->RestartBuffer();
		header("Content-Type: application/x-javascript; charset=".LANG_CHARSET);
		echo CUtil::PhpToJsObject($arResult);
		die();
	}
	elseif ($_REQUEST["ACTION"] == "SHOWHIDEELEMENTS")
	{
		if (isset($_REQUEST["report_action"]) && (isset($_REQUEST["report_id"]) && intval($_REQUEST["report_id"])))
		{
			$report_id = intval($_REQUEST["report_id"]);
			CCheckListResult::Update($report_id, array('HIDDEN' => $_REQUEST['report_action'] == 'hide' ? 'Y' : 'N'));
		}

		LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANG,true);
	}
	elseif ($_REQUEST["ACTION"] == "CHANGELISTPROP")
	{
		if ($_REQUEST["showHiddenReports"] == "Y")
			$showHiddenReports = "Y";
		else
			$showHiddenReports = "N";

		CUserOptions::SetOption("checklist","show_hidden", $showHiddenReports);
		LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANG,true);
	}
	elseif ($_REQUEST["ACTION"] == "RESETBITRIXSTATUS")
	{
		$dbReport = CCheckListResult::GetList(Array(),Array("REPORT"=>"Y", "SENDED_TO_BITRIX" => 'Y'));
		if ($arReport = $dbReport->Fetch())
		{
			CCheckListResult::Update($arReport['ID'], array('SENDED_TO_BITRIX' => 'N'));
		}
		LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANG,true);
	}
	elseif ($_REQUEST["ACTION"] == "ADDREPORT")//add report
	{
		$arFields = array('REPORT' => 'Y');
		if ($_POST["TESTER"])
			$arFields["TESTER"] = $_POST["TESTER"];
		if ($_POST["COMPANY_NAME"])
			$arFields["COMPANY_NAME"] = $_POST["COMPANY_NAME"];
		if ($_POST["EMAIL"])
			$arFields["EMAIL"] = $_POST["EMAIL"];
		$report_id = $checklist->AddReport($arFields);
//		CCheckListResult::Update($report_id, $arFields);
		LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANG,true);
	}
	elseif ($_REQUEST["ACTION"] == "ADDSENDREPORT")//add report and send to bitrix
	{
		if (isset($_REQUEST['report_id']))
		{
			$report_id = intval($_REQUEST['report_id']);
			$dbReport = CCheckListResult::GetList(Array(),Array("REPORT"=>"Y", "ID" => $report_id));
			if ($arReport = $dbReport->Fetch())
			{
				$arFields = array();
				if ($_POST["COMPANY_NAME"])
					$arFields["COMPANY_NAME"] = $_POST["COMPANY_NAME"];
				if ($_POST["CLIENT"])
					$arFields["CLIENT"] = $_POST["CLIENT"];
				if ($_POST["CLIENT_POST"])
					$arFields["CLIENT_POST"] = $_POST["CLIENT_POST"];
				if ($_POST["PHONE"])
					$arFields["PHONE"] = $_POST["PHONE"];
				if ($_POST["PHONE_ADD"])
					$arFields["PHONE_ADD"] = $_POST["PHONE_ADD"];
				if ($_POST["EMAIL"])
					$arFields["EMAIL"] = $_POST["EMAIL"];
				if ($_POST["COMMENT"])
					$arFields["COMMENT"] = $_POST["COMMENT"];

				CCheckListResult::Update($report_id, array('SENDED_TO_BITRIX' => 'Y'));

				$res = $checklist->AddReport($arFields);

				$arFields['STATE'] = base64_encode(serialize($checklist->current_result));
				$arFields['CHECKLIST'] = base64_encode(serialize($checklist->checklist));
				$arFields['SITE'] = $_SERVER['HTTP_HOST'];
				SendReportToBitrix($arFields);
				require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
				die();
			}
		}
	}

/////////////////////////////////////////////////////////
//////////////////////PARAMS_PREPARE/////////////////////
/////////////////////////////////////////////////////////
	$arSections = $checklist->GetSections();
	$arStat = $checklist->GetSectionStat();
	$arCanClose = $arStat["CHECKED"];
	$arAutoCheck = array();

	foreach ($arPoints as $key=>$arFields)
	{
		$arStates["POINTS"][] = Array(
			"TEST_ID" => $key,
			"NAME"=>$arFields["NAME"],
			"STATUS" => $arFields["STATE"]["STATUS"],
			"IS_REQUIRE" => ($arFields["REQUIRE"])?$arFields["REQUIRE"]:"N",
			"AUTO" => $arFields["AUTO"],
			"COMMENTS_COUNT" => count($arFields["STATE"]["COMMENTS"]),
		);

		if ($arFields["AUTO"] == "Y")
		{
			$arAutoCheck["ID"][]=$key;
			$arAutoCheck["NAME"][]=$arFields["NAME"];
		}
	}

	foreach ($arSections as $key=>$arFields)
	{
		$arStats = $checklist->GetSectionStat($key);
		$arStates["SECTIONS"][] = Array(
			"ID" => $key,
			"CHECKED" => $arStats["CHECKED"],
			"TOTAL" => $arStats["TOTAL"],
			"PARENT" => $arFields["PARENT"],
			"CHECK" => $arStats["CHECK"],
			"FAILED"=>  $arStats["FAILED"]
		);
	}
	$arStates = CUtil::PhpToJsObject($arStates);
/////////////////////////////////////////////////////////
//////////////////////END_PREPARE////////////////////////
/////////////////////////////////////////////////////////
?>
	<div class="checklist-wrapper">
		<div class="checklist-top-info">




			<div class="checklist-top-info-right-wrap">
				<span class="checklist-top-info-left">
					<span class="checklist-top-info-left-item"><?=GetMessage("CL_TEST_TOTAL");?>:</span><br/>
					<span class="checklist-top-info-left-item"><?=GetMessage("CL_TEST_REQUIRE");?>:</span><br/>
					<span class="checklist-top-info-left-item checklist-test-successfully"><?=GetMessage("CL_TEST_CHECKED");?>:</span><br/>
					<span class="checklist-top-info-left-item"><?=GetMessage("CL_TEST_CHECKED_R");?>:</span><br/>
					<span class="checklist-top-info-left-item checklist-test-unsuccessful"><?=GetMessage("CL_TEST_FAILED");?>:</span><br/>
				</span><span class="checklist-top-info-right-nambers">
					<span id="total" class="checklist-top-info-left-item-qt"><?=$arStat["TOTAL"]?></span><br/>
					<span class="checklist-top-info-left-item-qt"><?=$arStat["REQUIRE"]?></span><br/>
					<span id="success" class="checklist-test-successfully"><?=$arStat["CHECK"]?></span><br/>
					<span id="success_r" class="checklist-top-info-left-item-qt"><?=($arStat["REQUIRE"] - $arStat["CHECK_R"])?></span><br/>
					<span id="failed" class="checklist-test-unsuccessful"><?=$arStat["FAILED"]?></span><br/>
				</span>
			</div>


			<div class="checklist-top-info-left-wrap">
				<div class="checklist-top-info-right">
					<span><?=GetMessage("CL_CHECK_PROGRESS");?>:</span>
					<div class="checklist-test-completion">
						<div class="checklist-test-completion-right">
							<div class="checklist-test-completion-cont">
								<span id="progress" class="checklist-test-completion-quan"><?=GetMessage("CL_TEST_PROGRESS",Array("#check#"=>$arStat["CHECK"],"#total#"=>$arStat["TOTAL"]));?></span>
								<div id="progress_bar" class="checklist-test-completion-pct" style="width:<?=round(($arStat["CHECK"])/($arStat["TOTAL"]*0.01),0)?>%;"></div>
							</div>
						</div>
					</div>
					<span id="current_test_name" class="checklist-test-completion-text"></span><span id="percent"></span>
				</div>
			</div>



			<div class="checklist-clear"></div>
			<a id="bx_start_button" class="adm-btn adm-btn-green adm-btn" onClick="StartAutoCheck()" style="margin-top: -121px">
				<span class="checklist-button-cont"><?=GetMessage("CL_BEGIN_AUTOTEST");?></span>
			</a>
			<div class="checklist-clear"></div>
			<a id="bx_start_save_project" class="adm-btn adm-btn-green adm-btn" onClick="ShowCloseProject(true)" style="display: none; margin-top: -61px">
				<span class="checklist-button-cont"><?=GetMessage("CL_SAVE_REPORT");?></span>
			</a>
		</div>
	<ul class="checklist-testlist">
	<?foreach($arStructure["STRUCTURE"] as $rkey=>$rFields):?>
		<li class="checklist-testlist-level1">
			<div class="checklist-testlist-text" id="<?=$rkey;?>_name"><?=$rFields["NAME"];?><span id="<?=$rkey;?>_stat" class="checklist-testlist-amount-test"></span>
			<span class="checklist-testlist-marker-list"></span>
			</div>
			<ul class="checklist-testlist-level2-wrap">
				<?
				$num = 1;
				foreach($rFields["POINTS"] as $pkey=>$pFields):?>
				<li id="<?=$pkey;?>" class="checklist-testlist-level3">
					<span class="checklist-testlist-level3-cont">
						<span class="checklist-testlist-level3-cont-nom"><?=$num++.". ";?></span>
						<span class="checklist-testlist-level3-cont-right">
							<span class="checklist-testlist-level3-cont-border" onclick='ShowPopupWindow("<?=$pkey;?>","<?=addslashes($pFields["NAME"]);?>");'>							<?=$pFields["NAME"];?>
							</span>
							<span id="comments_<?=$pkey;?>" onclick='ShowPopupWindow("<?=$pkey;?>","<?=addslashes($pFields["NAME"]);?>");' class="checklist-testlist-comments" ><?=count($pFields["STATE"]["COMMENTS"]);?></span>

						</span>
					</span>
					<span id="mark_<?=$pkey;?>"></span>
				</li>
				<?endforeach;?>
				<?foreach($rFields["CATEGORIES"] as $skey=>$sFields): $num = 1;?>
					<li class="checklist-testlist-level2">
						<div class="checklist-testlist-text" id="<?=$skey;?>_name" ><?=$sFields["NAME"];?><span id="<?=$skey;?>_stat" class="checklist-testlist-amount-test"></span>
							<span class="checklist-testlist-marker-list"></span>
						</div>
						<ul class="checklist-testlist-level3-wrap">
							<?foreach($sFields["POINTS"] as $pkey=>$pFields):?>
								<li id="<?=$pkey;?>" class="checklist-testlist-level3">
									<span class="checklist-testlist-level3-cont">
										<span class="checklist-testlist-level3-cont-nom"><?=$num++.". ";?></span>
										<span class="checklist-testlist-level3-cont-right">
											<span class="checklist-testlist-level3-cont-border" onclick='ShowPopupWindow("<?=$pkey;?>","<?=addslashes($pFields["NAME"]);?>");'><?=$pFields["NAME"];?></span>
											<span id="comments_<?=$pkey;?>" class="checklist-testlist-comments" onclick='ShowPopupWindow("<?=$pkey;?>","<?=addslashes($pFields["NAME"]);?>");'><?=count($pFields["STATE"]["COMMENTS"]);?></span>
										</span>
									</span>
									<span id="mark_<?=$pkey;?>"></span>
								</li>
							<?endforeach;?>
						</ul>
					</li>
				<?endforeach;?>
			</ul>
		</li>
	<?endforeach;?>
	</ul>

	<script type="text/javascript">
		function ShowHint (el)
		{
			el.BXHINT = new BX.CHint({
				parent: el,
				show_timeout: 200,
				hide_timeout: 200,
				dx: 2,
				showOnce: false,
				preventHide: true,
				min_width: 250,
				hint: '<?=CUtil::JSEscape(GetMessage('CL_SAVE_SEND_REPORT_HINT'))?>',
				//title: 'title'
			});
			el.BXHINT.Show();
		}

		var arStates = eval(<?=$arStates;?>);
		var DetailWindow = false;
		var arMainStat ={
			"REQUIRE":<?=$arStat["REQUIRE"];?>,
			"REQUIRE_CHECK":<?=$arStat["REQUIRE_CHECK"];?>,
			"FAILED":<?=$arStat["FAILED"];?>,
			"SUCCESS":<?=$arStat["CHECK"];?>,
			"SUCCESS_R":<?=$arStat["CHECK_R"];?>,
			"TOTAL":<?=$arStat["TOTAL"];?>
		};
		var arRequireCount=<?=$arStat["REQUIRE"];?>;
		var arRequireCheckCount=<?=$arStat["REQUIRE_CHECK"];?>;
		var arFailedCount = <?=$arStat["FAILED"];?>;
		var CanClose = "<?=$arCanClose;?>";
		var arAutoCheck = new Array('<?=implode("','",$arAutoCheck["ID"]);?>');
		var arAutoCheckName = new Array('<?=implode("','",$arAutoCheck["NAME"]);?>');
		var arTestResult = {"total":0,"success":0,"failed":0};
		var start = "<?=$isFisrtTime;?>";
		var showHiddenReports = "<?=$showHiddenReports?>";
		var bx_autotest_step = 0;
		var bx_test_num = 0;
		var ErrorSections = new Array();
		var bx_autotest=false;
		var bx_stoptest=false;
		var Dialog = false;
		var checklist_div= document.getElementsByTagName('div');
		var body = document.getElementsByTagName('body');
		var current = 0;
		var next = 0;
		var prev = 0;

		BX.ready(function(){InitState();});
		if(start=="N")
		{
			///BX.ready(function(){InitState();});
			BX.ready(function(){StartAutoCheck();});
		}

		var list_binds={
			checklist_span:BX.findChildren(document,{className:'checklist-testlist-text'}, true),
			hover_link:BX.findChildren(document,{className:'checklist-testlist-level3-cont'}, true),
			show_list:function(){
				BX.hasClass(this.parentNode,'testlist-open')?BX.removeClass(this.parentNode, 'testlist-open'):BX.addClass(this.parentNode, 'testlist-open');
			},
			hover_border:function(event){
				var event = event || window.event;
				if(event.type=='mouseover') BX.findChild(this,{className:'checklist-testlist-level3-cont-border'}, true).style.borderBottom='1px dashed';
				if(event.type=='mouseout') BX.findChild(this,{className:'checklist-testlist-level3-cont-border'}, true).style.borderBottom='none';
			},
			binds:function(){
				for(var i=0; i<this.checklist_span.length; i++){
					BX.bind(this.checklist_span[i], "click", this.show_list)
				}
				for(var b=0; b<this.hover_link.length; b++){
					BX.bind(this.hover_link[b], 'mouseover', this.hover_border);
					BX.bind(this.hover_link[b], 'mouseout', this.hover_border)
				}
			}
		};
		list_binds.binds();


		function InitState()
		{
			for (var i=0;i<arStates["SECTIONS"].length;i++)
				ChangeSection(arStates["SECTIONS"][i]);
			for (var i=0;i<arStates["POINTS"].length;i++)
				ChangeStatus(arStates["POINTS"][i]);
			if (CanClose == "Y")
				ShowCloseProject();
			if (ErrorSections.length>0)
				for(var i=0;i<ErrorSections.length;i++)
				{
					if (BX(ErrorSections[i]+"_name").parentNode)
						BX.addClass(BX(ErrorSections[i]+"_name").parentNode, 'testlist-open');
				}
		}

		function textarea_edit(_this, effect){
			if(effect){
				_this.value=="<?=GetMessage("CL_ADD_COMMENT");?>"?_this.value="":false;
				BX.addClass(_this, "checklist-textarea-active");
			}
			if(!effect){
				if(_this.value==''){
					_this.value='<?=GetMessage("CL_ADD_COMMENT");?>';
					BX.removeClass(_this, "checklist-textarea-active");
				}
			}
		}

		function loadButton(id){
			BX.toggleClass(BX(id), 'bx_start_button');
			var buttonText =  BX.findChild(BX(id), {className:'checklist-button-cont'}, true, false);
			buttonText.innerHTML=='<?=GetMessage("CL_BEGIN_AUTOTEST");?>' ? buttonText.innerHTML='<?=GetMessage("CL_END_TEST");?>' : buttonText.innerHTML='<?=GetMessage("CL_BEGIN_AUTOTEST");?>';
			return false
		}

		function ShowPopupWindow(testID,head_name)
		{
			current = 0;
			next = 0;
			prev = 0;
			Dialog = new BX.CAdminDialog(
				{
					title: head_name+" - "+testID,
					head: "",
					content_url: "/bitrix/admin/checklist_detail.php?TEST_ID="+testID+"&lang=<?=LANG;?>&bxpublic=Y",
					icon: "head-block",
					resizable: false,
					draggable: true,
					height: "530",
					width: "780"
				}
			);
			Dialog.SetButtons(['<input id="prev" type="button" onclick="Move(\'prev\');"name="prev" value="<?=GetMessage("CL_PREV_TEST");?>"><input id="next" type="button" name="next" onclick="Move(\'next\');" value="<?=GetMessage("CL_NEXT_TEST");?>">', {title: '<?=GetMessage('CL_DONE')?>', id: 'close', name: 'close', action: function () {this.parentWindow.Close();}}]);
			for (var i=0;i<arStates["POINTS"].length;i++)
			{
				if (arStates["POINTS"][i].TEST_ID == testID)
				{


					if (arStates["POINTS"][i].IS_REQUIRE == "Y")
						Dialog.SetTitle(head_name+" - "+testID+" ("+"<?=GetMessage("CL_TEST_IS_REQUIRE");?>"+")");

					current = i;
					ReCalc(current);
					break;
				}
			}
			Dialog.Show();
		}


		function ReCalc(current)
		{
			BX("next").disabled = null;
			BX("prev").disabled = null;
			prev = current-1;
			next = current+1;
			if (current == 0)
			{
				BX("prev").disabled = "disabled";
				next = current+1;
			}
			if (current == (arStates["POINTS"].length-1))
			{
				BX("next").disabled = "disabled";
				prev = current-1;
			}
		}

		function hide_project_form(_this)
		{
			if (CanClose != "Y")
			{
				_this.checked = false;
				var bx_info = document.createElement('div');
				BX.addClass(bx_info,"checklist-alert-comment");
				var result = "";
				result+= "<?=addslashes(GetMessage("CL_CANT_CLOSE_PROJECT"));?>";
				result+= "<br><br><?=addslashes(GetMessage("CL_CANT_CLOSE_PROJECT_PASSED_REQUIRE"));?>"+"<b>"+arMainStat.REQUIRE_CHECK+"<?=GetMessage("CL_FROM")?>"+arMainStat.REQUIRE+"</b>"
				result+= "<br><?=addslashes(GetMessage("CL_CANT_CLOSE_PROJECT_FAILED"));?>"+"<b>"+arMainStat.FAILED+"</b>";
				bx_info.innerHTML = result;
				var project_info = BX.PopupWindowManager.create(
					"project_info",
					null,
					{
						autoHide : true,
						lightShadow : true,
						closeIcon:true,
						zIndex:100100
					}
				);
				project_info.setContent(bx_info);
				project_info.setButtons([
				new BX.PopupWindowButton({text : "<?=GetMessage("CL_CLOSE");?>", className : "", events : { click : function(){
				if (ErrorSections.length>0)
				for(var i=0;i<ErrorSections.length;i++)
				{
					if (BX(ErrorSections[i]+"_name").parentNode)
						BX.addClass(BX(ErrorSections[i]+"_name").parentNode, 'testlist-open');
				}
				project_info.close();
				} } })

			]);
				project_info.show();
				return;
			}
			if (_this.checked == true)
				BX("bx_project_form").style.display ="block";
			else
				BX("bx_project_form").style.display ="none";
		}

		function ChangeStatus(element)
		{
			BX.removeClass(BX(element.TEST_ID), BX(element.TEST_ID).className);
			BX("mark_"+element.TEST_ID).className = "";

			if (element.STATUS == "F")
			{
				arTestResult.failed++;
				BX.addClass(BX(element.TEST_ID),"checklist-testlist-red");
				BX.addClass(BX("mark_"+element.TEST_ID),"checklist-testlist-item-closed");
			}else if (element.STATUS == "A")
			{
				arTestResult.success++;
				if (element.REQUIRE == "Y")
					arTestResult.success_r++;
				BX.addClass(BX(element.TEST_ID),"checklist-testlist-green");
				BX.addClass(BX("mark_"+element.TEST_ID),"checklist-testlist-item-done");
			}else if (element.STATUS == "W")
			{
				if (element.IS_REQUIRE == "Y")
					BX.addClass(BX(element.TEST_ID),"checklist-testlist-black");
				else
					BX.addClass(BX(element.TEST_ID),"checklist-testlist-grey");
			}else if (element.STATUS == "S")
			{
				if (element.IS_REQUIRE == "Y")
					BX.addClass(BX(element.TEST_ID),"checklist-testlist-black checklist-testlist-through");
				else
					BX.addClass(BX(element.TEST_ID),"checklist-testlist-grey checklist-testlist-through");
			}
			BX.addClass(BX(element.TEST_ID),"checklist-testlist-level3");

			if (element.COMMENTS_COUNT >0)
			{
				BX("comments_"+element.TEST_ID).innerHTML = element.COMMENTS_COUNT;
				BX.removeClass(BX("comments_"+element.TEST_ID),"checklist-hide");
			}
			else
				BX.addClass(BX("comments_"+element.TEST_ID),"checklist-hide");
		}

		function ChangeSection(data)
		{
			BX(data.ID+"_stat").innerHTML = "(";
			if (data.FAILED>0)
				BX(data.ID+"_stat").innerHTML+= "<span class=\"checklist-testlist-red\">"+data.FAILED+"</span>/";
			BX(data.ID+"_stat").innerHTML+= "<span class=\"checklist-testlist-passed-test\">"+data.CHECK+"</span>/"+data.TOTAL;
			BX(data.ID+"_stat").innerHTML+= ")";
			BX.removeClass(BX(data.ID+"_name"),"checklist-testlist-green");
			if (data.CHECKED == "Y")
				BX.addClass(BX(data.ID+"_name"),"checklist-testlist-green");
			else if(data.FAILED > 0)
				ErrorSections[ErrorSections.length] = data.ID;
		}

		function RefreshCheckList(json_data)
		{
			arTestResult.total++;
			ChangeStatus(json_data);
			BX("progress").innerHTML = parseInt(json_data.SUCCESS)+"<?=GetMessage("CL_FROM")?> "+json_data.TOTAL;
			BX("progress_bar").style.width = Math.round((parseInt(json_data.SUCCESS))/(json_data.TOTAL*0.01))+"%";
			BX("success").innerHTML = json_data.SUCCESS;
			BX("success_r").innerHTML = parseInt(json_data.REQUIRE) - parseInt(json_data.SUCCESS_R);
			if (parseInt(json_data.REQUIRE) - parseInt(json_data.SUCCESS_R) == 0)
				BX('bx_start_save_project').style.display = 'inline-block';
			else
				BX('bx_start_save_project').style.display = 'none';
			BX("failed").innerHTML = json_data.FAILED;
			//BX("bx_count_check").innerHTML = json_data.SUCCESS;
			//BX("bx_count_from").innerHTML = parseInt(json_data.SUCCESS)+parseInt(json_data.FAILED);

			ChangeSection(json_data.PARENT);
			if (json_data.PARENT.ID!=json_data.SUB_PARENT.ID)
				ChangeSection(json_data.SUB_PARENT);

				CanClose = json_data.CAN_CLOSE_PROJECT;
			arMainStat = json_data.MAIN_STAT;
			BX("percent").innerHTML = "";
		}

		function TestResultSimple(data)
		{
			try
			{
				var json_data=eval("(" +data+")");
				if (json_data && json_data.STATUS)
				{
					RefreshCheckList(json_data);
					Dialog.Notify("<?=GetMessage("CL_SAVE_SUCCESS");?>");
					//setTimeout("Dialog.hideNotify()",2000);
				}
			}catch(e){
				//do nothing
			}
			if (CanClose == "Y")
				ShowCloseProject();
			CloseWaitWindow();
		}

		function TestResultHandler(data)
		{
			bx_autotest_step++;
			try
			{
				var json_data=eval("(" +data+")");
				if (json_data)
				{
					if (json_data.STATUS)
						RefreshCheckList(json_data);
					else if (json_data.IN_PROGRESS == "Y" && bx_stoptest == false)
					{
						BX("percent").innerHTML = " &mdash; "+json_data.PERCENT+"%";
						AutoTest(json_data.TEST_ID,TestResultHandler,bx_autotest_step);
						return;
					}
				}
			}catch(e){
				//do nothing
			}

			if (bx_autotest == true)
			{
				bx_autotest_step = 0;
				bx_test_num++;
				if (bx_test_num<arAutoCheck.length && bx_stoptest == false)
				{
					AutoTest(arAutoCheck[bx_test_num],TestResultHandler);
					return;
				}
				if (CanClose == "Y")
					ShowCloseProject();
				else
					ShowResultInfo();
				loadButton("bx_start_button");
				start = "Y";
				bx_test_num = 0;
				bx_autotest_step = 0;
				bx_autotest = false;
				bx_stoptest = false;
				BX("current_test_name").innerHTML = "<?=GetMessage("CL_AUTOTEST_DONE");?>";
				BX("percent").innerHTML = "";
				CloseWaitWindow();
				return;
			}
			if (CanClose == "Y")
				ShowCloseProject();
			CloseWaitWindow();
		}

		function ScrollToProject()
		{
			var arNodePos = BX.pos(BX("project_check"));
			var animation = new BX.fx({
			start:0,
			finish :arNodePos.top-50,
			type:"accelerated",
			time:1,
			step:0.01,
			callback:function(value)
			{
				window.scroll(0,value)
			}

			});
			animation.start();
		}

		function StartAutoCheck(testID)
		{
			if (bx_autotest == true || bx_stoptest == true)
			{
				var buttonText =  BX.findChild(BX("bx_start_button"), {className:'checklist-button-cont'}, true, false);
				buttonText.innerHTML = "<?=GetMessage("CL_END_TEST_PROCCESS");?>";
				bx_stoptest = true;
				return;
			}
			ErrorSections = Array();
			bx_autotest = true;
			loadButton("bx_start_button");
			arTestResult = {"total":0,"success":0,"failed":0};
			BX("current_test_name").innerHTML = "<?=GetMessage("CL_TEST");?>: "+arAutoCheckName[bx_test_num];
			AutoTest(arAutoCheck[bx_test_num],TestResultHandler,bx_autotest_step);
		}

		function AutoTest(testID,callback,step)
		{
			var data = "ACTION=update&autotest=Y&TEST_ID="+testID+"&STEP="+step;
			for(var i=0; i<arAutoCheck.length; i++)
			{
				if(testID == arAutoCheck[i])
				{
					BX("current_test_name").innerHTML = "<?=GetMessage("CL_TEST");?>: "+arAutoCheckName[i];
					break;
				}
			}
			BX.ajax.post("/bitrix/admin/checklist.php"+"?lang=<?=LANG;?>&bxpublic=Y&<?=bitrix_sessid_get()?>",data,callback);
		}

		/*function SaveReport()
		{
			window.location = 'checklist.php?lang=<?=LANG?>&ACTION=ADDREPORT&<?=bitrix_sessid_get()?>';
		}*/

		function checkError()
		{
			var error_message = "";

			if(BX("COMPANY_NAME").value == "" || BX("TESTER").value == "" || BX("EMAIL").value == "" )
				error_message = "<?=GetMessage("CL_REQUIRE_FIELDS2");?>";
			if (error_message.length>0)
			{
				alert(error_message)
				return true;
			}
			else
				return false;
		}

		function SaveReport()
		{
		if (!checkError())
			{
				BX('about_tester').submit();
			}
		}

		var closePopup;
		var showedShowCloseProject = false;
		function ShowCloseProject(show)
		{
			if (showedShowCloseProject && show != true)
				return;
			var bx_info = document.createElement('div');
			var result = "";
			result+="<?=GetMessage("CL_TEST_TOTAL");?>: "+arMainStat.TOTAL;
			result+="<br><?=GetMessage("CL_TEST_CHECKED");?>: "+arMainStat.SUCCESS;
			result+= "<br><?=GetMessage("CL_TEST_FAILED");?>: "+arMainStat.FAILED;
			result+= "<br><?=GetMessage("CL_TEST_REQUIRE");?>: "+arMainStat.REQUIRE_CHECK;
			result+= "<br><br><b><?=GetMessage("CL_MANUAL_MINI_2");?></b>";
			result+= "<br><br>" +
					"<form id='about_tester' method='POST' action='checklist.php?lang=<?=LANG?>'>" +
					'<?=bitrix_sessid_post()?>' +
					"<input type='hidden' name='ACTION' value='ADDREPORT'>" +
						"<table border=0>" +
							"<tr><td><?=GetMessage('CL_REPORT_FIO_TESTER')?>:</td><td><input type='text' id='TESTER' name='TESTER' size=60 /></td></tr>" +
							"<tr><td><?=GetMessage('CL_REPORT_COMPANY_NAME')?>:</td><td><input type='text' id='COMPANY_NAME' name='COMPANY_NAME' size=60 /></td></tr>" +
							"<tr><td><?=GetMessage('CL_REPORT_EMAIL')?>:</td><td><input type='text' id='EMAIL' name='EMAIL' size=60 /></td></tr>" +
						"</table>" +
					"</form>";
			bx_info.innerHTML = result;
			BX.addClass(bx_info,"checklist-manual checklist-detail-popup-result");

			if (closePopup == undefined)
				closePopup= new BX.CAdminDialog(
				{
					title: "<?=GetMessage("CL_RESULT_TEST");?>",
					head: "",
					content: result,
					icon: "head-block",
					resizable: false,
					draggable: true,
					//height: "140",
					//width: "300",
					buttons: ['<input type="button" onclick="if (Dialog) Dialog.Close(); SaveReport();" value="<?=GetMessage("CL_SAVE_REPORT");?>">', BX.CAdminDialog.btnClose]
				});
			closePopup.Show();
			showedShowCloseProject = true;
		}

		function openErrorSection ()
		{
			if (ErrorSections.length>0)
			for(var i=0;i<ErrorSections.length;i++)
			{
				if (BX(ErrorSections[i]+"_name").parentNode)
					BX.addClass(BX(ErrorSections[i]+"_name").parentNode, 'testlist-open');
			}
		}

		var popupInfo;
		function ShowResultInfo()
		{
			var bx_info = document.createElement('div');
			var result = "";
			result+="<?=GetMessage("CL_TEST_TOTAL");?>: "+arTestResult.total;
			result+="<br><?=GetMessage("CL_TEST_CHECKED");?>: "+arTestResult.success;
			result+= "<br><?=GetMessage("CL_TEST_FAILED");?>: "+arTestResult.failed;
//			if (start == "N")
			result+='<br><br><hr><?=GetMessage("CL_MANUAL_MINI");?>';
			bx_info.innerHTML = result;

			BX.addClass(bx_info,"checklist-manual checklist-detail-popup-result");


			popupInfo = new BX.CAdminDialog(
			{
				title: "<?=GetMessage("CL_AUTOTEST_RESULT");?>",
				head: "",
				content: result,
				icon: "head-block",
				resizable: false,
				draggable: true,
				//height: "90",
				//width: "300",
				buttons: ['<input type="button" onclick="openErrorSection(); popupInfo.Close();" value="<?=GetMessage("CL_CLOSE");?>">']
			});
			popupInfo.Show();
		}

	function XSSReportModifier(data)
	{
		if (data.id != "QSEC0080")
			return;

		var fileboxes = BX.findChildren(data.reportNode, {className:"checklist-vulnscan-files"},true);
		if(!window.xssHelpPopup)
		{
			window.xssHelpPopup = new BX.PopupWindow("checklist_xssHelpPopup", null, {
				draggable: false,
				closeIcon:true,
				autoHide: false,
				angle:{position:"right",offset:94},
				offsetLeft:-360,
				offsetTop:-120,
				zIndex:1500,
				closeByEsc: false,
				bindOptions: {
					forceTop: true,
					forceLeft: false,
					position:"right"
				}
			});
			BX.addCustomEvent(data.parent,"onWindowClose",function(){window.xssHelpPopup.close();});
		}
		for(i in fileboxes)
		{
			button = new BX.PopupWindowButton({
						text : "?",
						events:{
							click:BX.proxy(function(){
								help = BX.findChild(this,{className:"checklist-vulnscan-helpbox"},true);
								var text = help.innerHTML;
								_this = BX.proxy_context;
								if(help)
								{
									window.xssHelpPopup.setBindElement(_this.buttonNode);
									window.xssHelpPopup.setContent(BX.create("DIV",{props:{className:"checklist-xss-popup"}, html: text}));
									window.xssHelpPopup.show();
								}

							},fileboxes[i])
						}
					});
			fileboxes[i].appendChild(BX.create("DIV",{
				style:{textAlign:"right", marginTop:"7px"},
				children:[button.buttonNode]
			})
			);
		}
	}
	BX.addCustomEvent("onAfterDetailReportShow", XSSReportModifier);
	BX.onCustomEvent('onAdminTabsChange');

	function ShowHideReports ()
	{
		if (showHiddenReports == 'Y')
			showHiddenReports = 'N';
		else
				showHiddenReports = 'Y';
		window.location = 'checklist.php?lang=<?=LANG?>&ACTION=CHANGELISTPROP&showHiddenReports='+showHiddenReports+'&<?=bitrix_sessid_get()?>';
	}


	</script>
	<?} else {?>
	<div class="checklist-body">
	<script>
		var showHiddenReports = "<?=$showHiddenReports?>";
		function ShowHideReports ()
		{
			if (showHiddenReports == 'Y')
				showHiddenReports = 'N';
			else
				showHiddenReports = 'Y';
			window.location = 'checklist.php?lang=<?=LANG?>&ACTION=CHANGELISTPROP&showHiddenReports='+showHiddenReports+'&<?=bitrix_sessid_get()?>';
		}
		function RefreshReportStatuses ()
		{
			BX.ajax.get("https://www.1c-bitrix.ru/buy_tmp/partner_check_key_for_qc.php?key=" + "<?=md5(trim(LICENSE_KEY))?>", {}, function (data) {
				var json_data=eval("(" +data+")");
				if (json_data.ERROR !== undefined)
				{
					window.location = 'checklist.php?lang=<?=LANG?>&ACTION=RESETBITRIXSTATUS'+'&<?=bitrix_sessid_get()?>';
				}
			});
		}
		function ShowHint (el)
		{
			el.BXHINT = new BX.CHint({
				parent: el,
				show_timeout: 200,
				hide_timeout: 200,
				dx: 2,
				showOnce: false,
				preventHide: true,
				min_width: 250,
				hint: '<?=CUtil::JSEscape(GetMessage('CL_SAVE_SEND_REPORT_HINT'))?>',
				//title: 'title'
			});
			el.BXHINT.Show();
		}
		function showProjectForm (report_id)
		{
			BX('report_id').value = report_id;
			BX('bx_project_form').style.display = 'block';
			BX('bx_start_button').style.display = 'none';
			BX('checklist_manual').style.display = 'none';
			BX('checklist_manual2').style.display = 'block';
		}
		function checkError()
		{
			var error_message = "";

			if(BX("CLIENT").value == "" || BX("CLIENT_POST").value == "" || BX("PHONE").value == "" || BX("EMAIL").value == "" || BX("COMPANY_NAME").value == "")
			{
				error_message = "<?=GetMessage("CL_REQUIRE_FIELDS");?>";
				alert(error_message)
				return true;
			}
			else
				return false;
		}

		function SaveSendReport()
		{
			if (!checkError())
			{
				BX("type_action").value = "ADDSENDREPORT";
				BX('bx_project_form').submit();
			}
		}

		function hideReport (report_id)
		{
			window.location = 'checklist.php?lang=<?=LANG?>&ACTION=SHOWHIDEELEMENTS&report_id='+report_id+'&report_action=hide&<?=bitrix_sessid_get()?>';
		}

		function showReport (report_id)
		{
			window.location = 'checklist.php?lang=<?=LANG?>&ACTION=SHOWHIDEELEMENTS&report_id='+report_id+'&report_action=show&<?=bitrix_sessid_get()?>';
		}
	</script>
		<div id='checklist_manual'>
			<?echo BeginNote();?>
			<?=GetMessage("CL_MANUAL");?>
			<?echo EndNote();?>
		</div>
		<div id='checklist_manual2' style="display: none">
			<?echo BeginNote();?>
			<?=GetMessage("CL_MANUAL2");?>
			<?echo EndNote();?>
		</div>
		<form id="bx_start_test" action="?lang=<?=LANG;?>" method="POST">
			<?=bitrix_sessid_post()?>
			<input type="hidden" name = "bx_start_test"  value="Y">
		</form>
		<a id="bx_start_button" class="adm-btn adm-btn-green adm-btn-add" onclick="BX('bx_start_test').submit();"><?=GetMessage("CL_BEGIN");?></a>
		<?ShowReportList();?>
		<?echo BeginNote();?>
		<?=GetMessage("CL_MANUAL_TEST");?>
		<?echo EndNote();?>
	<?}?>
</div>
<?function ShowReportList()
{
	global $showHiddenReports;
	$arFilter = array("REPORT"=>"Y");
	if ($showHiddenReports == 'N')
	{
		$arFilter['HIDDEN'] = 'N';
	}
	$dbReport = CCheckListResult::GetList(Array(),$arFilter);
	while ($arReport = $dbReport->Fetch())
		$arReports[]=$arReport;?>

	<form id="bx_project_form" style="display:none;" action="" method="POST" enctype="multipart/form-data">
		<?=bitrix_sessid_post()?>
		<div class="checklist-result-form-content" >
		<?=BeginNote()?>
			<h2><?=GetMessage("CL_FORM_ABOUT_CLIENT_TITLE");?></h2>
			<div class="checklist-result-form-content-field">
				<input id="report_id" name="report_id" type="hidden"/>
				<label><?=GetMessage("CL_REPORT_COMPANY_NAME");?></label><input id="COMPANY_NAME" name="COMPANY_NAME" type="text"/>
			</div>
			<div class="checklist-result-form-content-field">
				<label><?=GetMessage("CL_REPORT_CLIENT_NAME");?></label><input id="CLIENT" name="CLIENT" type="text"/>
			</div>
			<div class="checklist-result-form-content-field">
				<label><?=GetMessage("CL_REPORT_CLIENT_POST");?></label><input id="CLIENT_POST" name="CLIENT_POST" type="text"/>
			</div>
			<div class="checklist-result-form-content-field">
				<label><?=GetMessage("CL_REPORT_PHONE");?></label>
				<table width="100%" style="border-spacing: 0px">
					<tr>
					<td width="65%" style="border: 0px; padding: 0">
					<input id="PHONE" name="PHONE" type="text" style="width:100%"/>
					</td>
					<td width="55" style="border: 0px; padding: 0">
					<div style="text-align: right"><?=GetMessage("CL_REPORT_PHONE_ADD");?>&nbsp;</div>
					</td>
					<td style="border: 0px">
					<input id="PHONE_ADD" name="PHONE_ADD" type="text"/>
					</td>
					</tr>
				</table>
			</div>
			<div class="checklist-result-form-content-field">
				<label><?=GetMessage("CL_REPORT_EMAIL");?></label><input id="EMAIL" name="EMAIL" type="text"/>
			</div>
			<div class="checklist-result-textarea-wrap">
				<label><?=GetMessage("CL_REPORT_COMMENT");?></label>
				<div class="checklist-result-textarea">
					<textarea style="color: #AAAAAA" id="report_comment" OnFocus="if (this.value =='<?=GetMessage("CL_REPORT_COMMENT_HELP")?>') {this.value = ''; this.style.color = '#000000';}" OnBlur="if (this.value == '') {this.value = '<?=GetMessage("CL_REPORT_COMMENT_HELP")?>'; this.style.color = '#AAAAAA';}" name="COMMENT" class="checklist-textarea"><?=GetMessage("CL_REPORT_COMMENT_HELP")?></textarea>
				</div>
			</div>
			<input id="type_action" type="hidden" name="ACTION" value="ADDSENDREPORT">
			<div class="checklist-result-form-button">
				<a class="adm-btn adm-btn-green adm-btn" onclick="SaveSendReport();"><?=GetMessage("CL_SAVE_SEND_REPORT");?></a>
			</div>
		<?=EndNote()?>
		</div>

	</form>

	<?
	$exists_sended_to_bitrix = CCheckListResult::GetList(Array(),Array("SENDED_TO_BITRIX"=>"Y"))->Fetch();
	if(count($arReports)>0) {?>
		<div class="checklist-archive-rept">
			<?=GetMessage("CL_REPORT_ARCHIVE");?>
			<table class="checklist-archive-table" cellspacing="0">
				<tr class="checklist-archive-table-header">
					<td><?=GetMessage("CL_REPORT_DATE");?></td>
					<td><?=GetMessage("CL_REPORT_FIO_TESTER");?> (<?=GetMessage("CL_REPORT_COMPANY_NAME")?>)</td>
					<td><?=GetMessage("CL_REPORT_TABLE_TOTAL");?></td>
					<td><?=GetMessage("CL_REPORT_TABLE_CHECKED");?></td>
					<td><?=GetMessage("CL_REPORT_TABLE_FAILED");?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<?foreach ($arReports as $k=>$arReport):?>
					<tr class="">
						<td><?=$arReport["DATE_CREATE"]?></td>
						<td><?=$arReport["TESTER"]?> (<?=$arReport["COMPANY_NAME"]?>)</td>
						<td><?=$arReport["TOTAL"]?></td>
						<td><?=$arReport["SUCCESS"]?></td>
						<td><?=$arReport["FAILED"]?></td>
						<td><a class="checklist-archive-table-detail" href="/bitrix/admin/checklist_report.php?ID=<?=$arReport["ID"];?>&lang=<?=LANG;?>"><?=GetMessage("CL_REPORT_TABLE_DETAIL");?></a></td>
						<td>
							<?if ($arReport["SENDED_TO_BITRIX"] == 'N' && $k == 0) {?>
								<?if(!$exists_sended_to_bitrix) {?>
									<a href="" onmouseover="ShowHint(this)" onclick="showProjectForm(<?=$arReport["ID"]?>); return false;"><?=GetMessage("CL_SAVE_SEND_REPORT_CUT");?></a>
								<?} else {?>
									&nbsp;
								<?}?>
							<?} elseif ($arReport["SENDED_TO_BITRIX"] == 'Y') {?>
								<?=GetMessage("CL_REPORT_SENDED");?>
							<?} else {?>
								&nbsp;
							<?}?>
						</td>
						<td>
							<?if ($arReport["HIDDEN"] == 'N') {?>
							<a href="" onclick="hideReport(<?=$arReport["ID"]?>); return false;"><?=GetMessage('CL_HIDE_REPORT')?></a>
							<?} else {?>
							<a href="" onclick="showReport(<?=$arReport["ID"]?>); return false;"><?=GetMessage('CL_SHOW_REPORT')?></a>
							<?}?>
						</td>
					</tr>
				<?endforeach;?>
			</table>
			<br>
		</div>
		<div>
			<div style="float: right">
				<input type="checkbox" id="sh_chk" onClick="ShowHideReports()" <?=($showHiddenReports=='Y' ? 'checked' : '')?>><label for="sh_chk"> <?=GetMessage('CL_SHOW_HIDDEN')?></label>
			</div>
			<div>
			<?if($exists_sended_to_bitrix) {?>
				<a class="adm-btn adm-btn-green " onclick="RefreshReportStatuses();"><?=GetMessage("CL_REFRESH_REPORT_STATUSES");?></a>
			<?} else {?>
				<br><br>
			<?}?>
			</div>
		</div>
		<script>
			BX.adminFormTools.modifyCheckbox(BX('sh_chk'))
		</script>
	<?} else {?>
		<div style="margin-top:15px"></div>
	<?}?>
<?}

function SendReportToBitrix ($arFields)
{
	global $APPLICATION;

	$APPLICATION->RestartBuffer();

	$arFields['LICENSE_KEY'] = md5(trim(LICENSE_KEY));
?>
	<?=GetMessage('CL_SENDING_QC_REPORT')?>
	<form id="bx_project_tests_send" style="display:none;" action="http://partners.1c-bitrix.ru/personal/send_quality_control.php" method="POST">
		<input type="hidden" name="charset" value="<?=htmlspecialcharsbx(LANG_CHARSET)?>" />
		<?foreach ($arFields as $key=>$val) {?>
			<input type="hidden" name="<?=$key?>" value="<?=htmlspecialcharsbx($val)?>" />
		<?}?>
	</form>
	<script>
		document.getElementById('bx_project_tests_send').submit();
	</script>
<?
	die;
}
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");?>
