<?
$DOCTOR_ID = intval($arParams["DOCTOR_ID"]);
if($DOCTOR_ID > 0):

	$res = CIBlockElement::GetByID($DOCTOR_ID);
	if($ob = $res->GetNext(false,false))
		$arResult["DOCTOR"] = $ob["NAME"]."\r\n".$ob["PREVIEW_TEXT"];

endif;
?>