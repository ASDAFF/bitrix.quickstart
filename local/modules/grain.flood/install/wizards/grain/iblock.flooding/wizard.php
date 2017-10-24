<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

define("WIZ_IBFC_SOURCE_CHARSET", "utf-8");
define("WIZ_IBFC_MAX_ELEMENTS", 50000);
define("WIZ_IBFC_MAX_SECTIONS", 20000);
define("WIZ_IBF_DATA_DIR", "/bitrix/wizards/grain/iblock.flooding/data/");
define("WIZ_IBF_VARS_DIR", "/bitrix/wizards/grain/iblock.flooding/temp/");

$spath=pathinfo(__FILE__);
require_once($spath["dirname"]."/scripts/functions.php");

class Step1 extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(Loc::getMessage('GRAIN_FLOOD_IBF_STEP1_TITLE'));
		$this->SetStepID("step1");
		$this->SetCancelStep("cancel");
	}

	function ShowStep()
	{
		
		if(CModule::IncludeModule("iblock")) {
		
			$this->content = "";
		
			$rsIBlockTypes = CIBlockType::GetList(Array("SORT"=>"ASC"));
			
			while($arIBlockType=$rsIBlockTypes->Fetch()) if($arIBType = CIBlockType::GetByIDLang($arIBlockType["ID"], LANG)) {
			
				$this->content .= "<b>".$arIBType["NAME"]."</b><br />";
				
				$rsIblocks = CIBlock::GetList(Array("SORT"=>"ASC"),Array("TYPE"=>$arIBlockType["ID"]));
				
				while($arIBlock=$rsIblocks->GetNext()) {
				
					$this->content .= "<label>".$this->ShowRadioField("WIZ_IBF_IBLOCK_ID", $arIBlock["ID"])." [".$arIBlock["ID"]."] ".$arIBlock["NAME"]."</label><br />";
			
				}
			
			}

			$this->SetNextStep("step2");
			
		} else {

			$this->SetError(Loc::getMessage('GRAIN_FLOOD_IBF_ERROR1'), "error1");

		}
		
	}

	function OnPostForm()
	{
		$wizard = &$this->GetWizard();
	
		global $DB;
	
		if ($wizard->IsNextButtonClick() || $wizard->IsFinishButtonClick())
		{
			$arErrors = Array();
			
			if (intval($wizard->GetVar("WIZ_IBF_IBLOCK_ID"))<=0) $arErrors[] = Loc::getMessage('GRAIN_FLOOD_IBF_ERROR2');

			if(count($arErrors)>0) $this->SetError(implode("<br />", $arErrors), "error1");
			
		}
	}


}


