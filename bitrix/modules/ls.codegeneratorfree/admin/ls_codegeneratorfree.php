<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
CUtil::InitJSCore(array('ajax'));
$RIGHT = $APPLICATION->GetGroupRight("main");
if ($RIGHT == "D")
	$APPLICATION->AuthForm(GetMessage("PTB_NOT_ACCESS"));
$arReplace = array();
$temp = explode("//", GetMessage("PTB_TRANSLIT"));
foreach ($temp as $k=>$val) {
	$temp[$k] = explode("/", $val);
	$arReplace[$temp[$k][0]] = trim($temp[$k][1]);
}

function getSimCode($arReplace, $name, $action, $str_space_replace, $str_other_replace) {
	$code = strtr(trim($str), $arReplace);
	$code = preg_replace("@\s+@i", $str_space_replace, $code);
	$code = preg_replace('@(\++|\'+|\.+|\"+|\*+|\(+|\)+|\/+|\”+|\,+|\:+|)@i', '', $code);
	$code = preg_replace('@\_+@i',$str_other_replace, $code);
	
	switch ($action) {
		case "L":
			$code = strtolower($code);
			break;
		case "U":
			$code = strtoupper($code);
			break;
	};
	return $code;
}

function CheckCodeSection($IBLOCK_ID, $CODE, $ID) {
	$res = CIBlockSection::GetList(
		Array("ID"=>"ASC"), 
		Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>$CODE, "!ID"=>$ID), 
		false,
		Array("ID")
	);
	if($item = $res->GetNext()) {
		return 1;
	};
	return 0;
}

function CheckCodeElement($IBLOCK_ID, $CODE, $ID) {
	$count = 0;
	$res = CIBlockElement::GetList(
		Array("ID"=>"ASC"), 
		Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>$CODE, "!ID"=>$ID), 
		false,
		false,
		Array("ID")
	);
	if($item = $res->GetNext()) {
		return 1;
	};
	return 0;
}

function CheckEnd($IBLOCK_ID, $LID) {
	$all_count = CIBlockElement::GetList(
		array("ID" => "ASC"), 
		array("IBLOCK_ID" => $IBLOCK_ID)
	);
	$all = $all_count->SelectedRowsCount();
	
	$last_count = CIBlockElement::GetList(
		array("ID" => "ASC"), 
		array("IBLOCK_ID" => $IBLOCK_ID, "<=ID" => $LID)
	);
	$last = $last_count->SelectedRowsCount();
	if ($last==$all)
		return true;
	else 
		return false;
}

