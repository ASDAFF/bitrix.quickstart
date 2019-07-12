<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

CModule::IncludeModule("iblock");    

    $res = CIBlock::GetList(Array(), Array('CODE'=>array("parents_questions_".SITE_ID, "students_questions_".SITE_ID, "applications_for_admission_".SITE_ID), 'ACTIVE'=>'Y'), false); 
    $iblocks = Array();
	while($ar_res = $res->Fetch()){
		//if(in_array($ar_res['ID'], $IBlock_Event))continue;
		$iblocks[$ar_res['ID']] = $ar_res['NAME'];
	}

	foreach($iblocks as $key=>$iblock){
	
		$tp_code = 'SCHOOL_ELEMENT_ADD_IBLOCK'. $key;	// код шаблона

		$res = CIBlock::GetByID($key);		// название шаблона
		$res = $res->GetNext();
        
		//$tp_title = 'On #SITE_NAME# (#SERVER_NAME#) add element into "'. $res['NAME']. '"';
		$tp_title = GetMessage("W_IB_ADDEVENT_ONSITE") .' #SITE_NAME# (#SERVER_NAME#) '. GetMessage("W_IB_ADDEVENT_MESSAGE"). ' " '. $res['NAME']. '"';

		// определяем подстановки для шаблона
		$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$key));
		$tp_desc = "#NAME# - element name\n";

		$tp_desc2 = GetMessage("W_IB_ADDEVENT_NAME") . ": #NAME#\n";
		while ($prop_fields = $properties->GetNext()){
			$tp_desc .= '#'. $prop_fields['CODE']. '# - '. $prop_fields['NAME']. "\n";
			$tp_desc2 .= $prop_fields['NAME']. ': #'. $prop_fields['CODE']. "#\n";
		}
		
		$tp_desc .= "#SECTIONS# - element section\n";
		$tp_desc .= "#PREVIEW_TEXT# - anounce\n";
		$tp_desc .= "#DETAIL_TEXT# - detaile text\n";
		$tp_desc .= "#DIRECT_LINK# - adminstrative edit link\n";
		
		$et = new CEventType;
		$rsLang = CLanguage::GetList();
		while ($arLang = $rsLang->Fetch()){
			$et->Add(array(
				"SITE_ID"       => $arLang['LID'], 
				"EVENT_NAME"    => $tp_code,
				"NAME"          => $tp_title, 
				"DESCRIPTION"   => $tp_desc 
				)
			);
		}

		$arr["ACTIVE"] = "Y";
		$arr["EVENT_NAME"] = $tp_code; 
		$arr["LID"] = array($res['LID']); 
		$arr["EMAIL_FROM"] = "#DEFAULT_EMAIL_FROM#";
		$arr["EMAIL_TO"] = "#DEFAULT_EMAIL_FROM#";
		$arr["BCC"] = "#BCC#";
		$arr["SUBJECT"] = $tp_title;
		$arr["BODY_TYPE"] = "text"; 
		$arr["MESSAGE"] = "On #SITE_NAME# (#SERVER_NAME#) created new elements\n";
		$arr["MESSAGE"] .= $tp_desc2;
		$emess = new CEventMessage; 
		$emess->Add($arr);
	}
?>
