<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(CModule::IncludeModule("form")){
	$form_sid = "orderform_".WIZARD_SITE_ID;
	$rsForm = CForm::GetBySID($form_sid);
	if(!$arForm = $rsForm->Fetch()){
		$arFields = array(
			"NAME"              => GetMessage("ORDERFORM_TITLE"),
			"SID"               => $form_sid,
			"C_SORT"            => 10,
			"BUTTON"            => GetMessage("ORDERFORM_BUTTON_TEXT"),
			"DESCRIPTION"       => "",
			"DESCRIPTION_TYPE"  => "text",
			"STAT_EVENT1"       => "form",
			"STAT_EVENT2"       => "visitor_form",
			"arSITE"            => array(WIZARD_SITE_ID),
			"arMENU"			=> array("ru" => GetMessage("ORDERFORM_RU_MENU_TITLE").WIZARD_SITE_ID, "en" => GetMessage("ORDERFORM_EN_MENU_TITLE").WIZARD_SITE_ID)
	    );
		$NEW_ID = CForm::Set($arFields);
		if($NEW_ID>0){
			
			$arTemplates = CForm::SetMailTemplate($NEW_ID);
			CForm::Set(array("arMAIL_TEMPLATE" => $arTemplates), $NEW_ID);
			$arFields = array(
				"FORM_ID"             => $NEW_ID,
			    "C_SORT"              => 100,
			    "ACTIVE"              => "Y",
			    "TITLE"               => GetMessage("ORDERFORM_STATUS_TITLE"),
			    "DESCRIPTION"         => GetMessage("ORDERFORM_STATUS_DESCRIPTION"),
			    "CSS"                 => "statusgreen",
			    "HANDLER_OUT"         => "",
			    "HANDLER_IN"          => "",
			    "DEFAULT_VALUE"       => "Y",
			    "arPERMISSION_VIEW"   => array(2),
			    "arPERMISSION_MOVE"   => array(2),
			    "arPERMISSION_EDIT"   => array(),
			    "arPERMISSION_DELETE" => array(),
			);
			$STATUS_ID = CFormStatus::Set($arFields);
	
			//	контактное лицо
			$arAnswer = array();
			$arAnswer[] = array(
				"ACTIVE" => "Y",
				"C_SORT" => 1,
				"MESSAGE" => " ",
				"VALUE" => "",
				"FIELD_TYPE" => "text",
				"FIELD_WIDTH" => "38"
			);
			$arFields = array(
				"FORM_ID" => $NEW_ID,                   
				"ACTIVE" => "Y",
				"TITLE" => GetMessage("ORDERFORM_Q1_TITLE"),
				"TITLE_TYPE" => "text",
				"SID" => "ORDERFORM_Q1",
				"COMMENTS" => GetMessage("ORDERFORM_Q1_COMMENT"),			   
				"C_SORT" => 100,
				"ADDITIONAL" => "N",
				"REQUIRED" => "Y",
				"IN_RESULTS_TABLE" => "Y",
				"IN_EXCEL_TABLE" => "Y",
				"arFILTER_ANSWER_TEXT" => array("text"),
				"arANSWER" => $arAnswer,
			);
			$Q_ID = CFormField::Set($arFields);
			
			//	Название компании
			$arAnswer = array();
			$arAnswer[] = array(
				"ACTIVE" => "Y",
				"C_SORT" => 1,
				"MESSAGE" => " ",
				"VALUE" => "",
				"FIELD_TYPE" => "text",
				"FIELD_WIDTH" => "38"
			);
			$arFields = array(
				"FORM_ID" => $NEW_ID,                   
				"ACTIVE" => "Y",
				"TITLE" => GetMessage("ORDERFORM_Q2_TITLE"),
				"TITLE_TYPE" => "text",
				"SID" => "ORDERFORM_Q2",
				"COMMENTS" => GetMessage("ORDERFORM_Q1_COMMENT"),			   
				"C_SORT" => 200,
				"ADDITIONAL" => "N",
				"REQUIRED" => "N",
				"IN_RESULTS_TABLE" => "Y",
				"IN_EXCEL_TABLE" => "Y",
				"arFILTER_ANSWER_TEXT" => array("text"),
				"arANSWER" => $arAnswer,
			);
			$Q_ID = CFormField::Set($arFields);
			
			//	Телефон
			$arAnswer = array();
			$arAnswer[] = array(
				"ACTIVE" => "Y",
				"C_SORT" => 1,
				"MESSAGE" => " ",
				"VALUE" => "",
				"FIELD_TYPE" => "text",
				"FIELD_WIDTH" => "38"
			);
			$arFields = array(
				"FORM_ID" => $NEW_ID,                   
				"ACTIVE" => "Y",
				"TITLE" => GetMessage("ORDERFORM_Q3_TITLE"),
				"TITLE_TYPE" => "text",
				"SID" => "ORDERFORM_Q3",
				"COMMENTS" => GetMessage("ORDERFORM_Q1_COMMENT"),			   
				"C_SORT" => 300,
				"ADDITIONAL" => "N",
				"REQUIRED" => "Y",
				"IN_RESULTS_TABLE" => "Y",
				"IN_EXCEL_TABLE" => "Y",
				"arFILTER_ANSWER_TEXT" => array("text"),
				"arANSWER" => $arAnswer,
			);
			$Q_ID = CFormField::Set($arFields);
			
			//	Факс
			$arAnswer = array();
			$arAnswer[] = array(
				"ACTIVE" => "Y",
				"C_SORT" => 1,
				"MESSAGE" => " ",
				"VALUE" => "",
				"FIELD_TYPE" => "text",
				"FIELD_WIDTH" => "38"
			);
			$arFields = array(
				"FORM_ID" => $NEW_ID,                   
				"ACTIVE" => "Y",
				"TITLE" => GetMessage("ORDERFORM_Q4_TITLE"),
				"TITLE_TYPE" => "text",
				"SID" => "ORDERFORM_Q4",
				"COMMENTS" => GetMessage("ORDERFORM_Q1_COMMENT"),			   
				"C_SORT" => 400,
				"ADDITIONAL" => "N",
				"REQUIRED" => "N",
				"IN_RESULTS_TABLE" => "Y",
				"IN_EXCEL_TABLE" => "Y",
				"arFILTER_ANSWER_TEXT" => array("text"),
				"arANSWER" => $arAnswer,
			);
			$Q_ID = CFormField::Set($arFields);
			
			//	email
			$arAnswer = array();
			$arAnswer[] = array(
				"ACTIVE" => "Y",
				"C_SORT" => 1,
				"MESSAGE" => " ",
				"VALUE" => "",
				"FIELD_TYPE" => "email",
				"FIELD_WIDTH" => "38"
			);
			$arFields = array(
				"FORM_ID" => $NEW_ID,                   
				"ACTIVE" => "Y",
				"TITLE" => GetMessage("ORDERFORM_Q5_TITLE"),
				"TITLE_TYPE" => "text",
				"SID" => "ORDERFORM_Q5",
				"COMMENTS" => GetMessage("ORDERFORM_Q1_COMMENT"),			   
				"C_SORT" => 500,
				"ADDITIONAL" => "N",
				"REQUIRED" => "N",
				"IN_RESULTS_TABLE" => "Y",
				"IN_EXCEL_TABLE" => "Y",
				"arFILTER_ANSWER_TEXT" => array("text"),
				"arANSWER" => $arAnswer,
			);
			$Q_ID = CFormField::Set($arFields);
			
			//	Почтовый адрес
			$arAnswer = array();
			$arAnswer[] = array(
				"ACTIVE" => "Y",
				"C_SORT" => 1,
				"MESSAGE" => " ",
				"VALUE" => "",
				"FIELD_TYPE" => "textarea",
				"FIELD_WIDTH" => "30",
				"FIELD_HEIGHT" => "3"
			);
			$arFields = array(
				"FORM_ID" => $NEW_ID,                   
				"ACTIVE" => "Y",
				"TITLE" => GetMessage("ORDERFORM_Q6_TITLE"),
				"TITLE_TYPE" => "text",
				"SID" => "ORDERFORM_Q6",
				"COMMENTS" => GetMessage("ORDERFORM_Q1_COMMENT"),			   
				"C_SORT" => 600,
				"ADDITIONAL" => "N",
				"REQUIRED" => "N",
				"IN_RESULTS_TABLE" => "Y",
				"IN_EXCEL_TABLE" => "Y",
				"arFILTER_ANSWER_TEXT" => array("text"),
				"arANSWER" => $arAnswer,
			);
			$Q_ID = CFormField::Set($arFields);
			
			//	Юридический адрес
			$arAnswer = array();
			$arAnswer[] = array(
				"ACTIVE" => "Y",
				"C_SORT" => 1,
				"MESSAGE" => " ",
				"VALUE" => "",
				"FIELD_TYPE" => "textarea",
				"FIELD_WIDTH" => "30",
				"FIELD_HEIGHT" => "3"
			);
			$arFields = array(
				"FORM_ID" => $NEW_ID,                   
				"ACTIVE" => "Y",
				"TITLE" => GetMessage("ORDERFORM_Q7_TITLE"),
				"TITLE_TYPE" => "text",
				"SID" => "ORDERFORM_Q7",
				"COMMENTS" => GetMessage("ORDERFORM_Q1_COMMENT"),			   
				"C_SORT" => 700,
				"ADDITIONAL" => "N",
				"REQUIRED" => "N",
				"IN_RESULTS_TABLE" => "Y",
				"IN_EXCEL_TABLE" => "Y",
				"arFILTER_ANSWER_TEXT" => array("text"),
				"arANSWER" => $arAnswer,
			);
			$Q_ID = CFormField::Set($arFields);
			
			//	КПП
			$arAnswer = array();
			$arAnswer[] = array(
				"ACTIVE" => "Y",
				"C_SORT" => 1,
				"MESSAGE" => " ",
				"VALUE" => "",
				"FIELD_TYPE" => "text",
				"FIELD_WIDTH" => "38"
			);
			$arFields = array(
				"FORM_ID" => $NEW_ID,                   
				"ACTIVE" => "Y",
				"TITLE" => GetMessage("ORDERFORM_Q8_TITLE"),
				"TITLE_TYPE" => "text",
				"SID" => "ORDERFORM_Q8",
				"COMMENTS" => GetMessage("ORDERFORM_Q1_COMMENT"),			   
				"C_SORT" => 800,
				"ADDITIONAL" => "N",
				"REQUIRED" => "N",
				"IN_RESULTS_TABLE" => "Y",
				"IN_EXCEL_TABLE" => "Y",
				"arFILTER_ANSWER_TEXT" => array("text"),
				"arANSWER" => $arAnswer,
			);
			$Q_ID = CFormField::Set($arFields);
			
			//	Пункт отправки (пункт заргузки)
			$arAnswer = array();
			$arAnswer[] = array(
				"ACTIVE" => "Y",
				"C_SORT" => 1,
				"MESSAGE" => " ",
				"VALUE" => "",
				"FIELD_TYPE" => "text",
				"FIELD_WIDTH" => "38"
			);
			$arFields = array(
				"FORM_ID" => $NEW_ID,                   
				"ACTIVE" => "Y",
				"TITLE" => GetMessage("ORDERFORM_Q9_TITLE"),
				"TITLE_TYPE" => "text",
				"SID" => "ORDERFORM_Q9",
				"COMMENTS" => GetMessage("ORDERFORM_Q9_COMMENT"),			   
				"C_SORT" => 900,
				"ADDITIONAL" => "N",
				"REQUIRED" => "Y",
				"IN_RESULTS_TABLE" => "Y",
				"IN_EXCEL_TABLE" => "Y",
				"arFILTER_ANSWER_TEXT" => array("text"),
				"arANSWER" => $arAnswer,
			);
			$Q_ID = CFormField::Set($arFields);
			
			//	Пункт назначения (пункт выгрузки)
			$arAnswer = array();
			$arAnswer[] = array(
				"ACTIVE" => "Y",
				"C_SORT" => 1,
				"MESSAGE" => " ",
				"VALUE" => "",
				"FIELD_TYPE" => "text",
				"FIELD_WIDTH" => "38"
			);
			$arFields = array(
				"FORM_ID" => $NEW_ID,                   
				"ACTIVE" => "Y",
				"TITLE" => GetMessage("ORDERFORM_Q10_TITLE"),
				"TITLE_TYPE" => "text",
				"SID" => "ORDERFORM_Q10",
				"COMMENTS" => GetMessage("ORDERFORM_Q9_COMMENT"),			   
				"C_SORT" => 1000,
				"ADDITIONAL" => "N",
				"REQUIRED" => "Y",
				"IN_RESULTS_TABLE" => "Y",
				"IN_EXCEL_TABLE" => "Y",
				"arFILTER_ANSWER_TEXT" => array("text"),
				"arANSWER" => $arAnswer,
			);
			$Q_ID = CFormField::Set($arFields);
			
			//	Предполагаемая дата отгрузки
			$arAnswer = array();
			$arAnswer[] = array(
				"ACTIVE" => "Y",
				"C_SORT" => 1,
				"MESSAGE" => " ",
				"VALUE" => "",
				"FIELD_TYPE" => "date",
				"FIELD_WIDTH" => "38"
			);
			$arFields = array(
				"FORM_ID" => $NEW_ID,                   
				"ACTIVE" => "Y",
				"TITLE" => GetMessage("ORDERFORM_Q11_TITLE"),
				"TITLE_TYPE" => "text",
				"SID" => "ORDERFORM_Q11",
				"COMMENTS" => GetMessage("ORDERFORM_Q9_COMMENT"),			   
				"C_SORT" => 1100,
				"ADDITIONAL" => "N",
				"REQUIRED" => "N",
				"IN_RESULTS_TABLE" => "Y",
				"IN_EXCEL_TABLE" => "Y",
				"arANSWER" => $arAnswer,
			);
			$Q_ID = CFormField::Set($arFields);
			
			//	Описание груза
			$arAnswer = array();
			$arAnswer[] = array(
				"ACTIVE" => "Y",
				"C_SORT" => 1,
				"MESSAGE" => " ",
				"VALUE" => "",
				"FIELD_TYPE" => "textarea",
				"FIELD_WIDTH" => "30",
				"FIELD_HEIGHT" => "3"
			);
			$arFields = array(
				"FORM_ID" => $NEW_ID,                   
				"ACTIVE" => "Y",
				"TITLE" => GetMessage("ORDERFORM_Q12_TITLE"),
				"TITLE_TYPE" => "text",
				"SID" => "ORDERFORM_Q12",
				"COMMENTS" => GetMessage("ORDERFORM_Q9_COMMENT"),			   
				"C_SORT" => 1200,
				"ADDITIONAL" => "N",
				"REQUIRED" => "Y",
				"IN_RESULTS_TABLE" => "Y",
				"IN_EXCEL_TABLE" => "Y",
				"arFILTER_ANSWER_TEXT" => array("text"),
				"arANSWER" => $arAnswer,
			);
			$Q_ID = CFormField::Set($arFields);
			
			//	Вид перевозки
			$arAnswer = array();
			$arAnswer[] = array(
				"ACTIVE" => "Y",
				"C_SORT" => 1,
				"MESSAGE" => " ",
				"VALUE" => "",
				"FIELD_TYPE" => "text",
				"FIELD_WIDTH" => "38"
			);
			$arFields = array(
				"FORM_ID" => $NEW_ID,                   
				"ACTIVE" => "Y",
				"TITLE" => GetMessage("ORDERFORM_Q13_TITLE"),
				"TITLE_TYPE" => "text",
				"SID" => "ORDERFORM_Q13",
				"COMMENTS" => GetMessage("ORDERFORM_Q13_COMMENT"),			   
				"C_SORT" => 1300,
				"ADDITIONAL" => "N",
				"REQUIRED" => "N",
				"IN_RESULTS_TABLE" => "Y",
				"IN_EXCEL_TABLE" => "Y",
				"arFILTER_ANSWER_TEXT" => array("text"),
				"arANSWER" => $arAnswer,
			);
			$Q_ID = CFormField::Set($arFields);
			
			//	Код ТНВЭД
			$arAnswer = array();
			$arAnswer[] = array(
				"ACTIVE" => "Y",
				"C_SORT" => 1,
				"MESSAGE" => " ",
				"VALUE" => "",
				"FIELD_TYPE" => "text",
				"FIELD_WIDTH" => "38"
			);
			$arFields = array(
				"FORM_ID" => $NEW_ID,                   
				"ACTIVE" => "Y",
				"TITLE" => GetMessage("ORDERFORM_Q14_TITLE"),
				"TITLE_TYPE" => "text",
				"SID" => "ORDERFORM_Q14",
				"COMMENTS" => GetMessage("ORDERFORM_Q13_COMMENT"),			   
				"C_SORT" => 1400,
				"ADDITIONAL" => "N",
				"REQUIRED" => "N",
				"IN_RESULTS_TABLE" => "Y",
				"IN_EXCEL_TABLE" => "Y",
				"arFILTER_ANSWER_TEXT" => array("text"),
				"arANSWER" => $arAnswer,
			);
			$Q_ID = CFormField::Set($arFields);
			
			//	Вес брутто, ~ кг
			$arAnswer = array();
			$arAnswer[] = array(
				"ACTIVE" => "Y",
				"C_SORT" => 1,
				"MESSAGE" => " ",
				"VALUE" => "",
				"FIELD_TYPE" => "text",
				"FIELD_WIDTH" => "38"
			);
			$arFields = array(
				"FORM_ID" => $NEW_ID,                   
				"ACTIVE" => "Y",
				"TITLE" => GetMessage("ORDERFORM_Q15_TITLE"),
				"TITLE_TYPE" => "text",
				"SID" => "ORDERFORM_Q15",
				"COMMENTS" => GetMessage("ORDERFORM_Q13_COMMENT"),			   
				"C_SORT" => 1500,
				"ADDITIONAL" => "N",
				"REQUIRED" => "N",
				"IN_RESULTS_TABLE" => "Y",
				"IN_EXCEL_TABLE" => "Y",
				"arFILTER_ANSWER_TEXT" => array("text"),
				"arANSWER" => $arAnswer,
			);
			$Q_ID = CFormField::Set($arFields);
			
			//	Объем, ? куб.м.
			$arAnswer = array();
			$arAnswer[] = array(
				"ACTIVE" => "Y",
				"C_SORT" => 1,
				"MESSAGE" => " ",
				"VALUE" => "",
				"FIELD_TYPE" => "text",
				"FIELD_WIDTH" => "38"
			);
			$arFields = array(
				"FORM_ID" => $NEW_ID,                   
				"ACTIVE" => "Y",
				"TITLE" => GetMessage("ORDERFORM_Q16_TITLE"),
				"TITLE_TYPE" => "text",
				"SID" => "ORDERFORM_Q16",
				"COMMENTS" => GetMessage("ORDERFORM_Q13_COMMENT"),			   
				"C_SORT" => 1600,
				"ADDITIONAL" => "N",
				"REQUIRED" => "N",
				"IN_RESULTS_TABLE" => "Y",
				"IN_EXCEL_TABLE" => "Y",
				"arFILTER_ANSWER_TEXT" => array("text"),
				"arANSWER" => $arAnswer,
			);
			$Q_ID = CFormField::Set($arFields);
			
			//	Габаритные размеры (длина, ширина, высота)
			$arAnswer = array();
			$arAnswer[] = array(
				"ACTIVE" => "Y",
				"C_SORT" => 1,
				"MESSAGE" => " ",
				"VALUE" => "",
				"FIELD_TYPE" => "text",
				"FIELD_WIDTH" => "38"
			);
			$arFields = array(
				"FORM_ID" => $NEW_ID,                   
				"ACTIVE" => "Y",
				"TITLE" => GetMessage("ORDERFORM_Q17_TITLE"),
				"TITLE_TYPE" => "text",
				"SID" => "ORDERFORM_Q17",
				"COMMENTS" => GetMessage("ORDERFORM_Q13_COMMENT"),			   
				"C_SORT" => 1700,
				"ADDITIONAL" => "N",
				"REQUIRED" => "N",
				"IN_RESULTS_TABLE" => "Y",
				"IN_EXCEL_TABLE" => "Y",
				"arFILTER_ANSWER_TEXT" => array("text"),
				"arANSWER" => $arAnswer,
			);
			$Q_ID = CFormField::Set($arFields);
			
			//	Дополнительная информация
			$arAnswer = array();
			$arAnswer[] = array(
				"ACTIVE" => "Y",
				"C_SORT" => 1,
				"MESSAGE" => " ",
				"VALUE" => "",
				"FIELD_TYPE" => "textarea",
				"FIELD_WIDTH" => "30",
				"FIELD_HEIGHT" => "3"
			);
			$arFields = array(
				"FORM_ID" => $NEW_ID,                   
				"ACTIVE" => "Y",
				"TITLE" => GetMessage("ORDERFORM_Q18_TITLE"),
				"TITLE_TYPE" => "text",
				"SID" => "ORDERFORM_Q18",
				"COMMENTS" => GetMessage("ORDERFORM_Q18_COMMENT"),			   
				"C_SORT" => 1800,
				"ADDITIONAL" => "N",
				"REQUIRED" => "N",
				"IN_RESULTS_TABLE" => "Y",
				"IN_EXCEL_TABLE" => "Y",
				"arFILTER_ANSWER_TEXT" => array("text"),
				"arANSWER" => $arAnswer,
			);
			$Q_ID = CFormField::Set($arFields);
		}
	}
	
	if(intval($NEW_ID)<=0)
		$NEW_ID = $arForm["ID"];
	
	if(intval($NEW_ID)>0){
		CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_SITE_ID."/footer.php", array("ORDERFORM_ACTIVE" => "Y"));
		CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/main_".WIZARD_SITE_ID."/footer.php", array("ORDERFORM_ACTIVE" => "Y"));
		CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/services/orderform/index.php", array("ORDERFORM_ID" => $NEW_ID));
		if(CModule::IncludeModule('fileman')){
			$menuFile = "services/.left.menu.php";
			$fullMenuFile = $_SERVER["DOCUMENT_ROOT"].WIZARD_SITE_DIR.$menuFile;
			$arResult = CFileMan::GetMenuArray($fullMenuFile);
			$arMenuItems = $arResult["aMenuLinks"];
			$menuTemplate = $arResult["sMenuTemplate"];
			$check = true;
			foreach($arMenuItems as $item){
				if($item[1] == "orderform/"){
					$check = false;
					break;
				}
			}
			if($check){
				$arMenuItems[] = array(
					GetMessage("ORDERFORM_MENU_TITLE"),
					"orderform/", 
					Array(),
					Array(),
					""
				);
				CFileMan::SaveMenu(Array(WIZARD_SITE_ID, WIZARD_SITE_DIR.$menuFile), $arMenuItems, $menuTemplate);
			}
		}
	}else{
		CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_SITE_ID."/footer.php", array("ORDERFORM_ACTIVE" => "N"));
		CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/templates/main_".WIZARD_SITE_ID."/footer.php", array("ORDERFORM_ACTIVE" => "N"));
	}
}
?>