<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
if (strlen($backurl)>0) LocalRedirect($backurl);
$APPLICATION->SetTitle("�����������");
?>
<p class="notetext"><font >�� ���������������� � ������� ��������������.</font></p>
<p><a href="<?=SITE_DIR?>">��������� �� ������� ��������</a></p>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>