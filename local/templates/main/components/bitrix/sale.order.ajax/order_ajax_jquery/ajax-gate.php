<?// "AJAX - интерфейс". Скрипт, через который js получает данные с сервера
if(isset($_POST["LOAD_STEP"])) {
	include($_POST["LOAD_STEP"]);
} else if(isset($_POST["SUBMIT_FORM"]) && $_POST["SUBMIT_FORM"] == "Y") {
	echo json_encode(Array("error" => $arResult["ERROR"], "redirect" => $arResult["REDIRECT_URL"]));
} else {
	$arResult["FIELDS"] = $arParams["FIELDS"];
	echo json_encode($arResult);
}?>