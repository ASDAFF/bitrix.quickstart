<?
/*������
===========================================================================================================================================*/
// ������ ���-�����                         
if (!CModule::IncludeModule("form")) return;

//���������� ��� �����
$codeForm = 'FORM_FEEDBACK_'.WIZARD_SITE_ID;

// ������� ������� � ����� ������������ ���������� ����� �������������� ����
COption::SetOptionInt("form", "SIMPLE", "N");

// ������ ���� ���� � ������� � �������� ������������ ���� ����� �� ����������
$arFilter = Array(
		"SID"                     => $codeForm,         
		"SID_EXACT_MATCH"         => "Y",
        "SITE"                    => array(WIZARD_SITE_ID),
        "SITE_EXACT_MATCH"        => "Y"
);


$rsForms = CForm::GetList($by="s_id", $order="desc", $arFilter, $is_filtered);
if ($arForm = $rsForms->Fetch())
{
	// ����� ����������, ��������� ���� �� �������
	$formId = $arForm["ID"];
    
} else {
	// ������� �����
	$formId = false;
	
}

// ���������� ������������� �����
//$rsSites = CSite::GetList($by="sort", $order="desc", Array());
//if  ($arSite = $rsSites->Fetch())
//{
//	$siteId = $arSite["ID"];
//	
//}
$siteId = WIZARD_SITE_ID;

// ������� ����� ���-����� ��� ������� ���� ��� ��� �������.
//die($siteId);
$arFields = array(
		"NAME"              => GetMessage('MACROS_1'),
		"SID"               => $codeForm,
		"C_SORT"            => 300,
		"BUTTON"            => GetMessage('MACROS_2'),
		"DESCRIPTION"       => "",
		"DESCRIPTION_TYPE"  => "text",
		"STAT_EVENT1"       => "form",
		"STAT_EVENT2"       => "form_feedback",
		"arSITE"            => array($siteId),
		"arMENU"            => array("ru" => GetMessage('MACROS_3'), "en" => "Feedback"),
);


$formId = CForm::Set($arFields, $formId);
/*if ($formId>0) echo "��������� ���-����� � ID=".$formId;
else // ������
{
	// ������� ����� ������
	global $strError;
	echo $strError;
}*/

// ���������� ������ �������
$arFilter = Array();

// ������� ������ ���� �������� �����, ��������������� �������
$rsStatuses = CFormStatus::GetList(
		$formId,
		$by="s_id",
		$order="desc",
		$arFilter,
		$is_filtered
);
$hasStatusesFlag = false;
if  ($arStatus = $rsStatuses->Fetch())
{
	$hasStatusesFlag = true;
}

if ($hasStatusesFlag == false) {
	// ������� ������
		
	$arFields = array(
			"FORM_ID"             => $formId,               // ID ���-�����
			"C_SORT"              => 100,                    // ������� ����������
			"ACTIVE"              => "Y",                    // ������ �������
			"TITLE"               => "DEFAULT",         // ��������� �������
			"DESCRIPTION"         => "DEFAULT",     // �������� �������
			"CSS"                 => "statusgreen",          // CSS �����
			"HANDLER_OUT"         => "",                     // ����������
			"HANDLER_IN"          => "",                     // ����������
			"DEFAULT_VALUE"       => "Y",                    // �� ���������
			"arPERMISSION_VIEW"   => array(0),               // ����� ��������� ��� ����
			"arPERMISSION_MOVE"   => array(0),                // ����� �������� ������ �������
			"arPERMISSION_EDIT"   => array(0),                // ����� �������������� ��� �������
			"arPERMISSION_DELETE" => array(0),                // ����� �������� ������ �������
	);
	
	$statusId = CFormStatus::Set($arFields);
	
}

$hasQuestionsFlag = false;
// ������� ������� � �����

// ���������� ������ �������
$arFilter = Array();

// ������� ������ ���� �������� ���-����� #4
$rsQuestions = CFormField::GetList(
		$formId,
		"N",
		$by="s_id",
		$order="desc",
		$arFilter,
		$is_filtered
);
while ($arQuestion = $rsQuestions->Fetch())
{
	//echo "<pre> $arQuestion"; print_r($arQuestion); echo "</pre>";
	$hasQuestionsFlag = true;
}

