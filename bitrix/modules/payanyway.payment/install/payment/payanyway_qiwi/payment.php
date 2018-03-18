<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));

$payment_system = 'payanyway_qiwi';
$unit_id = 822360;
$invoice = false;


if ($_SERVER["REQUEST_METHOD"] == "POST" && trim($_POST["SET_NEW_QIWI"])!=""){
	$qiwiUser = trim($_POST["QIWI_USER"]);
	$qiwiComment = trim($_POST["QIWI_COMMENT"]);
} else {
	$qiwiUser = "";
	$qiwiComment = "";
}

preg_match("/^([\d]{10})$/", $qiwiUser, $matches);

if (!$matches)
{
	?>
	<form method="post" action="<?= POST_FORM_ACTION_URI?>">
		<p><font color="Red"><?= GetMessage("PAYANYWAY_QIWIUSER_DESC")?></font></p>
		<table>
			<tr>
				<td><label><?= GetMessage("PAYANYWAY_QIWI_USER")?></label></td>
				<td><input type="text" name="QIWI_USER" value="<?= $qiwiUser?>"></td>
			</tr>
			<tr>
				<td><label><?= GetMessage("PAYANYWAY_QIWI_COMMENT")?></label></td>
				<td><input type="text" name="QIWI_COMMENT" value="<?= $qiwiComment?>"></td>
			</tr>
		</table>
		<input type="submit" name="SET_NEW_QIWI" value="<?= GetMessage("PAYANYWAY_EXTRA_PARAMS_OK")?>" />
	</form>
	<?
}
else
{
	$extraParameters["additionalParameters.qiwiUser"] = $qiwiUser;
	$extraParameters["additionalParameters.qiwiComment"] = $qiwiComment;
	
	if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/payment/payanyway/payment.php"))
		include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/payment/payanyway/payment.php");
}