if($_SERVER["REQUEST_METHOD"] == "POST" && $_REQUEST["ptb_gener_start"] == "Y") {
	CModule::IncludeModule("iblock");
	CUtil::JSPostUnescape();
	
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");
	
	if(array_key_exists("params", $_REQUEST) && is_array($_REQUEST["params"])){
		foreach ($_REQUEST["params"] as $key=>$value)
			if(!is_array($value))
				$params[$key] = htmlspecialchars($value);
	} else {
		$params = Array(
			"ptb_gen_chp" => $_REQUEST["ptb_gen_chp"],
			"ptb_gen_chall" => $_REQUEST["ptb_gen_chall"],
			"ptb_gen_reg" => $_REQUEST["ptb_gen_reg"],
			"ptb_gener_start" => $_REQUEST["ptb_gener_start"],
			"ptb_sel_tiblock" => $_REQUEST["ptb_sel_tiblock"],
			"ptb_sel_iblock" => $_REQUEST["ptb_sel_iblock"],
			"ptb_gen_element_shag" => $_REQUEST["ptb_gen_element_shag"],
			"ptb_gen_dubl" => $_REQUEST["ptb_gen_dubl"],
			"ptb_lid" => $_REQUEST["ptb_lid"],
			"ptb_gen_isset" => $_REQUEST["ptb_gen_isset"],
			"ptb_gen_sections" => $_REQUEST["ptb_gen_sections"],
			"ptb_gen_elements" => $_REQUEST["ptb_gen_elements"],
			"ptb_gen_element_count" => 0,
			"ptb_gen_show_sections" => "N",
			"ptb_errors" => Array(),
			"ptb_reply_code" => Array()
		);
	};
	
	$arrError = Array();
	$arParamsCode = Array(
		"max_len" => 255,
		"replace_space" => $params["ptb_gen_chp"],
		"replace_other" => $params["ptb_gen_chall"],
		"delete_repeat_replace" => true,
		"change_case" => $params["ptb_gen_reg"]
	);
	
	if($params["ptb_gen_sections"] == "Y") {
		$count_s = 0;
		$sec = new CIBlockSection;
		$arFilter = Array("IBLOCK_ID"=>$params["ptb_sel_iblock"]);
		if($params["ptb_gen_isset"] == "Y")
			$arFilter["CODE"] = false;
		$res = CIBlockSection::GetList(
			Array("ID"=>"ASC"), 
			$arFilter, 
			false,
			Array("ID","NAME")
		);
		while($item = $res->GetNext()) {
			if($params["ptb_gen_myfunction"] == "Y")
				$code = getSimCode($arReplace, trim($item["NAME"]), $params["ptb_gen_reg"], $params["ptb_gen_chp"], $params["ptb_gen_chall"]);
			else
				$code = CUtil::translit(trim($item["NAME"]), "ru", $arParamsCode);
			$isset_code = CheckCodeSection($params["ptb_sel_iblock"], $code, $item["ID"]);
			if($isset_code > 0) {
				switch($params["ptb_gen_dubl"]) {
					case 1:
						$code .= $isset_code + $params["ptb_reply_code"][$code]++;
						break;
					case 2:
						$code .= $item["ID"];
						break;
					case 3:
						continue;
						break;
				};
			};
			$arFields = Array("CODE"=>$code);
			$add = $sec->Update($item["ID"], $arFields);
			if(!$add)
				$params["ptb_errors"][] = GetMessage("PTB_ERROR_LIST", Array("#NAME#"=>$item["NAME"], "#ERROR#"=>$sec->LAST_ERROR));
			else
				$count_s++;
		};
		$params["ptb_gen_sections"] = "N";
		$params["ptb_gen_show_sections"] = "Y";
		$params["ptb_gen_sections_count"] = $count_s;
		
		$jsParams = CUtil::PhpToJSObject(array("params"=>$params));
		if($params["ptb_gen_elements"] == "Y") {
			CAdminMessage::ShowMessage(array(
				"MESSAGE"=>GetMessage("PTB_PROGRESS_STATUS_ON"),
				"DETAILS"=>"<b>".GetMessage("PTB_PROPGRESS_SECTION")."</b>".$count_s."<br/><b>".GetMessage("PTB_PROPGRESS_ELEMENT")."</b>0",
				"HTML"=>true,
				"TYPE"=>"OK",
			));
			echo '<script>
				generation('.$jsParams.');
			</script>';
		} else {
			if(count($params["ptb_errors"])>0)
				CAdminMessage::ShowMessage(array(
					"MESSAGE"=>GetMessage("PTB_ERROR_TEXT"),
					"DETAILS"=>implode("",$params["ptb_errors"]),
					"HTML"=>true,
					"TYPE"=>"ERROR",
				));
			
			CAdminMessage::ShowMessage(array(
				"MESSAGE"=>GetMessage("PTB_PROGRESS_STATUS_OK"),
				"DETAILS"=>"<b>".GetMessage("PTB_PROPGRESS_SECTION")."</b>".$count_s,
				"HTML"=>true,
				"TYPE"=>"OK",
			));
			echo '<script>
				generation_finish();
			</script>';
		};
		exit;
	};

	if($params["ptb_gen_elements"] == "Y") {
		$el = new CIBlockElement;
		$arFilter = Array("IBLOCK_ID"=>$params["ptb_sel_iblock"], ">ID" => $params["ptb_lid"]);
		if($params["ptb_gen_isset"] == "Y")
			$arFilter["CODE"] = false;
		$res = CIBlockElement::GetList(
			Array("ID"=>"ASC"), 
			$arFilter, 
			false,
			array("nTopCount" => $params["ptb_gen_element_shag"]),
			Array("ID","NAME")
		);
		while($item = $res->GetNext()) {
			if($params["ptb_gen_myfunction"] == "Y")
				$code = getSimCode($arReplace, trim($item["NAME"]), $params["ptb_gen_reg"], $params["ptb_gen_chp"], $params["ptb_gen_chall"]);
			else
				$code = CUtil::translit(trim($item["NAME"]), "ru", $arParamsCode);
			$isset_code = CheckCodeElement($params["ptb_sel_iblock"], $code, $item["ID"]);
			if($isset_code > 0) {
				switch($params["ptb_gen_dubl"]) {
					case 1:
						$code .= $isset_code + $params["ptb_reply_code"]++;
						break;
					case 2:
						$code .= $item["ID"];
						break;
					case 3:
						continue;
						break;
				};
			};
			$arFields = Array("CODE"=>$code);
			$add = $el->Update($item["ID"], $arFields);
			if(!$add)
				$params["ptb_errors"][] = GetMessage("PTB_ERROR_LIST2", Array("#NAME#"=>$item["NAME"], "#ERROR#"=>$el->LAST_ERROR));
			else {
				$params["ptb_gen_element_count"]++;
			}
			$params["ptb_lid"] = $item["ID"];
		};
		
		$jsParams = CUtil::PhpToJSObject(array("params"=>$params));
		if(CheckEnd($params["ptb_sel_iblock"], $params["ptb_lid"]))  {
			if($params["ptb_gen_show_sections"] == "Y") {
				if(count($params["ptb_errors"])>0)
					CAdminMessage::ShowMessage(array(
						"MESSAGE"=>GetMessage("PTB_ERROR_TEXT"),
						"DETAILS"=>implode("",$params["ptb_errors"]),
						"HTML"=>true,
						"TYPE"=>"ERROR",
					));
				CAdminMessage::ShowMessage(array(
					"MESSAGE"=>GetMessage("PTB_PROGRESS_STATUS_OK"),
					"DETAILS"=>"<b>".GetMessage("PTB_PROPGRESS_SECTION")."</b>".$params["ptb_gen_sections_count"]."<br/><b>".GetMessage("PTB_PROPGRESS_ELEMENT")."</b>".$params["ptb_gen_element_count"],
					"HTML"=>true,
					"TYPE"=>"OK",
				));
				
				echo '<script>
					generation_finish();
				</script>';
			} else {
				if(count($params["ptb_errors"])>0)
					CAdminMessage::ShowMessage(array(
						"MESSAGE"=>GetMessage("PTB_ERROR_TEXT"),
						"DETAILS"=>implode("",$params["ptb_errors"]),
						"HTML"=>true,
						"TYPE"=>"ERROR",
					));
				CAdminMessage::ShowMessage(array(
					"MESSAGE"=>GetMessage("PTB_PROGRESS_STATUS_OK"),
					"DETAILS"=>"<b>".GetMessage("PTB_PROPGRESS_ELEMENT")."</b>".$params["ptb_gen_element_count"],
					"HTML"=>true,
					"TYPE"=>"OK",
				));
				echo '<script>
					generation_finish();
				</script>';
			};
		} else {
			if($params["ptb_gen_show_sections"] == "Y") {
				CAdminMessage::ShowMessage(array(
					"MESSAGE"=>GetMessage("PTB_PROGRESS_STATUS_ON"),
					"DETAILS"=>"<b>".GetMessage("PTB_PROPGRESS_SECTION")."</b>".$params["ptb_gen_sections_count"]."<br/><b>".GetMessage("PTB_PROPGRESS_ELEMENT")."</b>".$params["ptb_gen_element_count"],
					"HTML"=>true,
					"TYPE"=>"OK",
				));
				echo '<script>
					generation('.$jsParams.');
				</script>';
			} else {
				CAdminMessage::ShowMessage(array(
					"MESSAGE"=>GetMessage("PTB_PROGRESS_STATUS_ON"),
					"DETAILS"=>"<b>".GetMessage("PTB_PROPGRESS_ELEMENT")."</b>".$params["ptb_gen_element_count"],
					"HTML"=>true,
					"TYPE"=>"OK",
				));
				echo '<script>
					generation('.$jsParams.');
				</script>';
			};
		};
	};
	require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin_js.php");
	exit;
}

