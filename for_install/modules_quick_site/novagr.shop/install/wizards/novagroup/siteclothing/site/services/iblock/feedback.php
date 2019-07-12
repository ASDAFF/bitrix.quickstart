<?
/*начало
===========================================================================================================================================*/
// модуль Веб-формы                         
if (!CModule::IncludeModule("form")) return;

//символьный код формы
$codeForm = 'FORM_FEEDBACK_'.WIZARD_SITE_ID;

// снимаем галочку с опции Использовать упрощённый режим редактирования форм
COption::SetOptionInt("form", "SIMPLE", "N");

// список всех форм у которых у текущего пользователя есть право на заполнение
$arFilter = Array(
		"SID"                     => $codeForm,         
		"SID_EXACT_MATCH"         => "Y",
        "SITE"                    => array(WIZARD_SITE_ID),
        "SITE_EXACT_MATCH"        => "Y"
);


$rsForms = CForm::GetList($by="s_id", $order="desc", $arFilter, $is_filtered);
if ($arForm = $rsForms->Fetch())
{
	// форма существует, проверяем есть ли статусы
	$formId = $arForm["ID"];
    
} else {
	// создаем форму
	$formId = false;
	
}

// определяем идентификатор сайта
//$rsSites = CSite::GetList($by="sort", $order="desc", Array());
//if  ($arSite = $rsSites->Fetch())
//{
//	$siteId = $arSite["ID"];
//	
//}
$siteId = WIZARD_SITE_ID;

// добавим новую веб-форму или обновим если она уже существ.
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
/*if ($formId>0) echo "Добавлена веб-форма с ID=".$formId;
else // ошибка
{
	// выводим текст ошибки
	global $strError;
	echo $strError;
}*/

// сформируем массив фильтра
$arFilter = Array();

// получим список всех статусов формы, соответствующих фильтру
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
	// создаем статус
		
	$arFields = array(
			"FORM_ID"             => $formId,               // ID веб-формы
			"C_SORT"              => 100,                    // порядок сортировки
			"ACTIVE"              => "Y",                    // статус активен
			"TITLE"               => "DEFAULT",         // заголовок статуса
			"DESCRIPTION"         => "DEFAULT",     // описание статуса
			"CSS"                 => "statusgreen",          // CSS класс
			"HANDLER_OUT"         => "",                     // обработчик
			"HANDLER_IN"          => "",                     // обработчик
			"DEFAULT_VALUE"       => "Y",                    // по умолчанию
			"arPERMISSION_VIEW"   => array(0),               // право просмотра для всех
			"arPERMISSION_MOVE"   => array(0),                // право перевода только админам
			"arPERMISSION_EDIT"   => array(0),                // право редактирование для админам
			"arPERMISSION_DELETE" => array(0),                // право удаления только админам
	);
	
	$statusId = CFormStatus::Set($arFields);
	
}

$hasQuestionsFlag = false;
// создаем вопросы в форме

// сформируем массив фильтра
$arFilter = Array();

