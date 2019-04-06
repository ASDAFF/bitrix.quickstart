<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?include(GetLangFileName(dirname(__FILE__).'/', '/payment.php'));?>
<?
	$iOrderID = IntVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]);
	$obData = (CPickpoint::SelectOrderPostamat($iOrderID));
	$arData = $obData->Fetch();
?>
<style>
	.pp_ps_table
	{
		border-collapse:collapse;
	}
	.pp_ps_table td,.pp_ps_table th
	{
		padding:10px;
		border:1px solid #CECECE;
	}
	.pp_sms
	{
		margin:15px;
		font-size:14px;
	}
	.pp_sms span
	{
		margin-left:30px;
	}
</style>
<table class = "pp_ps_table">
	<tr><th><?=GetMessage("PP_POSTAMAT")?></th><th><?=GetMessage("PP_VALUE")?></th></tr>
	<tr>
		<td><?=GetMessage("PP_POSTAMAT_ID")?></td><td><?=$arData["POSTAMAT_ID"]?></td>
	</tr>
	<tr>
		<td><?=GetMessage("PP_ADDRESS")?></td><td><?=$arData["ADDRESS"]?></td>
	</tr>
	<tr>		
		<td><?=GetMessage("PP_NAME")?></td><td><?=$arData["NAME"]?></td>
	</tr>
</table>
<p class = "pp_sms"><?=GetMessage("PP_SMS_PHONE")?>:<span><?=$arData["SMS_PHONE"]?></span></p>