if($_SERVER["REQUEST_METHOD"] == "POST" && $_REQUEST["gener_empty"] == "Y") {
	
	CModule::IncludeModule("iblock");
	CUtil::JSPostUnescape();
	
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");
	
	if(array_key_exists("params", $_REQUEST) && is_array($_REQUEST["params"])){
		foreach ($_REQUEST["params"] as $key=>$value)
			if(!is_array($value))
				$params[$key] = htmlspecialchars($value);
	} else {
		$params = Array(
			"ptb_gener_start" => $_REQUEST["ptb_gener_start"],
			"ptb_sel_tiblock" => $_REQUEST["ptb_sel_tiblock"],
			"ptb_sel_iblock" => $_REQUEST["ptb_sel_iblock"],
			"ptb_gen_element_shag" => $_REQUEST["ptb_gen_element_shag"],
			"ptb_lid" => $_REQUEST["ptb_lid"],
			"ptb_gen_sections" => $_REQUEST["ptb_gen_sections"],
			"ptb_gen_elements" => $_REQUEST["ptb_gen_elements"],
			"ptb_gen_element_count" => 0,
			"ptb_gen_show_sections" => "N"
		);
	};
	
	$arrError = Array();
	
	if($params["ptb_gen_sections"] == "Y") {
		$count_s = 0;
		$sec = new CIBlockSection;
		$arFilter = Array("IBLOCK_ID"=>$params["ptb_sel_iblock"]);
		$res = CIBlockSection::GetList(
			Array("ID"=>"ASC"), 
			$arFilter, 
			false,
			Array("ID","NAME")
		);
		while($item = $res->GetNext()) {
			$arFields = Array("CODE"=>"");
			$add = $sec->Update($item["ID"], $arFields);
			if(!$add)
				$params["ptb_errors"][] = GetMessage("PTB_ERROR_LIST", Array("#NAME#"=>$item["NAME"], "#ERROR#"=>$sec->LAST_ERROR));
			else
				$count_s++;
		};
		$params["ptb_gen_sections"] = "N";
		$params["ptb_gen_show_sections"] = "Y";
		$params["ptb_gen_sections_count"] = $count_s;
		
		$jsParams = CUtil::PhpToJSObject(array("params"=>$params));
		if($params["ptb_gen_elements"] == "Y") {
			CAdminMessage::ShowMessage(array(
				"MESSAGE"=>GetMessage("PTB_PROGRESS_STATUS_ON"),
				"DETAILS"=>"<b>".GetMessage("PTB_PROPGRESS_SECTION")."</b>".$count_s."<br/><b>".GetMessage("PTB_PROPGRESS_ELEMENT")."</b>0",
				"HTML"=>true,
				"TYPE"=>"OK",
			));
			echo '<script>
				cleaning('.$jsParams.');
			</script>';
		} else {
			if(count($params["ptb_errors"])>0)
				CAdminMessage::ShowMessage(array(
					"MESSAGE"=>GetMessage("PTB_ERROR_TEXT"),
					"DETAILS"=>implode("",$params["ptb_errors"]),
					"HTML"=>true,
					"TYPE"=>"ERROR",
				));
			
			CAdminMessage::ShowMessage(array(
				"MESSAGE"=>GetMessage("PTB_PROGRESS_STATUS_OK"),
				"DETAILS"=>"<b>".GetMessage("PTB_PROPGRESS_SECTION")."</b>".$count_s,
				"HTML"=>true,
				"TYPE"=>"OK",
			));
			echo '<script>
				generation_finish();
			</script>';
		};
		
		exit;
	};

	if($params["ptb_gen_elements"] == "Y") {
		
		$el = new CIBlockElement;
		$arFilter = Array("IBLOCK_ID"=>$params["ptb_sel_iblock"], ">ID" => $params["ptb_lid"]);
		$res = CIBlockElement::GetList(
			Array("ID"=>"ASC"), 
			$arFilter, 
			false,
			array("nTopCount" => $params["ptb_gen_element_shag"]),
			Array("ID","NAME")
		);
		while($item = $res->GetNext()) {
			$arFields = Array("CODE"=>"");
			$add = $el->Update($item["ID"], $arFields);
			if(!$add)
				$params["ptb_errors"][] = GetMessage("PTB_ERROR_LIST2", Array("#NAME#"=>$item["NAME"], "#ERROR#"=>$el->LAST_ERROR));
			else {
				$params["ptb_gen_element_count"]++;
			}
			$params["ptb_lid"] = $item["ID"];
		};
		
		$jsParams = CUtil::PhpToJSObject(array("params"=>$params));
		if(CheckEnd($params["ptb_sel_iblock"], $params["ptb_lid"]))  {
			if($params["ptb_gen_show_sections"] == "Y") {
				if(count($params["ptb_errors"])>0)
					CAdminMessage::ShowMessage(array(
						"MESSAGE"=>GetMessage("PTB_ERROR_TEXT"),
						"DETAILS"=>implode("",$params["ptb_errors"]),
						"HTML"=>true,
						"TYPE"=>"ERROR",
					));
				CAdminMessage::ShowMessage(array(
					"MESSAGE"=>GetMessage("PTB_PROGRESS_STATUS_OK"),
					"DETAILS"=>"<b>".GetMessage("PTB_PROPGRESS_SECTION")."</b>".$params["ptb_gen_sections_count"]."<br/><b>".GetMessage("PTB_PROPGRESS_ELEMENT")."</b>".$params["ptb_gen_element_count"],
					"HTML"=>true,
					"TYPE"=>"OK",
				));
				
				echo '<script>
					generation_finish();
				</script>';
			} else {
				if(count($params["ptb_errors"])>0)
					CAdminMessage::ShowMessage(array(
						"MESSAGE"=>GetMessage("PTB_ERROR_TEXT"),
						"DETAILS"=>implode("",$params["ptb_errors"]),
						"HTML"=>true,
						"TYPE"=>"ERROR",
					));
				CAdminMessage::ShowMessage(array(
					"MESSAGE"=>GetMessage("PTB_PROGRESS_STATUS_OK"),
					"DETAILS"=>"<b>".GetMessage("PTB_PROPGRESS_ELEMENT")."</b>".$params["ptb_gen_element_count"],
					"HTML"=>true,
					"TYPE"=>"OK",
				));
				echo '<script>
					generation_finish();
				</script>';
			};
		} else {
			if($params["ptb_gen_show_sections"] == "Y") {
				CAdminMessage::ShowMessage(array(
					"MESSAGE"=>GetMessage("PTB_PROGRESS_STATUS_ON"),
					"DETAILS"=>"<b>".GetMessage("PTB_PROPGRESS_SECTION")."</b>".$params["ptb_gen_sections_count"]."<br/><b>".GetMessage("PTB_PROPGRESS_ELEMENT")."</b>".$params["ptb_gen_element_count"],
					"HTML"=>true,
					"TYPE"=>"OK",
				));
				echo '<script>
					cleaning('.$jsParams.');
				</script>';
			} else {
				CAdminMessage::ShowMessage(array(
					"MESSAGE"=>GetMessage("PTB_PROGRESS_STATUS_ON"),
					"DETAILS"=>"<b>".GetMessage("PTB_PROPGRESS_ELEMENT")."</b>".$params["ptb_gen_element_count"],
					"HTML"=>true,
					"TYPE"=>"OK",
				));
				echo '<script>
					cleaning('.$jsParams.');
				</script>';
			};
		};
	};
	require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin_js.php");
	exit;
}

