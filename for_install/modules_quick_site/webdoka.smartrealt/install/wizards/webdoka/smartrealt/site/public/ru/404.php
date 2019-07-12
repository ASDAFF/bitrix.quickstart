<?php 
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y"); 
$APPLICATION->SetTitle("Страница не найдена");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_after.php");
?>
<div align="left"> 
  <h1>Страница не найдена</h1>

  <p>Страница, которую вы запрашиваете, не найдена. Попробуйте вернуться на <a href="<?php echo SITE_DIR?>">главную страницу</a>.</p>
</div>  
<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>