<?
if(!$USER->IsAdmin())
	return;

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/jivosite.jivosite/config.php');
IncludeModuleLangFile(__FILE__);

?>
<br>

<img src="http://www.jivosite.ru/wp-content/themes/Lotus/images/logo.png" alt="">

<?
$token = COption::GetOptionString("jivosite.jivosite", "auth_token");

if ($token) {
	
?>
	<form action="https://<?= JIVO_BASE_URL ?>/integration/login" target="_blank">
		<input type="hidden" name="token" value="<?= $token ?>">
		<input type="hidden" name="partner" value="bitrix">
		<input style="font-size: 18px; padding: 8px;" type="submit" value="<?= GetMessage('GOTO_ADMIN') ?>">
	</form>
		
<?}?>

<?= GetMessage('SETUP_AIR') ?>