$APPLICATION->SetTitle(GetMessage("PTB_CODEGEN_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
$aTabs = array(
	array(
		"DIV" => "ptb_gen",
		"TAB" => GetMessage("PTB_TAB"),
		"ICON" => "main_user_edit",
		"TITLE" => GetMessage("PTB_TAB")
	)
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if(!CModule::IncludeModule("iblock"))
	echo GetMessage("PTB_ERROR_IBLOCK");
	
$dbIBlockType = CIBlockType::GetList();
$arIBTypes = array();
$arIB = array();
while ($arIBType = $dbIBlockType->Fetch())
{
	if ($arIBTypeData = CIBlockType::GetByIDLang($arIBType["ID"], LANG))
	{
		$arIB[$arIBType['ID']] = array();
		$arIBTypes[$arIBType['ID']] = '['.$arIBType['ID'].'] '.$arIBTypeData['NAME'];
	}
}

$dbIBlock = CIBlock::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y'));
while ($arIBlock = $dbIBlock->Fetch())
{
	$arIB[$arIBlock['IBLOCK_TYPE_ID']][$arIBlock['ID']] = ($arIBlock['CODE'] ? '['.$arIBlock['CODE'].'] ' : '').$arIBlock['NAME'];
};

?>

<div id="ptb_window"></div>
<form id="ptb_codegenerator" method="POST" action="<?=$APPLICATION->GetCurPage()?>?lang=<?=LANG?>" name="ptb_codegenerator">
<?
echo bitrix_sessid_post();
$tabControl->Begin();
$tabControl->BeginNextTab();
?>

<input type="hidden" name="ptb_lid" id="ptb_lid" value="0" />

		<tr>
			<td width="300"><label for="ptb_sel_tiblock"><?=GetMessage("PTB_SEL_TIBLOCK");?></label></td>
			<td>
				<select name="ptb_sel_tiblock" id="ptb_sel_tiblock" onchange="changeIblockList(this.value)">
					<option value="0"><?=GetMessage('PTB_NOT_SET')?></option>
					<?foreach ($arIBTypes as $ibtype_id => $ibtype_name) {?>
						<option value="<?=$ibtype_id?>"><?=$ibtype_name?></option>
					<?};?>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="ptb_sel_iblock"><?=GetMessage("PTB_SEL_IBLOCK");?></label></td>
			<td>
				<select id="ptb_sel_iblock" name="ptb_sel_iblock">
					<option value="0"><?=GetMessage('PTB_NOT_SET')?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="ptb_gen_element_shag"><?=GetMessage("PTB_GEN_SHAG");?></label></td>
			<td><input type="text" name="ptb_gen_element_shag" id="ptb_gen_element_shag" value="150"  /></td>
		</tr>
		<tr>
			<td><label for="ptb_gen_sections"><?=GetMessage("PTB_GEN_SECTIONS");?></label></td>
			<td><input type="checkbox" name="ptb_gen_sections" id="ptb_gen_sections" value="Y" checked /></td>
		</tr>
		<tr>
			<td><label for="ptb_gen_elements"><?=GetMessage("PTB_GEN_ELEMENTS");?></label></td>
			<td><input type="checkbox" name="ptb_gen_elements" id="ptb_gen_elements" value="Y" checked /></td>
		</tr>
		<tr>
			<td><label for="ptb_gen_chp"><?=GetMessage("PTB_GEN_CHP");?></label></td>
			<td><input type="text" name="ptb_gen_chp" size="2" id="ptb_gen_chp" value="-" checked /></td>
		</tr>
		<tr>
			<td><label for="ptb_gen_chall"><?=GetMessage("PTB_GEN_CHALL");?></label></td>
			<td><input type="text" name="ptb_gen_chall" size="2" id="ptb_gen_chall" value="-" checked /></td>
		</tr>
		<tr>
			<td><label for="ptb_gen_reg"><?=GetMessage("PTB_GEN_REG");?></label></td>
			<td>
				<select id="ptb_gen_reg" name="ptb_gen_reg">
					<option value="L"><?=GetMessage('PTB_GEN_REG1')?></option>
					<option value="U"><?=GetMessage('PTB_GEN_REG2')?></option>
					<option value="false"><?=GetMessage('PTB_GEN_REG3')?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="ptb_gen_dubl"><?=GetMessage("PTB_GEN_DUBL");?></label></td>
			<td>
				<select id="ptb_gen_dubl" name="ptb_gen_dubl">
					<option value="2"><?=GetMessage('PTB_GEN_DUBL2')?></option>
					<option value="1"><?=GetMessage('PTB_GEN_DUBL1')?></option>
					<option value="3"><?=GetMessage('PTB_GEN_DUBL3')?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="ptb_gen_isset"><?=GetMessage("PTB_GEN_ISSET");?></label></td>
			<td><input type="checkbox" name="ptb_gen_isset" id="ptb_gen_isset" value="Y" /></td>
		</tr>
		<tr>
			<td><label for="ptb_gen_myfunction"><?=GetMessage("PTB_GEN_MYFUNCTION");?></label></td>
			<td><input type="checkbox" name="ptb_gen_myfunction" id="ptb_gen_myfunction" value="Y" /></td>
		</tr>
		<tr>
			<td colspan="2" style="color:#999;"><?=GetMessage("PTB_GEN_MYFUNCTION2");?><br/><?=GetMessage("PTB_PRIMTEXT");?></td>
		</tr>	
<?
$tabControl->Buttons();
?>
<input type="button" id="start_generation" value="<?echo GetMessage("PTB_CODEGEN_START")?>" OnClick="GenerStart();" class="adm-btn-save" />
<input type="button" id="stop_generation" value="<?=GetMessage("PTB_CODEGEN_STOP")?>" OnClick="GenerStop();" disabled />
<input type="button" id="empty_code" value="<?=GetMessage("PTB_CODEGEN_EMPTY")?>" OnClick="EmptyCode();"  />
<?
$tabControl->End();
?>
</form>
<script>
var finish_get = 0;
var arIblocks = <?= CUtil::PhpToJsObject($arIB)?>;
function changeIblockList(value) {
	var j, arControls = BX('ptb_sel_iblock');

	if (arControls)
		arControls.options.length = 0;

	if (value == 0)
	{
		arControls.options[0] = new Option('<?=GetMessage('PTB_NOT_SET')?>', '0');
	}

	for (j in arIblocks[value])
		arControls.options[arControls.options.length] = new Option(arIblocks[value][j], j);
}
function EmptyCode() {
	finish_get = 0;
	var tiblock = document.getElementById("ptb_sel_tiblock");
	var iblock = document.getElementById("ptb_sel_iblock");
	if(tiblock.value == 0 || iblock.value == 0) {
		alert("<?=GetMessage("PTB_GEN_ERROR_IBLOCK_EMPTY");?>");
		return false
	};
	if(!document.getElementById('ptb_gen_sections').checked && !document.getElementById('ptb_gen_elements').checked) {
		alert("<?=GetMessage("PTB_GEN_ERROR_DOCSEL");?>");
		return false
	}
	ShowWaitWindow();
	document.getElementById('start_generation').disabled = "disabled";
	document.getElementById('empty_code').disabled = "disabled";
	document.getElementById('stop_generation').disabled = "";
	cleaning();
}
function GenerStart() {
	finish_get = 0;
	var tiblock = document.getElementById("ptb_sel_tiblock");
	var iblock = document.getElementById("ptb_sel_iblock");
	if(tiblock.value == 0 || iblock.value == 0) {
		alert("<?=GetMessage("PTB_GEN_ERROR_IBLOCK_EMPTY");?>");
		return false
	};
	if(!document.getElementById('ptb_gen_sections').checked && !document.getElementById('ptb_gen_elements').checked) {
		alert("<?=GetMessage("PTB_GEN_ERROR_DOCSEL");?>");
		return false
	}
	ShowWaitWindow();
	document.getElementById('start_generation').disabled = "disabled";
	document.getElementById('empty_code').disabled = "disabled";
	document.getElementById('stop_generation').disabled = "";

	generation();
}
function GenerStop() {
	finish_get = 1;
	generation_finish();
	document.getElementById('ptb_window').innerHTML = '';
}
function generation(params) {
	var query = "ptb_gener_start=Y" + "&lang=<?echo LANG;?>";
	if(!params) {
		query += "&<?echo bitrix_sessid_get()?>";
		query += "&ptb_sel_tiblock=" + document.getElementById('ptb_sel_tiblock').value;
		query += "&ptb_sel_iblock=" + document.getElementById('ptb_sel_iblock').value;
		query += "&ptb_gen_element_shag=" + document.getElementById('ptb_gen_element_shag').value;
		if(document.getElementById('ptb_gen_sections').checked){
			query += "&ptb_gen_sections=" + document.getElementById('ptb_gen_sections').value;
		}
		if(document.getElementById('ptb_gen_elements').checked){
			query += "&ptb_gen_elements=" + document.getElementById('ptb_gen_elements').value;
		}
		query += "&ptb_gen_chp=" + document.getElementById('ptb_gen_chp').value;
		query += "&ptb_gen_chall=" + document.getElementById('ptb_gen_chall').value;
		query += "&ptb_gen_reg=" + document.getElementById('ptb_gen_reg').value;
		query += "&ptb_gen_dubl=" + document.getElementById('ptb_gen_dubl').value;
		if(document.getElementById('ptb_gen_isset').checked){
			query += "&ptb_gen_isset=" + document.getElementById('ptb_gen_isset').value;
		}
		query += "&ptb_lid=" + document.getElementById('ptb_lid').value;
	};
	if(finish_get != 1) {
		BX.ajax.post(
			'ls_codegeneratorfree.php?'+query,
			params,
			function(res){
				document.getElementById('ptb_window').innerHTML = res;	
			}
		);
	} else {
		document.getElementById('ptb_window').innerHTML = '';
	};
}
function cleaning(params) {
	var query = "gener_empty=Y" + "&lang=<?echo LANG;?>";
	if(!params) {
		query += "&<?echo bitrix_sessid_get()?>";
		query += "&ptb_sel_tiblock=" + document.getElementById('ptb_sel_tiblock').value;
		query += "&ptb_sel_iblock=" + document.getElementById('ptb_sel_iblock').value;
		query += "&ptb_gen_element_shag=" + document.getElementById('ptb_gen_element_shag').value;
		if(document.getElementById('ptb_gen_sections').checked){
			query += "&ptb_gen_sections=" + document.getElementById('ptb_gen_sections').value;
		}
		if(document.getElementById('ptb_gen_elements').checked){
			query += "&ptb_gen_elements=" + document.getElementById('ptb_gen_elements').value;
		}
		query += "&ptb_lid=" + document.getElementById('ptb_lid').value;
	};
	if(finish_get != 1) {
		BX.ajax.post(
			'ls_codegeneratorfree.php?'+query,
			params,
			function(res){
				document.getElementById('ptb_window').innerHTML = res;	
			}
		);
	} else {
		document.getElementById('ptb_window').innerHTML = '';
	};
}
function generation_finish() {
	document.getElementById('start_generation').disabled = "";
	document.getElementById('empty_code').disabled = "";
	document.getElementById('stop_generation').disabled = "disabled";
	CloseWaitWindow();
}
</script>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>