if ($hasQuestionsFlag==false) {
	// ������� �������
	//feedback_name
	// ��������� ������ �������
	$arANSWER = array();
	
	$arANSWER[] = array(
			"MESSAGE"     => " ",                           // �������� ANSWER_TEXT
			"C_SORT"      => 100,                            // ������� ����������
			"ACTIVE"      => "Y",                            // ���� ����������
			"FIELD_TYPE"  => "text",                        // ��� ������
			"FIELD_PARAM" => ""  // ��������� ������
	);

	
	// ��������� ������ �����
	$arFields = array(
			"FORM_ID"              => $formId,                     // ID ���-�����
			"ACTIVE"               => Y,                     // ���� ����������
			"TITLE"                => GetMessage('MACROS_5'), // ����� �������
			"TITLE_TYPE"           => "text",                // ��� ������ �������
			"SID"                  => "feedback_name",          // ���������� ������������� �������
			"C_SORT"               => 100,                   // ������� ����������
			"ADDITIONAL"           => "N",                   // �� ��������� ������ ���-�����
			"REQUIRED"             => "Y",                   // ����� �� ������ ������ ����������
			"IN_RESULTS_TABLE"     => "Y",                   // �������� � HTML ������� �����������
			"IN_EXCEL_TABLE"       => "Y",                   // �������� � Excel ������� �����������
			"FILTER_TITLE"         => GetMessage('MACROS_7'),       // ������� � ���� �������
			"RESULTS_TABLE_TITLE"  => GetMessage('MACROS_7'),       // ��������� ������� �������
			//"arIMAGE"              => $arIMAGE,              // ����������� �������
			"arFILTER_ANSWER_TEXT" => array("text"),     // ��� ������� �� ANSWER_TEXT
			"arANSWER"             => $arANSWER,             // ����� �������
	);
	
	// ������� ����� ������
	$NEW_ID = CFormField::Set($arFields);
		
	// feedback_message
	$arANSWER = array();
	
	$arANSWER[] = array(
			"MESSAGE"     => " ",                           // �������� ANSWER_TEXT
			"C_SORT"      => 100,                            // ������� ����������
			"ACTIVE"      => "Y",                            // ���� ����������
			"FIELD_TYPE"  => "textarea",                        // ��� ������
			"FIELD_PARAM" => ""  // ��������� ������
	);
	
	
	// ��������� ������ �����
	$arFields = array(
			"FORM_ID"              => $formId,                     // ID ���-�����
			"ACTIVE"               => Y,                     // ���� ����������
			"TITLE"                => GetMessage('MACROS_6'), // ����� �������
			"TITLE_TYPE"           => "text",                // ��� ������ �������
			"SID"                  => "feedback_message",          // ���������� ������������� �������
			"C_SORT"               => 200,                   // ������� ����������
			"ADDITIONAL"           => "N",                   // �� ��������� ������ ���-�����
			"REQUIRED"             => "Y",                   // ����� �� ������ ������ ����������
			"IN_RESULTS_TABLE"     => "Y",                   // �������� � HTML ������� �����������
			"IN_EXCEL_TABLE"       => "Y",                   // �������� � Excel ������� �����������
			"FILTER_TITLE"         => GetMessage('MACROS_6'),       // ������� � ���� �������
			"RESULTS_TABLE_TITLE"  => GetMessage('MACROS_6'),       // ��������� ������� �������
			//"arIMAGE"              => $arIMAGE,              // ����������� �������
			//"arFILTER_ANSWER_TEXT" => array("text"),     // ��� ������� �� ANSWER_TEXT
			"arANSWER"             => $arANSWER,             // ����� �������
	);
	
	// ������� ����� ������
	$NEW_ID = CFormField::Set($arFields);
		
	//feedback_email
	$arANSWER = array();
	
	$arANSWER[] = array(
			"MESSAGE"     => " ",                           // �������� ANSWER_TEXT
			"C_SORT"      => 100,                            // ������� ����������
			"ACTIVE"      => "Y",                            // ���� ����������
			"FIELD_TYPE"  => "email",                        // ��� ������
			"FIELD_PARAM" => ""  // ��������� ������
	);
	
	
	// ��������� ������ �����
	$arFields = array(
			"FORM_ID"              => $formId,                     // ID ���-�����
			"ACTIVE"               => Y,                     // ���� ����������
			"TITLE"                => GetMessage('MACROS_8'), // ����� �������
			"TITLE_TYPE"           => "text",                // ��� ������ �������
			"SID"                  => "feedback_email",          // ���������� ������������� �������
			"C_SORT"               => 300,                   // ������� ����������
			"ADDITIONAL"           => "N",                   // �� ��������� ������ ���-�����
			"REQUIRED"             => "Y",                   // ����� �� ������ ������ ����������
			"IN_RESULTS_TABLE"     => "Y",                   // �������� � HTML ������� �����������
			"IN_EXCEL_TABLE"       => "Y",                   // �������� � Excel ������� �����������
			"FILTER_TITLE"         => GetMessage('MACROS_9'),       // ������� � ���� �������
			"RESULTS_TABLE_TITLE"  => GetMessage('MACROS_9'),       // ��������� ������� �������
			//"arIMAGE"              => $arIMAGE,              // ����������� �������
			//"arFILTER_ANSWER_TEXT" => array("text"),     // ��� ������� �� ANSWER_TEXT
			"arANSWER"             => $arANSWER,             // ����� �������
	);
	
	// ������� ����� ������
	$NEW_ID = CFormField::Set($arFields);
}

// ����������� ����� ��������� �������� ������� ������ ���-�����
CForm::Set(array("arMAIL_TEMPLATE" => array($templateId)), $formId);

COption::SetOptionInt("novagr.shop", 'formFeedbackID'.WIZARD_SITE_ID, $formId );
COption::SetOptionString("novagr.shop", 'formFeedbackName'.WIZARD_SITE_ID, $codeForm );
COption::SetOptionString("novagr.shop", 'formFeedbacktemplateID'.WIZARD_SITE_ID, $templateId );
?>