<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$ar = array('hl'=>SITE_DIR.'catalog/men-jackets/henri-lloyd-carriden-jacket/', 'f'=>SITE_DIR.'catalog/woman/', 'm'=>SITE_DIR.'catalog/men/');
if(isset($_REQUEST['to'])&&array_key_exists(trim($_REQUEST['to']), $ar))
	LocalRedirect($ar[trim($_REQUEST['to'])]);