// получим список всех вопросов веб-формы #4
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
	// создаем вопросы
	//feedback_name
	// формируем массив ответов
	$arANSWER = array();
	
	$arANSWER[] = array(
			"MESSAGE"     => " ",                           // параметр ANSWER_TEXT
			"C_SORT"      => 100,                            // порядок фортировки
			"ACTIVE"      => "Y",                            // флаг активности
			"FIELD_TYPE"  => "text",                        // тип ответа
			"FIELD_PARAM" => ""  // параметры ответа
	);

	
	// формируем массив полей
	$arFields = array(
			"FORM_ID"              => $formId,                     // ID веб-формы
			"ACTIVE"               => Y,                     // флаг активности
			"TITLE"                => GetMessage('MACROS_5'), // текст вопроса
			"TITLE_TYPE"           => "text",                // тип текста вопроса
			"SID"                  => "feedback_name",          // символьный идентификатор вопроса
			"C_SORT"               => 100,                   // порядок сортировки
			"ADDITIONAL"           => "N",                   // мы добавляем вопрос веб-формы
			"REQUIRED"             => "Y",                   // ответ на данный вопрос обязателен
			"IN_RESULTS_TABLE"     => "Y",                   // добавить в HTML таблицу результатов
			"IN_EXCEL_TABLE"       => "Y",                   // добавить в Excel таблицу результатов
			"FILTER_TITLE"         => GetMessage('MACROS_7'),       // подпись к полю фильтра
			"RESULTS_TABLE_TITLE"  => GetMessage('MACROS_7'),       // заголовок столбца фильтра
			//"arIMAGE"              => $arIMAGE,              // изображение вопроса
			"arFILTER_ANSWER_TEXT" => array("text"),     // тип фильтра по ANSWER_TEXT
			"arANSWER"             => $arANSWER,             // набор ответов
	);
	
	// добавим новый вопрос
	$NEW_ID = CFormField::Set($arFields);
		
	// feedback_message
	$arANSWER = array();
	
	$arANSWER[] = array(
			"MESSAGE"     => " ",                           // параметр ANSWER_TEXT
			"C_SORT"      => 100,                            // порядок фортировки
			"ACTIVE"      => "Y",                            // флаг активности
			"FIELD_TYPE"  => "textarea",                        // тип ответа
			"FIELD_PARAM" => ""  // параметры ответа
	);
	
	
	// формируем массив полей
	$arFields = array(
			"FORM_ID"              => $formId,                     // ID веб-формы
			"ACTIVE"               => Y,                     // флаг активности
			"TITLE"                => GetMessage('MACROS_6'), // текст вопроса
			"TITLE_TYPE"           => "text",                // тип текста вопроса
			"SID"                  => "feedback_message",          // символьный идентификатор вопроса
			"C_SORT"               => 200,                   // порядок сортировки
			"ADDITIONAL"           => "N",                   // мы добавляем вопрос веб-формы
			"REQUIRED"             => "Y",                   // ответ на данный вопрос обязателен
			"IN_RESULTS_TABLE"     => "Y",                   // добавить в HTML таблицу результатов
			"IN_EXCEL_TABLE"       => "Y",                   // добавить в Excel таблицу результатов
			"FILTER_TITLE"         => GetMessage('MACROS_6'),       // подпись к полю фильтра
			"RESULTS_TABLE_TITLE"  => GetMessage('MACROS_6'),       // заголовок столбца фильтра
			//"arIMAGE"              => $arIMAGE,              // изображение вопроса
			//"arFILTER_ANSWER_TEXT" => array("text"),     // тип фильтра по ANSWER_TEXT
			"arANSWER"             => $arANSWER,             // набор ответов
	);
	
	// добавим новый вопрос
	$NEW_ID = CFormField::Set($arFields);
		
	//feedback_email
	$arANSWER = array();
	
	$arANSWER[] = array(
			"MESSAGE"     => " ",                           // параметр ANSWER_TEXT
			"C_SORT"      => 100,                            // порядок фортировки
			"ACTIVE"      => "Y",                            // флаг активности
			"FIELD_TYPE"  => "email",                        // тип ответа
			"FIELD_PARAM" => ""  // параметры ответа
	);
	
	
	// формируем массив полей
	$arFields = array(
			"FORM_ID"              => $formId,                     // ID веб-формы
			"ACTIVE"               => Y,                     // флаг активности
			"TITLE"                => GetMessage('MACROS_8'), // текст вопроса
			"TITLE_TYPE"           => "text",                // тип текста вопроса
			"SID"                  => "feedback_email",          // символьный идентификатор вопроса
			"C_SORT"               => 300,                   // порядок сортировки
			"ADDITIONAL"           => "N",                   // мы добавляем вопрос веб-формы
			"REQUIRED"             => "Y",                   // ответ на данный вопрос обязателен
			"IN_RESULTS_TABLE"     => "Y",                   // добавить в HTML таблицу результатов
			"IN_EXCEL_TABLE"       => "Y",                   // добавить в Excel таблицу результатов
			"FILTER_TITLE"         => GetMessage('MACROS_9'),       // подпись к полю фильтра
			"RESULTS_TABLE_TITLE"  => GetMessage('MACROS_9'),       // заголовок столбца фильтра
			//"arIMAGE"              => $arIMAGE,              // изображение вопроса
			//"arFILTER_ANSWER_TEXT" => array("text"),     // тип фильтра по ANSWER_TEXT
			"arANSWER"             => $arANSWER,             // набор ответов
	);
	
	// добавим новый вопрос
	$NEW_ID = CFormField::Set($arFields);
}

// приписываем вновь созданные почтовые шаблоны данной веб-форме
CForm::Set(array("arMAIL_TEMPLATE" => array($templateId)), $formId);

COption::SetOptionInt("novagr.shop", 'formFeedbackID'.WIZARD_SITE_ID, $formId );
COption::SetOptionString("novagr.shop", 'formFeedbackName'.WIZARD_SITE_ID, $codeForm );
COption::SetOptionString("novagr.shop", 'formFeedbacktemplateID'.WIZARD_SITE_ID, $templateId );
?>