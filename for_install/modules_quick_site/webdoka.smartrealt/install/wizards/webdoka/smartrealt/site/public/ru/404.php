<?php 
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y"); 
$APPLICATION->SetTitle("�������� �� �������");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_after.php");
?>
<div align="left"> 
  <h1>�������� �� �������</h1>

  <p>��������, ������� �� ������������, �� �������. ���������� ��������� �� <a href="<?php echo SITE_DIR?>">������� ��������</a>.</p>
</div>  
<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>