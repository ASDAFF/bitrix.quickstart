<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="subscribe-edit">
<?
foreach($arResult["MESSAGE"] as $itemID=>$itemValue)
	echo ShowMessage(array("MESSAGE"=>$itemValue, "TYPE"=>"OK"));
foreach($arResult["ERROR"] as $itemID=>$itemValue)
	echo ShowMessage(array("MESSAGE"=>$itemValue, "TYPE"=>"ERROR"));
?>
	

<h2><?=$arResult["SHOW_SMS_FORM"] ? GetMessage('sms_subscr') : GetMessage('email_subscr') ?></h2>

<!--Если пользователь отписался выводим ему спец сообщение-->
<?if ($arResult["UNSUBSCRIBE_FORM"] == 'show'):?>
	<h3><?=GetMessage('subscrb_kill')?></h3>
	<p>
		<?=GetMessage('subscrb_kill_1')?>&nbsp;<a href = '?ID=<?=$arResult["SUBSCRIPTION"]["ID"]?>'><?=GetMessage('subscr_managment')?></a>
	</p>
	<?return;?>
<?endif;?>

<?

//whether to show the forms
if(($arResult["ID"] == 0 && empty($_REQUEST["action"])) || 
	CSubscription::IsAuthorized($arResult["ID"]) || 
	$arResult["REQUEST"]["EMAIL"] == ''
)
{
	//show confirmation form
	$is_sms = preg_match("/^[ \+\-\(\)0-9]+?@phone\.sms$/",$arResult["SUBSCRIPTION"]["EMAIL"]);
	if($arResult["ID"] > 0 && $arResult["SUBSCRIPTION"]["CONFIRMED"] <> "Y" && (($is_sms && $arResult["SHOW_SMS_FORM"]) || ($arResult["SHOW_POST_FORM"] && !$is_sms)))
	{
		include("confirmation.php");
	}
	else
	{
		include("setting.php");	
	}
	//show current authorization section
	if($USER->IsAuthorized() && ($arResult["ID"] == 0 || $arResult["SUBSCRIPTION"]["USER_ID"] == 0))
	{
		//include("authorization.php");
	}
	//show authorization section for new subscription
	if($arResult["ID"]==0 && !$USER->IsAuthorized())
	{
		if($arResult["ALLOW_ANONYMOUS"]=="N" || ($arResult["ALLOW_ANONYMOUS"]=="Y" && $arResult["SHOW_AUTH_LINKS"]=="Y"))
		{
			//include("authorization_new.php");
		}
	}
	
	//status and unsubscription/activation section
	if($arResult["ID"]>0)
	{
		//include("status.php");
	}
}
else
{
	//subscription authorization form
	include("authorization_full.php");
}
?>
</div>