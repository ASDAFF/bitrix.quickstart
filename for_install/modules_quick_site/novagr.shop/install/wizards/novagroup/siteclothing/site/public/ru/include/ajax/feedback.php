<?php 
/**
 * Обработчик ajax для формы обр. связи
 * 
 */ 
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$siteUTF8 = true;
$rsSites = CSite::GetByID(SITE_ID);
$arSite = $rsSites->Fetch();
	
if (strtolower($arSite["CHARSET"]) == "windows-1251") {
	$siteUTF8 = false;	
	// конвертим реквест чтоб в письме не было кракозябр
	foreach ($_REQUEST as $key => $item) {
		$_REQUEST[$key] = iconv('UTF-8', 'windows-1251', $_REQUEST[$key]);
	}
	
}

//deb($_REQUEST);
if (CModule::IncludeModule("form") && $_SERVER["REQUEST_METHOD"] == "POST" && !empty($_REQUEST["web_form_submit"])) {

	$arResult = array();
	$result = array();
	// check errors
	$arResult["arrVALUES"] = $_REQUEST;
	$arResult["FORM_ERRORS"] = CForm::Check($_REQUEST["WEB_FORM_ID"], $arResult["arrVALUES"], false, "Y", "N");

	// отдаем ошибку
	if (!empty($arResult["FORM_ERRORS"])) {
		$result['result'] = 'ERROR';
		$arResult["FORM_ERRORS"] = str_replace(array("<br>", "<br />"), "\n", $arResult["FORM_ERRORS"]);
		$arResult["FORM_ERRORS"] = str_replace(array("&nbsp;&nbsp;&raquo;&nbsp;"), "", $arResult["FORM_ERRORS"]);
		$result['message'] = $arResult["FORM_ERRORS"];
		//$arResult["FORM_ERRORS"] = nl2br($arResult["FORM_ERRORS"]);
		//deb( $arResult["FORM_ERRORS"]);
		//deb( htmlspecialchars($arResult["FORM_ERRORS"]));
		
		//корректируем результат в зависимости от кодир.			
		if ($siteUTF8 == false) {
			foreach ($result as $key => $item) {
				$result[$key] = iconv('windows-1251', 'UTF-8', $result[$key]);
			}
		}
		
		$resultJson = json_encode($result);
		die($resultJson);
	}
	// если все ок то заполняем форму
	// check user session
	if (check_bitrix_sessid('sessid2'))
	{
		// add result
		if($RESULT_ID = CFormResult::Add($_REQUEST["WEB_FORM_ID"], $arResult["arrVALUES"]))
		{
			// send email notifications
			CFormCRM::onResultAdded($_REQUEST["WEB_FORM_ID"], $RESULT_ID);
			CFormResult::SetEvent($RESULT_ID);
			CFormResult::Mail($RESULT_ID);
			$result['result'] = 'OK';
			$result['message'] = "Сообщение отправлено успешно";
							
			//корректируем результат в зависимости от кодир.			
			if ($siteUTF8 == false) {
				foreach ($result as $key => $item) {
					$result[$key] = iconv('windows-1251', 'UTF-8', $result[$key]);
				}
			}			
			$resultJson = json_encode($result);
			die($resultJson);
		}
	}		
}
?>