<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//�������� ������ �� ����

$email_to = "";
$outName = "";
$outPhone = "";
$outText = "";
$message = "";

//e-mail ���� ����������
if(isset($_POST['EMAIL_TO']))
{
	$email_to = $_POST['EMAIL_TO'];
}
//���
if(isset($_POST['NAME']))
{
	$message .= "\r\n".GetMessage("WAPXAZ_AJAXFORM_IMA").$_POST['NAME'];
}
//�������
if(isset($_POST['PHONE']))
{
	$message .= "\r\n".GetMessage("WAPXAZ_AJAXFORM_TELEFON").$_POST['PHONE'];
}
//����� ���������
if(isset($_POST['MESSAGE']))
{
	$message .= "\r\n".GetMessage("WAPXAZ_AJAXFORM_TEKST_SOOBSENIA").$_POST['MESSAGE'];
}

//���������� c e-mail, ��������� � ���������� �����
$rsSites = CSite::GetByID(SITE_ID); 
$arSite = $rsSites->Fetch(); 
$email_from = $arSite["EMAIL"];//email ������(�� �������� �����)

if($email_to != "") { 
	mail($email_to, GetMessage("WAPXAZ_AJAXFORM_DANNYE_S_FORMY"), $message, "From: ".$email_from." \r\n");
}
?>