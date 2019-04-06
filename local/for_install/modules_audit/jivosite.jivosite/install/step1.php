<?

if(!check_bitrix_sessid()) return;
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/jivosite.jivosite/config.php');

IncludeModuleLangFile(__FILE__);

?>

<style>
	p.comment{
		width: 500px;
		font-style: italic;
		color: #888;
	}
</style>

<img src="http://jivo-userdata.s3.amazonaws.com/mail-images/logo-new.png" alt="">


<?= GetMessage("SIGN_UP_FORM") ?> 

<p><a href="<?echo $APPLICATION->GetCurPage()?>">&laquo; <?= GetMessage("BACK_TO_MODULE_LIST") ?></a></p>