class Step2 extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(Loc::getMessage('GRAIN_FLOOD_IBF_STEP2_TITLE'));
		$this->SetPrevStep("step1");
		$this->SetNextStep("step3");
		$this->SetStepID("step2");

		$this->SetCancelStep("cancel");
		
		$wizard = &$this->GetWizard();

		$wizard->SetDefaultVar("WIZ_IBF_STEP_LENGTH", "10");

		$wizard->SetDefaultVar("WIZ_IBF_SECTION_NAME", Loc::getMessage('GRAIN_FLOOD_IBF_STEP2_DEFAULT_SECTION_NAME'));
		$wizard->SetDefaultVar("WIZ_IBF_SECTION_NAME_ADD_NUMBERS", "Y");
		$wizard->SetDefaultVar("WIZ_IBF_SECTION_NAME_ADD_ID", "N");
		$wizard->SetDefaultVar("WIZ_IBF_SECTION_NAME_ADD_DEPTH_LEVEL", "N");
		$wizard->SetDefaultVar("WIZ_IBF_SECTION_CODE", "");
		$wizard->SetDefaultVar("WIZ_IBF_SECTION_CODE_ADD_NUMBERS", "N");
		$wizard->SetDefaultVar("WIZ_IBF_SECTION_CODE_ADD_ID", "N");
		$wizard->SetDefaultVar("WIZ_IBF_SECTION_CODE_ADD_DEPTH_LEVEL", "N");
 
		$wizard->SetDefaultVar("WIZ_IBF_NAME_SOURCE", "custom");
		$wizard->SetDefaultVar("WIZ_IBF_NAME", Loc::getMessage('GRAIN_FLOOD_IBF_STEP2_DEFAULT_NAME'));
		$wizard->SetDefaultVar("WIZ_IBF_NAME_CLAUSE_COUNT", "1");
		$wizard->SetDefaultVar("WIZ_IBF_NAME_ADD_NUMBERS", "Y");
		$wizard->SetDefaultVar("WIZ_IBF_NAME_ADD_ID", "N");
		$wizard->SetDefaultVar("WIZ_IBF_NAME_ADD_SECTION_NAME", "N");
		$wizard->SetDefaultVar("WIZ_IBF_NAME_ADD_SECTION_ID", "N");
		$wizard->SetDefaultVar("WIZ_IBF_CODE", "");
		$wizard->SetDefaultVar("WIZ_IBF_CODE_ADD_NUMBERS", "N");
		$wizard->SetDefaultVar("WIZ_IBF_CODE_ADD_ID", "N");
		$wizard->SetDefaultVar("WIZ_IBF_CODE_ADD_SECTION_CODE", "N");
		$wizard->SetDefaultVar("WIZ_IBF_CODE_ADD_SECTION_ID", "N");
		$wizard->SetDefaultVar("WIZ_IBF_ACTIVE_FROM", ConvertTimeStamp(time(),"FULL"));
		$wizard->SetDefaultVar("WIZ_IBF_ACTIVE_FROM_ADD_SECONDS", "0");
		$wizard->SetDefaultVar("WIZ_IBF_PREVIEW_TEXT_SOURCE", "none");
		$wizard->SetDefaultVar("WIZ_IBF_PREVIEW_TEXT_CLAUSE_COUNT", "3");
		$wizard->SetDefaultVar("WIZ_IBF_PREVIEW_TEXT", "");
		$wizard->SetDefaultVar("WIZ_IBF_DETAIL_TEXT_SOURCE", "none");
		$wizard->SetDefaultVar("WIZ_IBF_DETAIL_TEXT_CLAUSE_COUNT", "12");
		$wizard->SetDefaultVar("WIZ_IBF_DETAIL_TEXT", "");
		$wizard->SetDefaultVar("WIZ_IBF_PREVIEW_PICTURE_SOURCE", "none");
		$wizard->SetDefaultVar("WIZ_IBF_DETAIL_PICTURE_SOURCE", "none");
		$wizard->SetDefaultVar("WIZ_IBF_PREVIEW_PICTURE_SOURCE_RANDOM", Array());
		$wizard->SetDefaultVar("WIZ_IBF_DETAIL_PICTURE_SOURCE_RANDOM", Array());
		$wizard->SetDefaultVar("WIZ_IBF_PREVIEW_PICTURE_SOURCE_SELECTED", "");
		$wizard->SetDefaultVar("WIZ_IBF_DETAIL_PICTURE_SOURCE_SELECTED", "");
		$wizard->SetDefaultVar("WIZ_IBF_PARENT_SECTION", "0");
		$wizard->SetDefaultVar("WIZ_IBF_SECTIONS_QUANTITY_LEVEL_1", "0");
		$wizard->SetDefaultVar("WIZ_IBF_SECTIONS_QUANTITY_LEVEL_2", "0");
		$wizard->SetDefaultVar("WIZ_IBF_SECTIONS_QUANTITY_LEVEL_3", "0");
		$wizard->SetDefaultVar("WIZ_IBF_SECTIONS_QUANTITY_LEVEL_4", "0");
		$wizard->SetDefaultVar("WIZ_IBF_ELEMENTS_QUANTITY_LEVEL_1", "0");
		$wizard->SetDefaultVar("WIZ_IBF_ELEMENTS_QUANTITY_LEVEL_2", "0");
		$wizard->SetDefaultVar("WIZ_IBF_ELEMENTS_QUANTITY_LEVEL_3", "0");
		$wizard->SetDefaultVar("WIZ_IBF_ELEMENTS_QUANTITY_LEVEL_4", "0");
		$wizard->SetDefaultVar("WIZ_IBF_ELEMENTS_QUANTITY_LEVEL_5", "0");

	}

	function ShowStep()
	{

		$wizard =& $this->GetWizard();
		$step_length = intval($wizard->GetVar('WIZ_IBF_STEP_LENGTH'));
		$path = $wizard->package->path;

		CModule::IncludeModule("iblock");

		$arImageFolders = GFloodWizardTools::GetFolderList("images");
		$arTextFiles = GFloodWizardTools::GetFileList("texts");

		$arTextSources = Array("none"=>Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_TEXT_SOURCE_NONE"));
		$arTextSources["custom"]=Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_TEXT_SOURCE_CUSTOM");
		foreach($arTextFiles as $textFile) {
			if(substr($textFile,-4)!==".php")
				continue;
			$textFile = preg_replace("/\.php$/","",$textFile);
			$arTextSources[$textFile] = $textFile;
			
		}

		$this->content = <<<EOT

<style type="text/css">
table.inr { border-collapse: collapse; width: 100%; margin-bottom: 10px; }
table.inr th { border: 1px solid #d0d7d8; padding: 5px; font-size: 11px; font-weight: normal; background-color:#e0e8ea }
table.inr td { border: 1px solid #d0d7d8; padding: 5px; font-size: 11px; }
table.inr > tr:nth-child(2n), table.inr > tbody > tr:nth-child(2n) { background-color: #f0f4f4 }
table.inr label { cursor: pointer }
table.wizibf-table-name { width: 100%; border: none }
table.wizibf-table-name td { border: none; padding: 0px }
table.wizibf-table-name td.wizibf-table-name-col-name { width: 196px }
table.wizibf-table-name td.wizibf-table-name-col-name input { width: 188px }
img.wizibf-table-edit-file-icon { vertical-align: middle }
span.wizibf-tooltip { font-size: 0.8em; color: #888; display: block }
</style>

<script type="text/javascript">

function WizIbfTextTypeChange(container_id,type) {
    var obContainerCustom = document.getElementById(container_id+'_custom');
    if(obContainerCustom) {
    	obContainerCustom.style.display = type=="custom"?"":"none";
    }

    var obContainerText = document.getElementById(container_id+'_text');
    if(obContainerText) {
    	obContainerText.style.display = type!="none" && type!="custom"?"":"none";
    }
}

function WizIbfImageTypeChange(container_id,type) {

    var obContainerRandom = document.getElementById(container_id+'_random');
    if(obContainerRandom) {
    	obContainerRandom.style.display = type=="random"?"":"none";
    }

    var obContainerSelected = document.getElementById(container_id+'_selected');
    if(obContainerSelected) {
    	obContainerSelected.style.display = type=="selected"?"":"none";
    }

}

wizibf_tmp_input_id = "";

window.wizibfMedialibReturn = function(return_path) {

	if(window.wizibf_tmp_input_id.length>0) {
		
		obField = document.getElementById(window.wizibf_tmp_input_id);
		
		if(obField) obField.value=return_path.src;
	
	}

}

window.wizibfFilemanReturn = function(filename,foldername,site_id) {

	if(window.wizibf_tmp_input_id.length>0) {
		
		obField = document.getElementById(window.wizibf_tmp_input_id);
		
		if(obField) obField.value=foldername+"/"+filename;
	
	}

}


</script>

EOT;

		ob_start();

		CAdminFileDialog::ShowScript(Array(
		    "event" => "_wizibf_OpenFM",
		    "arResultDest" => array("FUNCTION_NAME" => "wizibfFilemanReturn"),
		    "arPath" => array("SITE" => SITE_ID, "PATH" =>"/upload"),
		    "select" => 'F',// F - file only, D - folder only
		    "operation" => 'O',
		    "showUploadTab" => true,
		    "showAddToMenuTab" => false,
		    "allowAllFiles" => true,
		    "SaveConfig" => true,
		));
		
		CMedialib::ShowDialogScript(Array(
		    "event" => "_wizibf_OpenML",
		    "arResultDest" => array("FUNCTION_NAME" => "wizibfMedialibReturn"),
		    "arPath" => array("SITE" => SITE_ID, "PATH" =>"/upload"),
		    "select" => 'F',// F - file only, D - folder only
		    "operation" => 'O',
		    "showUploadTab" => true,
		    "showAddToMenuTab" => false,
		    "allowAllFiles" => true,
		    "SaveConfig" => true,
		));
		
		$this->content .= ob_get_contents();
		ob_end_clean();

		$this->content .= $this->ShowHiddenField("WIZ_IBF_DATA_DIR",WIZ_IBF_DATA_DIR);
		$this->content .= $this->ShowHiddenField("WIZ_IBF_VARS_DIR",WIZ_IBF_VARS_DIR);

		// Parameters

		$this->content .= "<h3>".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_HEADER_SECTIONS")."</h3>";

		$this->content .= "<table class=\"inr\" cellspacing=\"0\">";

		$this->content .= "<tr>";
		$this->content .= "<th>".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_TABLE_HEADER_PARAMETER")."</th>";
		$this->content .= "<th>".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_TABLE_HEADER_VALUE")."</th>";
		$this->content .= "</tr>";

		$arSections = Array("0"=>Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_PARAMETER_CREATE_SECTIONS_ROOT"));
		$rsSections = CIBlockSection::GetTreeList(Array("IBLOCK_ID"=>$wizard->GetVar("WIZ_IBF_IBLOCK_ID")));
		while($arSection=$rsSections->GetNext()) $arSections[$arSection["ID"]] = str_repeat(".",$arSection["DEPTH_LEVEL"]).$arSection["NAME"];
		
		$this->content .= "<tr>";
		$this->content .= "<td>".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_PARAMETER_PARENT_FOLDER")."</td>";
		$this->content .= "<td>".$this->ShowSelectField("WIZ_IBF_PARENT_SECTION",$arSections)."</td>";
		$this->content .= "</tr>";

		$this->content .= "<tr>";
		$this->content .= "<td>".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_PARAMETER_CREATE_SECTIONS")."<span class=\"wizibf-tooltip\">".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_PARAMETER_CREATE_SECTIONS_TOOLTIP")."</span></td>";
		$this->content .= "<td>";
		for($i=1;$i<=4;$i++) {
			if($i>1) $this->content .= "&gt;";
			$this->content .= $this->ShowInputField("text", "WIZ_IBF_SECTIONS_QUANTITY_LEVEL_".$i, Array("size" => "3","maxlength"=>"5"));
		}
		$this->content .= "</td>";
		$this->content .= "</tr>";

		$this->content .= "<tr>";
		$this->content .= "<td>".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_PARAMETER_ELEMENTS_QUANTITY")."<span class=\"wizibf-tooltip\">".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_PARAMETER_ELEMENTS_QUANTITY_TOOLTIP")."</span></td>";
		$this->content .= "<td>";
		for($i=1;$i<=5;$i++) {
			if($i>1) $this->content .= "&gt;";
			$this->content .= $this->ShowInputField("text", "WIZ_IBF_ELEMENTS_QUANTITY_LEVEL_".$i, Array("size" => "3","maxlength"=>"5"));
		}
		$this->content .= "</td>";
		$this->content .= "</tr>";


		$this->content .= "<tr>";
		$this->content .= "<td>".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_FIELD_SECTION_NAME")."</td>";
		$this->content .= "<td>";
		$this->content .= "<table class=\"wizibf-table-name\">";
		$this->content .= "<tr><td rowspan=\"4\" class=\"wizibf-table-name-col-name\">";
		$this->content .= $this->ShowInputField("text", "WIZ_IBF_SECTION_NAME", Array("size" => "28"));
		$this->content .= "</td>";
		$this->content .= "<td>";
		$this->content .= $this->ShowCheckboxField("WIZ_IBF_SECTION_NAME_ADD_NUMBERS","Y",Array("id"=>"WIZ_IBF_SECTION_NAME_ADD_NUMBERS"));
		$this->content .= "&nbsp;<label for=\"WIZ_IBF_SECTION_NAME_ADD_NUMBERS\">".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_ADD_NUMBERS")."</label>";
		$this->content .= "</td></tr>";
		$this->content .= "<tr><td>";
		$this->content .= $this->ShowCheckboxField("WIZ_IBF_SECTION_NAME_ADD_ID","Y",Array("id"=>"WIZ_IBF_SECTION_NAME_ADD_ID"));
		$this->content .= "&nbsp;<label for=\"WIZ_IBF_SECTION_NAME_ADD_ID\">".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_ADD_ID")."</label>";
		$this->content .= "</td></tr>";
		$this->content .= "<tr><td>";
		$this->content .= $this->ShowCheckboxField("WIZ_IBF_SECTION_NAME_ADD_DEPTH_LEVEL","Y",Array("id"=>"WIZ_IBF_SECTION_NAME_ADD_DEPTH_LEVEL"));
		$this->content .= "&nbsp;<label for=\"WIZ_IBF_SECTION_NAME_ADD_DEPTH_LEVEL\">".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_ADD_DEPTH_LEVEL")."</label>";
		$this->content .= "</td></tr>";
		$this->content .= "</table>";
		$this->content .= "</td>";
		$this->content .= "</tr>";

		$this->content .= "<tr>";
		$this->content .= "<td>".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_FIELD_SECTION_CODE")."</td>";
		$this->content .= "<td>";
		$this->content .= "<table class=\"wizibf-table-name\">";
		$this->content .= "<tr><td rowspan=\"4\" class=\"wizibf-table-name-col-name\">";
		$this->content .= $this->ShowInputField("text", "WIZ_IBF_SECTION_CODE", Array("size" => "28"));
		$this->content .= "</td>";
		$this->content .= "<td>";
		$this->content .= $this->ShowCheckboxField("WIZ_IBF_SECTION_CODE_ADD_NUMBERS","Y",Array("id"=>"WIZ_IBF_SECTION_CODE_ADD_NUMBERS"));
		$this->content .= "&nbsp;<label for=\"WIZ_IBF_SECTION_CODE_ADD_NUMBERS\">".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_ADD_NUMBERS")."</label>";
		$this->content .= "</td></tr>";
		$this->content .= "<tr><td>";
		$this->content .= $this->ShowCheckboxField("WIZ_IBF_SECTION_CODE_ADD_ID","Y",Array("id"=>"WIZ_IBF_SECTION_CODE_ADD_ID"));
		$this->content .= "&nbsp;<label for=\"WIZ_IBF_SECTION_CODE_ADD_ID\">".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_ADD_ID")."</label>";
		$this->content .= "</td></tr>";
		$this->content .= "<tr><td>";
		$this->content .= $this->ShowCheckboxField("WIZ_IBF_SECTION_CODE_ADD_DEPTH_LEVEL","Y",Array("id"=>"WIZ_IBF_SECTION_CODE_ADD_DEPTH_LEVEL"));
		$this->content .= "&nbsp;<label for=\"WIZ_IBF_SECTION_CODE_ADD_DEPTH_LEVEL\">".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_ADD_DEPTH_LEVEL")."</label>";
		$this->content .= "</td></tr>";
		$this->content .= "</table>";
		$this->content .= "</td>";
		$this->content .= "</tr>";

		$this->content .= "</table>";

		// Elements

		$this->content .= "<h3>".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_HEADER_FIELDS")."</h3>";

		$this->content .= "<table class=\"inr\" cellspacing=\"0\">";

		$this->content .= "<tr>";
		$this->content .= "<th>".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_TABLE_HEADER_FIELD")."</th>";
		$this->content .= "<th>".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_TABLE_HEADER_PARAMETERS")."</th>";
		$this->content .= "</tr>";

		$this->content .= "<tr>";
		$this->content .= "<td>".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_FIELD_NAME")."</td>";
		$this->content .= "<td>";
		$this->content .= "<table class=\"wizibf-table-name\">";
		$this->content .= "<tr><td rowspan=\"4\" class=\"wizibf-table-name-col-name\">";
		
		$arTextSourcesTmp = $arTextSources;
		unset($arTextSourcesTmp["none"]);
		$this->content .= Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_TEXT_SOURCE").": ".$this->ShowSelectField("WIZ_IBF_NAME_SOURCE",$arTextSourcesTmp,Array("onchange"=>"WizIbfTextTypeChange('WIZ_IBF_NAME_SOURCE_CONTAINER',this.value)"));
		
		$this->content .= "<div id=\"WIZ_IBF_NAME_SOURCE_CONTAINER_text\"".(!$wizard->GetVar("WIZ_IBF_NAME_SOURCE") || in_array($wizard->GetVar("WIZ_IBF_NAME_SOURCE"),array("none","custom"))?" style=\"display:none\"":"").">".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_TEXT_CLAUSE_COUNT").": ".$this->ShowInputField("text", "WIZ_IBF_NAME_CLAUSE_COUNT", Array("size" => "3"))."</div>";

		$this->content .= "<div id=\"WIZ_IBF_NAME_SOURCE_CONTAINER_custom\"".(!!$wizard->GetVar("WIZ_IBF_NAME_SOURCE") && $wizard->GetVar("WIZ_IBF_NAME_SOURCE")!="custom"?" style=\"display:none\"":"").">".$this->ShowInputField("text", "WIZ_IBF_NAME", Array("size" => "28"))."</div>";
		
		$this->content .= "</td>";
		$this->content .= "<td>";
		$this->content .= $this->ShowCheckboxField("WIZ_IBF_NAME_ADD_NUMBERS","Y",Array("id"=>"WIZ_IBF_NAME_ADD_NUMBERS"));
		$this->content .= "&nbsp;<label for=\"WIZ_IBF_NAME_ADD_NUMBERS\">".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_ADD_NUMBERS")."</label>";
		$this->content .= "</td></tr>";
		$this->content .= "<tr><td>";
		$this->content .= $this->ShowCheckboxField("WIZ_IBF_NAME_ADD_ID","Y",Array("id"=>"WIZ_IBF_NAME_ADD_ID"));
		$this->content .= "&nbsp;<label for=\"WIZ_IBF_NAME_ADD_ID\">".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_ADD_ID")."</label>";
		$this->content .= "</td></tr>";
		$this->content .= "<tr><td>";
		$this->content .= $this->ShowCheckboxField("WIZ_IBF_NAME_ADD_SECTION_NAME","Y",Array("id"=>"WIZ_IBF_NAME_ADD_SECTION_NAME"));
		$this->content .= "&nbsp;<label for=\"WIZ_IBF_NAME_ADD_SECTION_NAME\">".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_ADD_SECTION_NAME")."</label>";
		$this->content .= "</td></tr>";
		$this->content .= "<tr><td>";
		$this->content .= $this->ShowCheckboxField("WIZ_IBF_NAME_ADD_SECTION_ID","Y",Array("id"=>"WIZ_IBF_NAME_ADD_SECTION_ID"));
		$this->content .= "&nbsp;<label for=\"WIZ_IBF_NAME_ADD_SECTION_ID\">".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_ADD_SECTION_ID")."</label>";
		$this->content .= "</td></tr>";
		$this->content .= "</table>";
		$this->content .= "</td>";
		$this->content .= "</tr>";

		$this->content .= "<tr>";
		$this->content .= "<td>".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_FIELD_CODE")."</td>";
		$this->content .= "<td>";
		$this->content .= "<table class=\"wizibf-table-name\">";
		$this->content .= "<tr><td rowspan=\"4\" class=\"wizibf-table-name-col-name\">";
		$this->content .= $this->ShowInputField("text", "WIZ_IBF_CODE", Array("size" => "28"));
		$this->content .= "</td>";
		$this->content .= "<td>";
		$this->content .= $this->ShowCheckboxField("WIZ_IBF_CODE_ADD_NUMBERS","Y",Array("id"=>"WIZ_IBF_CODE_ADD_NUMBERS"));
		$this->content .= "&nbsp;<label for=\"WIZ_IBF_CODE_ADD_NUMBERS\">".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_ADD_NUMBERS")."</label>";
		$this->content .= "</td></tr>";
		$this->content .= "<tr><td>";
		$this->content .= $this->ShowCheckboxField("WIZ_IBF_CODE_ADD_ID","Y",Array("id"=>"WIZ_IBF_CODE_ADD_ID"));
		$this->content .= "&nbsp;<label for=\"WIZ_IBF_CODE_ADD_ID\">".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_ADD_ID")."</label>";
		$this->content .= "</td></tr>";
		$this->content .= "<tr><td>";
		$this->content .= $this->ShowCheckboxField("WIZ_IBF_CODE_ADD_SECTION_CODE","Y",Array("id"=>"WIZ_IBF_CODE_ADD_SECTION_CODE"));
		$this->content .= "&nbsp;<label for=\"WIZ_IBF_CODE_ADD_SECTION_CODE\">".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_ADD_SECTION_CODE")."</label>";
		$this->content .= "</td></tr>";
		$this->content .= "<tr><td>";
		$this->content .= $this->ShowCheckboxField("WIZ_IBF_CODE_ADD_SECTION_ID","Y",Array("id"=>"WIZ_IBF_CODE_ADD_SECTION_ID"));
		$this->content .= "&nbsp;<label for=\"WIZ_IBF_CODE_ADD_SECTION_ID\">".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_ADD_SECTION_ID")."</label>";
		$this->content .= "</td></tr>";
		$this->content .= "</table>";
		$this->content .= "</td>";
		$this->content .= "</tr>";

		$this->content .= "<tr>";
		$this->content .= "<td>".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_FIELD_ACTIVE_FROM")."</td>";
		$this->content .= "<td>".$this->ShowInputField("text", "WIZ_IBF_ACTIVE_FROM", Array("size" => "18"))." ".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_ADD_SECONDS1")." ".$this->ShowInputField("text", "WIZ_IBF_ACTIVE_FROM_ADD_SECONDS", Array("size" => "6"))." ".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_ADD_SECONDS2")."</td>";
		
		$this->content .= "</tr>";

		$this->content .= "<tr>";
		$this->content .= "<td>".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_FIELD_PREVIEW_TEXT")."</td>";
		$this->content .= "<td>";
		
		$this->content .= Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_TEXT_SOURCE").": ".$this->ShowSelectField("WIZ_IBF_PREVIEW_TEXT_SOURCE",$arTextSources,Array("onchange"=>"WizIbfTextTypeChange('WIZ_IBF_PREVIEW_TEXT_SOURCE_CONTAINER',this.value)"));
		
		$this->content .= "<div id=\"WIZ_IBF_PREVIEW_TEXT_SOURCE_CONTAINER_text\"".(!$wizard->GetVar("WIZ_IBF_PREVIEW_TEXT_SOURCE") || in_array($wizard->GetVar("WIZ_IBF_PREVIEW_TEXT_SOURCE"),array("none","custom"))?" style=\"display:none\"":"").">".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_TEXT_CLAUSE_COUNT").": ".$this->ShowInputField("text", "WIZ_IBF_PREVIEW_TEXT_CLAUSE_COUNT", Array("size" => "3"))."</div>";

		$this->content .= "<div id=\"WIZ_IBF_PREVIEW_TEXT_SOURCE_CONTAINER_custom\"".($wizard->GetVar("WIZ_IBF_PREVIEW_TEXT_SOURCE")!="custom"?" style=\"display:none\"":"").">".$this->ShowInputField("textarea", "WIZ_IBF_PREVIEW_TEXT", Array("cols" => "28", "rows" => "4"))."</div>";
		
		$this->content .= "</td>";
		$this->content .= "</tr>";

		$this->content .= "<tr>";
		$this->content .= "<td>".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_FIELD_DETAIL_TEXT")."</td>";
		$this->content .= "<td>";

		$this->content .= Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_TEXT_SOURCE").": ".$this->ShowSelectField("WIZ_IBF_DETAIL_TEXT_SOURCE",$arTextSources,Array("onchange"=>"WizIbfTextTypeChange('WIZ_IBF_DETAIL_TEXT_SOURCE_CONTAINER',this.value)"));
		
		$this->content .= "<div id=\"WIZ_IBF_DETAIL_TEXT_SOURCE_CONTAINER_text\"".(!$wizard->GetVar("WIZ_IBF_DETAIL_TEXT_SOURCE") || in_array($wizard->GetVar("WIZ_IBF_DETAIL_TEXT_SOURCE"),array("none","custom"))?" style=\"display:none\"":"").">".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_TEXT_CLAUSE_COUNT").": ".$this->ShowInputField("text", "WIZ_IBF_DETAIL_TEXT_CLAUSE_COUNT", Array("size" => "3"))."</div>";

		$this->content .= "<div id=\"WIZ_IBF_DETAIL_TEXT_SOURCE_CONTAINER_custom\"".($wizard->GetVar("WIZ_IBF_DETAIL_TEXT_SOURCE")!="custom"?" style=\"display:none\"":"").">".$this->ShowInputField("textarea", "WIZ_IBF_DETAIL_TEXT", Array("cols" => "28", "rows" => "4"))."</div>";
		
		$this->content .= "</td>";
		$this->content .= "</tr>";

		$this->content .= "<tr>";
		$this->content .= "<td>".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_FIELD_PREVIEW_PICTURE")."</td>";
		$this->content .= "<td>";
		$this->content .= "<label>".$this->ShowRadioField("WIZ_IBF_PREVIEW_PICTURE_SOURCE","none",Array("onclick"=>"WizIbfImageTypeChange('WIZ_IBF_PREVIEW_PICTURE_SOURCE_CONTAINER',this.value)"))." ".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_IMAGE_SOURCE_NONE")."</label>";
		$this->content .= "<label>".$this->ShowRadioField("WIZ_IBF_PREVIEW_PICTURE_SOURCE","random",Array("onclick"=>"WizIbfImageTypeChange('WIZ_IBF_PREVIEW_PICTURE_SOURCE_CONTAINER',this.value)"))." ".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_IMAGE_SOURCE_RANDOM")."</label>";
		$this->content .= "<label>".$this->ShowRadioField("WIZ_IBF_PREVIEW_PICTURE_SOURCE","selected",Array("onclick"=>"WizIbfImageTypeChange('WIZ_IBF_PREVIEW_PICTURE_SOURCE_CONTAINER',this.value)"))." ".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_IMAGE_SOURCE_SELECT")."</label>";
		
		$this->content .= "<div id=\"WIZ_IBF_PREVIEW_PICTURE_SOURCE_CONTAINER_random\"".($wizard->GetVar("WIZ_IBF_PREVIEW_PICTURE_SOURCE")!="random"?" style=\"display:none\"":"").">";
		$i=0;
		foreach($arImageFolders as $folder) {
			if($i>0) $this->content .= "<br />";
			$this->content .= $this->ShowCheckboxField("WIZ_IBF_PREVIEW_PICTURE_SOURCE_RANDOM[]",$folder,Array("id"=>"WIZ_IBF_PREVIEW_PICTURE_SOURCE_RANDOM_".$i))."<label for=\"WIZ_IBF_PREVIEW_PICTURE_SOURCE_RANDOM_".$i."\"> ".$folder."</label>";
			$i++;
		}
		$this->content .= "</div>";

		$this->content .= "<div id=\"WIZ_IBF_PREVIEW_PICTURE_SOURCE_CONTAINER_selected\"".($wizard->GetVar("WIZ_IBF_PREVIEW_PICTURE_SOURCE")!="selected"?" style=\"display:none\"":"").">";

		$this->content .= $this->ShowInputField("text", "WIZ_IBF_PREVIEW_PICTURE_SOURCE_SELECTED", Array("size" => "18","id"=>"WIZ_IBF_PREVIEW_PICTURE_SOURCE_SELECTED"));

		$this->content .= "&nbsp;<a href=\"#\" onclick=\"window.wizibf_tmp_input_id='WIZ_IBF_PREVIEW_PICTURE_SOURCE_SELECTED'; window._wizibf_OpenFM(); return false;\" title=\"".Loc::getMessage("GRAIN_FLOOD_IBF_FILE_SELECT_STRUCTURE")."\"><img class=\"wizibf-table-edit-file-icon\" src=\"/bitrix/images/fileman/medialib/tabs/server.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".Loc::getMessage("GRAIN_FLOOD_IBF_FILE_SELECT_STRUCTURE")."\" /></a>";

		$this->content .= "&nbsp;<a href=\"#\" onclick=\"window.wizibf_tmp_input_id='WIZ_IBF_PREVIEW_PICTURE_SOURCE_SELECTED'; window._wizibf_OpenML(); return false;\" title=\"".Loc::getMessage("GRAIN_FLOOD_IBF_FILE_SELECT_MEDIALIB")."\"><img class=\"wizibf-table-edit-file-icon\" src=\"/bitrix/images/fileman/medialib/tabs/media.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".Loc::getMessage("GRAIN_FLOOD_IBF_FILE_SELECT_MEDIALIB")."\" /></a>";

		$this->content .= "</div>";
		
		$this->content .= "</td>";
		$this->content .= "</tr>";

		$this->content .= "<tr>";
		$this->content .= "<td>".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_FIELD_DETAIL_PICTURE")."</td>";
		$this->content .= "<td>";
		$this->content .= "<label>".$this->ShowRadioField("WIZ_IBF_DETAIL_PICTURE_SOURCE","none",Array("onclick"=>"WizIbfImageTypeChange('WIZ_IBF_DETAIL_PICTURE_SOURCE_CONTAINER',this.value)"))." ".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_IMAGE_SOURCE_NONE")."</label>";
		$this->content .= "<label>".$this->ShowRadioField("WIZ_IBF_DETAIL_PICTURE_SOURCE","random",Array("onclick"=>"WizIbfImageTypeChange('WIZ_IBF_DETAIL_PICTURE_SOURCE_CONTAINER',this.value)"))." ".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_IMAGE_SOURCE_RANDOM")."</label>";
		$this->content .= "<label>".$this->ShowRadioField("WIZ_IBF_DETAIL_PICTURE_SOURCE","selected",Array("onclick"=>"WizIbfImageTypeChange('WIZ_IBF_DETAIL_PICTURE_SOURCE_CONTAINER',this.value)"))." ".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_IMAGE_SOURCE_SELECT")."</label>";
		
		$this->content .= "<div id=\"WIZ_IBF_DETAIL_PICTURE_SOURCE_CONTAINER_random\"".($wizard->GetVar("WIZ_IBF_DETAIL_PICTURE_SOURCE")!="random"?" style=\"display:none\"":"").">";
		$i=0;
		foreach($arImageFolders as $folder) {
			if($i>0) $this->content .= "<br />";
			$this->content .= $this->ShowCheckboxField("WIZ_IBF_DETAIL_PICTURE_SOURCE_RANDOM[]",$folder,Array("id"=>"WIZ_IBF_DETAIL_PICTURE_SOURCE_RANDOM_".$i))."<label for=\"WIZ_IBF_DETAIL_PICTURE_SOURCE_RANDOM_".$i."\"> ".$folder."</label>";
			$i++;
		}
		$this->content .= "</div>";

		$this->content .= "<div id=\"WIZ_IBF_DETAIL_PICTURE_SOURCE_CONTAINER_selected\"".($wizard->GetVar("WIZ_IBF_DETAIL_PICTURE_SOURCE")!="selected"?" style=\"display:none\"":"").">";

		$this->content .= $this->ShowInputField("text", "WIZ_IBF_DETAIL_PICTURE_SOURCE_SELECTED", Array("size" => "18","id"=>"WIZ_IBF_DETAIL_PICTURE_SOURCE_SELECTED"));

		$this->content .= "&nbsp;<a href=\"#\" onclick=\"window.wizibf_tmp_input_id='WIZ_IBF_DETAIL_PICTURE_SOURCE_SELECTED'; window._wizibf_OpenFM(); return false;\" title=\"".Loc::getMessage("GRAIN_FLOOD_IBF_FILE_SELECT_STRUCTURE")."\"><img class=\"wizibf-table-edit-file-icon\" src=\"/bitrix/images/fileman/medialib/tabs/server.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".Loc::getMessage("GRAIN_FLOOD_IBF_FILE_SELECT_STRUCTURE")."\" /></a>";

		$this->content .= "&nbsp;<a href=\"#\" onclick=\"window.wizibf_tmp_input_id='WIZ_IBF_DETAIL_PICTURE_SOURCE_SELECTED'; window._wizibf_OpenML(); return false;\" title=\"".Loc::getMessage("GRAIN_FLOOD_IBF_FILE_SELECT_MEDIALIB")."\"><img class=\"wizibf-table-edit-file-icon\" src=\"/bitrix/images/fileman/medialib/tabs/media.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".Loc::getMessage("GRAIN_FLOOD_IBF_FILE_SELECT_MEDIALIB")."\" /></a>";

		$this->content .= "</div>";
		
		$this->content .= "</td>";
		$this->content .= "</tr>";


		$this->content .= "</table>";

		// Properties

		// $this->content .= "<h3>".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_HEADER_PROPERTIES")."</h3>";

		// Parameters

		$this->content .= "<h3>".Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_HEADER_PARAMETERS")."</h3>";

		$this->content .= Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_STEP_LENGTH1");
		$this->content .= " ".$this->ShowInputField("text", "WIZ_IBF_STEP_LENGTH", Array("size" => "3","maxlength"=>"3"))." ";
		$this->content .= Loc::getMessage("GRAIN_FLOOD_IBF_STEP2_STEP_LENGTH2");
		$this->content .= "<br><br>";
	}

	function OnPostForm()
	{
		$wizard = &$this->GetWizard();
	
		global $DB;
	
		if ($wizard->IsNextButtonClick() || $wizard->IsFinishButtonClick())
		{

			$arErrors = Array();
			
			$LevelSectionsCount=Array();
			$LevelSectionsQuantity=Array();
			$LastLevelSectionsCount=1;
			$SectionsCount=0;
			
			for($i=1;$i<=4;$i++) {
				if(intval($wizard->GetVar("WIZ_IBF_SECTIONS_QUANTITY_LEVEL_".$i))>0) {
					$LevelSectionsQuantity[$i] = intval($wizard->GetVar("WIZ_IBF_SECTIONS_QUANTITY_LEVEL_".$i));
					$LastLevelSectionsCount = intval($wizard->GetVar("WIZ_IBF_SECTIONS_QUANTITY_LEVEL_".$i))*$LastLevelSectionsCount;
					$LevelSectionsCount[$i]=$LastLevelSectionsCount;
					$SectionsCount+=$LevelSectionsCount[$i];
				} else {
					$LevelSectionsCount[$i]=0;
					$LevelSectionsQuantity[$i]=0;
				}
			}
			
			$LevelElementsCount=Array();
			$LevelElementsQuantity=Array();
			$ElementsCount=0;
			
			for($i=1;$i<=5;$i++) {
				if(intval($wizard->GetVar("WIZ_IBF_ELEMENTS_QUANTITY_LEVEL_".$i))>0) {
					$LevelElementsQuantity[$i] = intval($wizard->GetVar("WIZ_IBF_ELEMENTS_QUANTITY_LEVEL_".$i));
					$LevelElementsCount[$i]=$LevelElementsQuantity[$i];
					if($i>1) $LevelElementsCount[$i] = $LevelElementsCount[$i]*$LevelSectionsCount[$i-1];
					$ElementsCount+=$LevelElementsCount[$i];
				} else {
					$LevelElementsCount[$i]=0;
					$LevelElementsQuantity[$i]=0;
				}
			}

			$not_empty=false;
			for($i=5;$i>=1;$i--) {
				if($not_empty) if($LevelSectionsQuantity[$i]<=0) $arErrors[] = Loc::getMessage('GRAIN_FLOOD_IBF_ERROR3',Array("#LEVEL#"=>$i));
				if($LevelElementsQuantity[$i]>0) $not_empty=true;
				if($i<5 && $LevelSectionsQuantity[$i]>0) $not_empty=true;
			}

			if ($SectionsCount>WIZ_IBFC_MAX_SECTIONS) $arErrors[] = Loc::getMessage('GRAIN_FLOOD_IBF_ERROR4',Array("#MAX#"=>WIZ_IBFC_MAX_SECTIONS));
			if ($ElementsCount>WIZ_IBFC_MAX_ELEMENTS) $arErrors[] = Loc::getMessage('GRAIN_FLOOD_IBF_ERROR5',Array("#MAX#"=>WIZ_IBFC_MAX_ELEMENTS));

			//$arErrors[] = "test|".print_r($LevelElementsQuantity,true);

			if(count($arErrors)>0) {
				$this->SetError(implode("<br />", $arErrors), "error1");
			} else {
			
				GFloodWizardTools::StoreParentSections(false);
				
				$wizard =& $this->GetWizard();
				$path = $wizard->package->path;

				GFloodWizardTools::StoreVars($wizard->GetVars(true));

			}
			
		}
	}

}


class Step3 extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(Loc::getMessage('GRAIN_FLOOD_IBF_STEP3_TITLE'));
		$this->SetNextStep("final");
		$this->SetPrevStep("step2");
		$this->SetStepID("step3");
	}

	function ShowStep()
	{
		unset($_SESSION["WIZ_IBF_POS"]);
		unset($_SESSION["WIZ_IBF_ERRORS"]);
		unset($_SESSION["WIZ_IBF_ADDED"]);
		unset($_SESSION["WIZ_IBF_ERROR_LOG"]);
	
		$wizard =& $this->GetWizard();
		$step_length = intval($wizard->GetVar('WIZ_IBF_STEP_LENGTH'));
		$path = $wizard->package->path;

		$this->content = "<style>";
		$this->content .= "table.wizibf-table-errors { width: 100% !important; border: 1px solid red; border-collapse: collapse }";
		$this->content .= "table.wizibf-table-errors td { padding: 5px; border: 1px solid red; color: red }";
		$this->content .= "#wiz_ibf_progress { height: 20px; width: 599px; }\n";				
		$this->content .= "#wiz_ibf_progress #wiz_ibf_pb { height: 20px; background-color: #E0E9EC; }\n";				
		$this->content .= "#wiz_ibf_progress #wiz_ibf_pb #wiz_ibf_pb_indicator { height: 20px; background-color: #B0BDC0; }\n";				
		$this->content .= "</style>";

		$this->content .= '<div style="padding: 20px;">';
		$this->content .= '<div id="wiz_ibf_progress"></div>';
		$this->content .= '<div id="wait_message" style="display: none;"></div>';
		$this->content .= '<div id="output"><br /></div>';
		$this->content .= '</div>';
		$this->content .= '<script type="text/javascript" src="/bitrix/js/main/cphttprequest.js"></script>';
		$this->content .= '<script type="text/javascript" src="'.$path.'/js/flood.js?'.time().'"></script>';
		$this->content .= '<script type="text/javascript">

var wiz_ibf_lang = "'.LANGUAGE_ID.'";
var nextButtonID = "'.$wizard->GetNextButtonID().'";
var prevButtonID = "'.$wizard->GetPrevButtonID().'";
var formID = "'.$wizard->GetFormName().'";
var ajaxMessages = {wait:\''.Loc::getMessage('GRAIN_FLOOD_IBF_STEP3_WAIT').'\'};
var path = "'.CUtil::JSEscape($path).'";
var step_length = "'.$step_length.'";
var sessid = "'.bitrix_sessid().'";

if (window.addEventListener) 
{
	window.addEventListener("load", wizibfFlood, false);
	window.addEventListener("load", DisableButton, false);
}
else if (window.attachEvent) 
{
	window.attachEvent("onload", wizibfFlood);
	window.attachEvent("onload", DisableButton);
}
</script>';
	}

}


class FinalStep extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(Loc::getMessage('GRAIN_FLOOD_IBF_FINALSTEP_TITLE'));
		$this->SetStepID("final");
		$this->SetCancelStep("final");
		$this->SetCancelCaption(Loc::getMessage('GRAIN_FLOOD_IBF_FINALSTEP_BUTTONTITLE'));
	}

	function ShowStep()
	{

		$this->content = Loc::getMessage('GRAIN_FLOOD_IBF_FINALSTEP_CONTENT');

	}
}

class CancelStep extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(Loc::getMessage('GRAIN_FLOOD_IBF_CANCELSTEP_TITLE'));
		$this->SetStepID("cancel");
		$this->SetCancelStep("cancel");
		$this->SetCancelCaption(Loc::getMessage('GRAIN_FLOOD_IBF_CANCELSTEP_BUTTONTITLE'));
	}

	function ShowStep()
	{
		$this->content = Loc::getMessage('GRAIN_FLOOD_IBF_CANCELSTEP_CONTENT');
	}
}
?>