<?
define("STOP_STATISTICS", true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/wizard.php");

use \Bitrix\Main\Localization\Loc;

$wizard =  new CWizard("grain:iblock.flooding");
$wizard->IncludeWizardLang("scripts/flood.php", $lang);

$lang=$_REQUEST["WIZ_IBF_LANG"];

$wizard_path = $_SERVER["DOCUMENT_ROOT"].($wizard->GetPath());
$spath=pathinfo(__FILE__);

Loc::loadMessages(__FILE__);

$mainModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($mainModulePermissions < "W")
{
	echo Loc::getMessage('GRAIN_FLOOD_IBF_FLOOD_ERROR_ACCESS_DENIED');
	require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
	die();
}


define("WIZ_IBF_VARS_DIR",($wizard->GetPath())."/temp/");

require_once($_SERVER["DOCUMENT_ROOT"].$wizard->GetPath()."/scripts/functions.php");

$VARS = GFloodWizardTools::GetVars();

if (
	!is_array($VARS) 
	|| (is_array($VARS) && count($VARS)<=0) 
	|| intval($VARS["WIZ_IBF_IBLOCK_ID"])<=0 
	|| strlen($VARS["WIZ_IBF_DATA_DIR"])<=0
	|| strlen($VARS["WIZ_IBF_VARS_DIR"])<=0
)
{
	echo "Error while parsing vars temp file";
	require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
	die();
}

define("WIZ_IBF_DATA_DIR", $VARS["WIZ_IBF_DATA_DIR"]);
define("WIZ_IBF_VARS_DIR", $VARS["WIZ_IBF_VARS_DIR"]);

$step_length = intval($_REQUEST["STEP_LENGTH"]);
if ($step_length <= 0) $step_length = 10;

$STEP = intval($_REQUEST['STEP']);

$MAIN_PARENT_SECTION_ID = intval($VARS["WIZ_IBF_PARENT_SECTION"]);
if($MAIN_PARENT_SECTION_ID>0) {
	if(CModule::IncludeModule("iblock")) {
		$rsParentSection = CIBlockSection::GetByID($MAIN_PARENT_SECTION_ID);
		$MAIN_PARENT_SECTION_ID = 0;
		$MAIN_PARENT_SECTION_DEPTH_LEVEL = 0;
		if($MAIN_PARENT_SECTION = $rsParentSection->GetNext()) {
			$MAIN_PARENT_SECTION_ID = $MAIN_PARENT_SECTION["ID"];
			$MAIN_PARENT_SECTION_DEPTH_LEVEL = $MAIN_PARENT_SECTION["DEPTH_LEVEL"];
		}
	}
} else {
	$MAIN_PARENT_SECTION_DEPTH_LEVEL = 0;
}


$LevelSectionsCount=Array();
$LevelSectionsQuantity=Array();
$LastLevelSectionsCount=1;
$SectionsCount=0;

for($i=1;$i<=4;$i++) {
	if(intval($VARS["WIZ_IBF_SECTIONS_QUANTITY_LEVEL_".$i])>0) {
		$LevelSectionsQuantity[$i] = intval($VARS["WIZ_IBF_SECTIONS_QUANTITY_LEVEL_".$i]);
		$LastLevelSectionsCount = intval($VARS["WIZ_IBF_SECTIONS_QUANTITY_LEVEL_".$i])*$LastLevelSectionsCount;
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
	if(intval($VARS["WIZ_IBF_ELEMENTS_QUANTITY_LEVEL_".$i])>0) {
		$LevelElementsQuantity[$i] = intval($VARS["WIZ_IBF_ELEMENTS_QUANTITY_LEVEL_".$i]);
		$LevelElementsCount[$i]=$LevelElementsQuantity[$i];
		if($i>1) $LevelElementsCount[$i] = $LevelElementsCount[$i]*$LevelSectionsCount[$i-1];
		$ElementsCount+=$LevelElementsCount[$i];
	} else {
		$LevelElementsCount[$i]=0;
		$LevelElementsQuantity[$i]=0;
	}
}



switch($STEP)
{

    case 0:
		if($SectionsCount>0) {
	    	echo Loc::getMessage('GRAIN_FLOOD_IBF_FLOOD_FLOODING_SECTIONS');
	    	echo "<script>wizibfFlood(1)</script>";
	    } elseif($ElementsCount>0) {
	    	echo Loc::getMessage('GRAIN_FLOOD_IBF_FLOOD_FLOODING_ELEMENTS');
	    	echo "<script>wizibfFlood(2)</script>";
	    } else {
	    	echo "<script>wizibfFlood(3)</script>";
	    }
    break;

    case 1: // create sections
    
    	CModule::IncludeModule("iblock");
    
    	$time_limit = ini_get('max_execution_time');
    	if ($time_limit < $step_length) set_time_limit($step_length + 5);
    	$start_time = time();
    	$finish_time = $start_time + $step_length;

		$bFinish = true;
	
		$cur_step = 0;
		$errors = 0;
		$added = 0;

   		if (is_set($_SESSION["WIZ_IBF_POS"])) $cur_step = $_SESSION["WIZ_IBF_POS"];
   		if (is_set($_SESSION["WIZ_IBF_ERRORS"])) $errors = $_SESSION["WIZ_IBF_ERRORS"];
   		if (is_set($_SESSION["WIZ_IBF_ADDED"])) $added = $_SESSION["WIZ_IBF_ADDED"];

   		$test_cur_step = 0;

		GFloodWizardTools::GetParentSections($arCurParentSections);

    	while ($cur_step<$SectionsCount)
    	{

			// Detect parent section

			$cur_level_step = $cur_step;
			$cur_level = 0;
	
			for($i=1;$i<=4;$i++) {
				if($cur_level_step>=$LevelSectionsCount[$i]) {
					$cur_level_step -= $LevelSectionsCount[$i];
				} else {
					$cur_level = $i;
					break;
				}
			}

			if($cur_level>1) {
				$parent_section_index = floor($cur_level_step/$LevelSectionsQuantity[$cur_level]);
				$parent_section_id = $arCurParentSections[$cur_level-1][$parent_section_index];
			} else $parent_section_id = $MAIN_PARENT_SECTION_ID;

			/*
			echo "\$cur_step=".$cur_step."<br />";
			echo "\$cur_level_step=".$cur_level_step."<br />";
			echo "\$cur_level=".$cur_level."<br />";
			echo "count(\$arCurParentSections[\$cur_level-1])=".count($arCurParentSections[$cur_level-1])."<br />";
			echo "count(\$arCurParentSections[\$cur_level])=".count($arCurParentSections[$cur_level])."<br />";
			echo "\$parent_section_index=".$parent_section_index."<br /><br />";			
			*/
			
			// Adding section

			$bs = new CIBlockSection;

			$section_name = $VARS["WIZ_IBF_SECTION_NAME"];
			if($VARS["WIZ_IBF_NAME_ADD_NUMBERS"]=="Y") $section_name .= " ".($cur_step+1);
			if($VARS["WIZ_IBF_SECTION_NAME_ADD_DEPTH_LEVEL"]=="Y") $section_name .= " ".GetMessage("GRAIN_FLOOD_IBF_FLOOD_DEPTH_LEVEL")." ".($cur_level+$MAIN_PARENT_SECTION_DEPTH_LEVEL);
			
			$section_code = $VARS["WIZ_IBF_SECTION_CODE"];
			if($VARS["WIZ_IBF_CODE_ADD_NUMBERS"]=="Y") $section_code .= "-".($cur_step+1);
			if($VARS["WIZ_IBF_SECTION_CODE_ADD_DEPTH_LEVEL"]=="Y") $section_code .= "-level-".($cur_level+$MAIN_PARENT_SECTION_DEPTH_LEVEL);

			$section_code_rand = $section_code;
			if($VARS["WIZ_IBF_SECTION_CODE_ADD_ID"]=="Y") 
				$section_code_rand .= "-".RandString(20);

			$arSectionFields = Array(
				"IBLOCK_SECTION_ID" => $parent_section_id,
				"IBLOCK_ID"      => $VARS["WIZ_IBF_IBLOCK_ID"],
				"NAME"           => $section_name,
				"CODE"           => $section_code_rand,
				"ACTIVE"         => "Y",
				"DESCRIPTION"    => "Текст описания раздела",
				"DESCRIPTION_TYPE" => "text",
			);

			if($SECTION_ID = $bs->Add($arSectionFields,true,true)) {
			
				$added++;
			
				$arCurParentSections[$cur_level][] = $SECTION_ID;
			
				if(
					$VARS["WIZ_IBF_SECTION_NAME_ADD_ID"]=="Y"
					|| $VARS["WIZ_IBF_SECTION_CODE_ADD_ID"]=="Y"
				) {
				
					$bs = new CIBlockSection;

					if($VARS["WIZ_IBF_SECTION_NAME_ADD_ID"]=="Y") $section_name .= " ID:".$SECTION_ID;
					if($VARS["WIZ_IBF_SECTION_CODE_ADD_ID"]=="Y") $section_code .= "-id-".$SECTION_ID;

					$arSectionFields = Array(
						"NAME"           => $section_name,
						"CODE"           => $section_code,
					);

					if($bs->Update($SECTION_ID, $arSectionFields)) {
					
					
					} else {

						$errors++;
						GFloodWizardTools::AddErrorToLog($bs->LAST_ERROR);
					
					}
				
				}
			
			
			} else {
			
				$errors++;
				GFloodWizardTools::AddErrorToLog($bs->LAST_ERROR);
			
			}


			// Go to next step when time exceed

			$test_cur_step++;

    		$cur_step++;
    		
    		$cur_time = time();
    		
    		if ($cur_time >= $finish_time || $test_cur_step>50)
    		{
    			
    			$_SESSION["WIZ_IBF_POS"] = $cur_step;
    			$_SESSION["WIZ_IBF_ERRORS"] = $errors;
    			$_SESSION["WIZ_IBF_ADDED"] = $added;

    			$bFinish = false;
    			
				GFloodWizardTools::StoreParentSections($arCurParentSections);
    			
    			echo "<script>wizibfFlood(1, {AMOUNT:".CUtil::JSEscape($ElementsCount).",POS:".CUtil::JSEscape($cur_step).(intval($errors)>0?",ERROR_MESSAGE:'".CUtil::JSEscape(GetMessage("GRAIN_FLOOD_IBF_FLOOD_ERRORS_PROGRESSBAR").": ".$errors)."'":"")."});</script>";
    			
    			break;
    		}						
		}


    	if ($bFinish)
    	{
    		$strOK = Loc::getMessage('GRAIN_FLOOD_IBF_FLOOD_STATS_SECTIONS',Array("#NUMSECTIONS#"=>$added));

			if(intval($errors)>0) {

				$strOK .= "<table class='wizibf-table-errors'>";
				
				$arLog = GFloodWizardTools::GetErrorLog();
			
				foreach($arLog as $arError) {

					$strOK .= "<tr>";
					$strOK .= "<td>".$arError["ERROR"]."</td>";
					$strOK .= "<td>".$arError["COUNT"]."</td>";
					$strOK .= "</tr>";
				
				}

				$strOK .= "</table><br />";
			
			}

    		unset($_SESSION["WIZ_IBF_POS"]);
    		unset($_SESSION["WIZ_IBF_ERRORS"]);
    		unset($_SESSION["WIZ_IBF_ADDED"]); 
			unset($_SESSION["WIZ_IBF_ERROR_LOG"]);
   		
    		echo $strOK;

		    if($ElementsCount>0) {
		    	echo Loc::getMessage('GRAIN_FLOOD_IBF_FLOOD_FLOODING_ELEMENTS');
				GFloodWizardTools::StoreParentSections($arCurParentSections);
		    	echo "<script>wizibfFlood(2)</script>";
		    } else {
				GFloodWizardTools::StoreParentSections(false);
		    	echo "<script>wizibfFlood(3)</script>";
		    }

    	} 
    	
    break;

    case 2: // create elements
    
    	CModule::IncludeModule("iblock");
    
    	$time_limit = ini_get('max_execution_time');
    	if ($time_limit < $step_length) set_time_limit($step_length + 5);
    	$start_time = time();
    	$finish_time = $start_time + $step_length;

		$bFinish = true;
	
		$cur_step = 0;
		$errors = 0;
		$added = 0;

   		if (is_set($_SESSION["WIZ_IBF_POS"])) $cur_step = $_SESSION["WIZ_IBF_POS"];
   		if (is_set($_SESSION["WIZ_IBF_ERRORS"])) $errors = $_SESSION["WIZ_IBF_ERRORS"];
   		if (is_set($_SESSION["WIZ_IBF_ADDED"])) $added = $_SESSION["WIZ_IBF_ADDED"];

		GFloodWizardTools::GetParentSections($arCurParentSections);
   		
   		$test_cur_step = 0;
			
    	while ($cur_step<$ElementsCount)
    	{

			// Detect parent section

			$cur_level_step = $cur_step;
			$cur_level = 1;

			for($i=1;$i<=5;$i++) {
				if($cur_level_step>=$LevelElementsCount[$i]) {
					$cur_level_step -= $LevelElementsCount[$i];
				} else {
					$cur_level = $i;
					break;
				}
			}

			if($cur_level>1) {
				$parent_section_index = floor($cur_level_step/$LevelElementsQuantity[$cur_level]);
				$parent_section_id = $arCurParentSections[$cur_level-1][$parent_section_index];
			} else $parent_section_id = $MAIN_PARENT_SECTION_ID;

			/*
			echo "\$cur_step=".$cur_step."<br />";
			echo "\$cur_level_step=".$cur_level_step."<br />";
			echo "\$cur_level=".$cur_level."<br />";
			echo "count(\$arCurParentSections[\$cur_level-1])=".count($arCurParentSections[$cur_level-1])."<br />";
			echo "count(\$arCurParentSections[\$cur_level])=".count($arCurParentSections[$cur_level])."<br />";
			echo "\$parent_section_index=".$parent_section_index."<br /><br />";			
			*/


			// Adding element

			GFloodWizardTools::GetRandomParagraph("",0,true); // renew only

			$el = new CIBlockElement;

			$element_name = "";
			if($VARS["WIZ_IBF_NAME_SOURCE"]=="custom")
				$element_name = $VARS["WIZ_IBF_NAME"];
			elseif(!!$VARS["WIZ_IBF_NAME_SOURCE"] && $VARS["WIZ_IBF_NAME_SOURCE"]!=="none")
				$element_name = GFloodWizardTools::GetRandomParagraph($VARS["WIZ_IBF_NAME_SOURCE"],$VARS["WIZ_IBF_NAME_CLAUSE_COUNT"]);
			
			if($VARS["WIZ_IBF_NAME_ADD_NUMBERS"]=="Y") $element_name .= " ".($cur_step+1);
			
			$element_code = $VARS["WIZ_IBF_CODE"];
			if($VARS["WIZ_IBF_CODE_ADD_NUMBERS"]=="Y") $element_code .= "-".($cur_step+1);

			$element_code_rand = $element_code;
			if($VARS["WIZ_IBF_CODE_ADD_ID"]=="Y" || $VARS["WIZ_IBF_CODE_ADD_SECTION_NAME"]=="Y"	|| $VARS["WIZ_IBF_CODE_ADD_SECTION_ID"]=="Y") 
				$element_code_rand .= "-".RandString(20);
			
			$ACTIVE_FROM = new \Bitrix\Main\Type\DateTime($VARS["WIZ_IBF_ACTIVE_FROM"]);
			$ACTIVE_FROM->add((intval($VARS["WIZ_IBF_ACTIVE_FROM_ADD_SECONDS"])*$cur_step)." seconds");
			
			$PREVIEW_TEXT = "";
			if($VARS["WIZ_IBF_PREVIEW_TEXT_SOURCE"]=="custom")
				$PREVIEW_TEXT = $VARS["WIZ_IBF_DETAIL_TEXT"];
			elseif(!!$VARS["WIZ_IBF_PREVIEW_TEXT_SOURCE"] && $VARS["WIZ_IBF_PREVIEW_TEXT_SOURCE"]!=="none")
				$PREVIEW_TEXT = GFloodWizardTools::GetRandomParagraph($VARS["WIZ_IBF_PREVIEW_TEXT_SOURCE"],$VARS["WIZ_IBF_PREVIEW_TEXT_CLAUSE_COUNT"]);

			$DETAIL_TEXT = "";
			if($VARS["WIZ_IBF_DETAIL_TEXT_SOURCE"]=="custom")
				$DETAIL_TEXT = $VARS["WIZ_IBF_PREVIEW_TEXT"];
			elseif(!!$VARS["WIZ_IBF_DETAIL_TEXT_SOURCE"] && $VARS["WIZ_IBF_DETAIL_TEXT_SOURCE"]!=="none")
				$DETAIL_TEXT = GFloodWizardTools::GetRandomParagraph($VARS["WIZ_IBF_DETAIL_TEXT_SOURCE"],$VARS["WIZ_IBF_DETAIL_TEXT_CLAUSE_COUNT"]);
			
			$arElementFields = Array(
				"IBLOCK_SECTION_ID" => $parent_section_id,
				"IBLOCK_ID"      => $VARS["WIZ_IBF_IBLOCK_ID"],
				"NAME"           => $element_name,
				"CODE"           => $element_code_rand,
				"ACTIVE"         => "Y",
				"PREVIEW_TEXT"   => $PREVIEW_TEXT,
				"PREVIEW_TEXT_TYPE" => "text",
				"DETAIL_TEXT"    => $DETAIL_TEXT,
				"DETAIL_TEXT_TYPE" => "text",
			);
			if(!!$VARS["WIZ_IBF_ACTIVE_FROM"])
				$arElementFields["ACTIVE_FROM"] = $ACTIVE_FROM;

			$preview_tmp_image_file = false;

			if($VARS["WIZ_IBF_PREVIEW_PICTURE_SOURCE"]=="random" && count($VARS["WIZ_IBF_PREVIEW_PICTURE_SOURCE_RANDOM"])>0) {
			
				$random_image_file = GFloodWizardTools::GetRandomImage($VARS["WIZ_IBF_PREVIEW_PICTURE_SOURCE_RANDOM"]);
				if($random_image_file!==false && strlen($random_image_file)>0) {
					$pathinfo = pathinfo($random_image_file);
					$preview_tmp_image_file = WIZ_IBF_VARS_DIR."images/preview/".$pathinfo["basename"];
					copy($_SERVER["DOCUMENT_ROOT"].$random_image_file,$_SERVER["DOCUMENT_ROOT"].$preview_tmp_image_file);
					$arElementFields["PREVIEW_PICTURE"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$preview_tmp_image_file);
				}
				
			} elseif($VARS["WIZ_IBF_PREVIEW_PICTURE_SOURCE"]=="selected" && strlen($VARS["WIZ_IBF_PREVIEW_PICTURE_SOURCE_SELECTED"])>0) {
			
				$pathinfo = pathinfo($VARS["WIZ_IBF_PREVIEW_PICTURE_SOURCE_SELECTED"]);
				$preview_tmp_image_file = WIZ_IBF_VARS_DIR."images/preview/".$pathinfo["basename"];
				copy($_SERVER["DOCUMENT_ROOT"].$VARS["WIZ_IBF_PREVIEW_PICTURE_SOURCE_SELECTED"],$_SERVER["DOCUMENT_ROOT"].$preview_tmp_image_file);
				$arElementFields["PREVIEW_PICTURE"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$preview_tmp_image_file);
			
			}

			$detail_tmp_image_file = false;

			if($VARS["WIZ_IBF_DETAIL_PICTURE_SOURCE"]=="random" && count($VARS["WIZ_IBF_DETAIL_PICTURE_SOURCE_RANDOM"])>0) {
			
				$random_image_file = GFloodWizardTools::GetRandomImage($VARS["WIZ_IBF_DETAIL_PICTURE_SOURCE_RANDOM"]);
				if($random_image_file!==false && strlen($random_image_file)>0) {
					$pathinfo = pathinfo($random_image_file);
					$detail_tmp_image_file = WIZ_IBF_VARS_DIR."images/detail/".$pathinfo["basename"];
					copy($_SERVER["DOCUMENT_ROOT"].$random_image_file,$_SERVER["DOCUMENT_ROOT"].$detail_tmp_image_file);
					$arElementFields["DETAIL_PICTURE"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$detail_tmp_image_file);
				}
				
			} elseif($VARS["WIZ_IBF_DETAIL_PICTURE_SOURCE"]=="selected" && strlen($VARS["WIZ_IBF_DETAIL_PICTURE_SOURCE_SELECTED"])>0) {

				$pathinfo = pathinfo($VARS["WIZ_IBF_DETAIL_PICTURE_SOURCE_SELECTED"]);
				$detail_tmp_image_file = WIZ_IBF_VARS_DIR."images/preview/".$pathinfo["basename"];
				copy($_SERVER["DOCUMENT_ROOT"].$VARS["WIZ_IBF_DETAIL_PICTURE_SOURCE_SELECTED"],$_SERVER["DOCUMENT_ROOT"].$detail_tmp_image_file);
				$arElementFields["DETAIL_PICTURE"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$detail_tmp_image_file);
			
			}

			//$PROP = array();
			//$PROP[12] = "Белый";  // свойству с кодом 12 присваиваем значение "Белый"
			//$PROP[3] = 38;        // свойству с кодом 3 присваиваем значение 38

			//$arElementFields["PROPERTY_VALUES"] = $PROP;
			
			if($ELEMENT_ID = $el->Add($arElementFields,false,true,true)) {
			
				$added++;
			
				if(!!$preview_tmp_image_file)
					unlink($_SERVER["DOCUMENT_ROOT"].$preview_tmp_image_file);

				if(!!$detail_tmp_image_file)
					unlink($_SERVER["DOCUMENT_ROOT"].$detail_tmp_image_file);
			
				if(
					$VARS["WIZ_IBF_NAME_ADD_ID"]=="Y"
					|| $VARS["WIZ_IBF_NAME_ADD_SECTION_NAME"]=="Y"
					|| $VARS["WIZ_IBF_NAME_ADD_SECTION_ID"]=="Y"
					|| $VARS["WIZ_IBF_CODE_ADD_ID"]=="Y"
					|| $VARS["WIZ_IBF_CODE_ADD_SECTION_CODE"]=="Y"
					|| $VARS["WIZ_IBF_CODE_ADD_SECTION_ID"]=="Y"
				) {
				
					$el = new CIBlockElement;

					if($VARS["WIZ_IBF_NAME_ADD_ID"]=="Y") $element_name .= " ID:".$ELEMENT_ID;
					if($VARS["WIZ_IBF_CODE_ADD_ID"]=="Y") $element_code .= "-id-".$ELEMENT_ID;

					if(
						(
							$VARS["WIZ_IBF_NAME_ADD_SECTION_NAME"]=="Y"
							|| $VARS["WIZ_IBF_NAME_ADD_SECTION_ID"]=="Y"
							|| $VARS["WIZ_IBF_CODE_ADD_SECTION_CODE"]=="Y"
							|| $VARS["WIZ_IBF_CODE_ADD_SECTION_ID"]=="Y"
						)
						&& intval($parent_section_id)>0
					) {
					
						$rsSection = CIBlockSection::GetByID($parent_section_id);
						
						if($arSection=$rsSection->Fetch()) {
							if($VARS["WIZ_IBF_NAME_ADD_SECTION_NAME"]=="Y") $element_name .= " ".GetMessage("GRAIN_FLOOD_IBF_FLOOD_IN_SECTION")." ".$arSection["NAME"];
							if($VARS["WIZ_IBF_NAME_ADD_SECTION_ID"]=="Y") $element_name .= " SECTION_ID:".$arSection["ID"];
							if($VARS["WIZ_IBF_CODE_ADD_SECTION_CODE"]=="Y") $element_code .= "-section-".$arSection["CODE"];
							if($VARS["WIZ_IBF_CODE_ADD_SECTION_ID"]=="Y") $element_code .= "-section-id-".$arSection["ID"];
						}
					
					}

					$arElementFields = Array(
						"NAME"           => $element_name,
						"CODE"           => $element_code,
					);

					if($el->Update($ELEMENT_ID, $arElementFields)) {
					
					
					} else {

						$errors++;
						GFloodWizardTools::AddErrorToLog($el->LAST_ERROR);
					
					}
				
				}

			
			} else {
			
				$errors++;
				GFloodWizardTools::AddErrorToLog($el->LAST_ERROR);
			
			}


			// Go to next step when time exceed

			$test_cur_step++;

    		$cur_step++;
    		
    		
    		$cur_time = time();
    		
    		if ($cur_time >= $finish_time || $test_cur_step>50)
    		{
    			
    			$_SESSION["WIZ_IBF_POS"] = $cur_step;
    			$_SESSION["WIZ_IBF_ERRORS"] = $errors;
    			$_SESSION["WIZ_IBF_ADDED"] = $added;
 			
    			$bFinish = false;
    			
    			echo "<script>wizibfFlood(2, {AMOUNT:".CUtil::JSEscape($ElementsCount).",POS:".CUtil::JSEscape($cur_step).(intval($errors)>0?",ERROR_MESSAGE:'".CUtil::JSEscape(GetMessage("GRAIN_FLOOD_IBF_FLOOD_ERRORS_PROGRESSBAR").": ".$errors)."'":"")."});</script>";
    			
    			break;
    		}						
		}

    	if ($bFinish)
    	{
    		$strOK = Loc::getMessage('GRAIN_FLOOD_IBF_FLOOD_STATS_ELEMENTS',Array("#NUMELEMENTS#"=>$added));

			if(intval($errors)>0) {

				$strOK .= "<table class='wizibf-table-errors'>";
				
				$arLog = GFloodWizardTools::GetErrorLog();
			
				foreach($arLog as $arError) {

					$strOK .= "<tr>";
					$strOK .= "<td>".$arError["ERROR"]."</td>";
					$strOK .= "<td>".$arError["COUNT"]."</td>";
					$strOK .= "</tr>";
				
				}

				$strOK .= "</table><br />";
			
			}

    		unset($_SESSION["WIZ_IBF_POS"]);
    		unset($_SESSION["WIZ_IBF_ERRORS"]);
    		unset($_SESSION["WIZ_IBF_ADDED"]);
			unset($_SESSION["WIZ_IBF_ERROR_LOG"]);
   		
    		echo $strOK;
			GFloodWizardTools::StoreParentSections(false);
    		echo '<script>wizibfFlood(3);</script>';
    	} 
    	
    
    break;
    
    case 3:
    	echo Loc::getMessage('GRAIN_FLOOD_IBF_FLOOD_ALL_DONE');
    	echo '<script>EnableButton();</script>';
    break;
}


require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
?>