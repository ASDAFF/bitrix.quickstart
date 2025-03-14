<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
/**
 * Copyright (c) 17/9/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

//отправка данных из форм

$email_to = "";
$outName = "";
$outPhone = "";
$outText = "";
$message = "";

//e-mail кому отправляем
if(isset($_POST['EMAIL_TO']))
{
	$email_to = $_POST['EMAIL_TO'];
}
//имя
if(isset($_POST['NAME']))
{
	$message .= "\r\n".GetMessage("WAPXAZ_AJAXFORM_IMA").$_POST['NAME'];
}
//телефон
if(isset($_POST['PHONE']))
{
	$message .= "\r\n".GetMessage("WAPXAZ_AJAXFORM_TELEFON").$_POST['PHONE'];
}
//текст сообщения
if(isset($_POST['MESSAGE']))
{
	$message .= "\r\n".GetMessage("WAPXAZ_AJAXFORM_TEKST_SOOBSENIA").$_POST['MESSAGE'];
}

//отправляем c e-mail, указанный в настройках сайта
$rsSites = CSite::GetByID(SITE_ID); 
$arSite = $rsSites->Fetch(); 
$email_from = $arSite["EMAIL"];//email админа(из настроек сайта)

if($email_to != "") { 
	mail($email_to, GetMessage("WAPXAZ_AJAXFORM_DANNYE_S_FORMY"), $message, "From: ".$email_from." \r\n");
}
?>