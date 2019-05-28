<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;

$createUrl = "https://w.qiwi.com/order/external/create.action";

Loc::loadMessages(__FILE__);

/** @method  static CSalePaySystemAction::GetParamValue */

$to 		= trim(CSalePaySystemAction::GetParamValue("CLIENT_PHONE"));
$from 		= CSalePaySystemAction::GetParamValue("SHOP_ID");
$summ 		= (double)CSalePaySystemAction::GetParamValue("SHOULD_PAY");
$currency	= CSalePaySystemAction::GetParamValue("CURRENCY");
$txnId 		= (int)CSalePaySystemAction::GetParamValue("ORDER_ID");
$successUrl = CSalePaySystemAction::GetParamValue("SUCCESS_URL");
$failUrl 	= CSalePaySystemAction::GetParamValue("FAIL_URL");
$lifetime	= (int)CSalePaySystemAction::GetParamValue("BILL_LIFETIME");
$comment	= "Order-{$txnId}"; //Loc::getMessage("SALE_QH_COMMENT", array("#ID#" => $txnId));
$validPhone = '/^\+7\d{10}$/';

if (isset($_POST["SET_NEW_PHONE"]))
	$to = trim($_POST["NEW_PHONE"]);
?>

<?if(!preg_match($validPhone, $to)):?>
	<form  action="<?= POST_FORM_ACTION_URI?>" method="post">
		<p><strong><?=Loc::getMessage("SALE_QH_INCORRECT_PHONE_NUMBER")?></strong></p>
		<p><?=htmlspecialchars(Loc::getMessage("SALE_QH_INPUT_PHONE"))?></p>
		<input type="text" name="NEW_PHONE" size="30" value="+7" placeholder="+7" />
		<input type="submit" name="SET_NEW_PHONE" value="<?= Loc::getMessage("SALE_QH_SEND_PHONE")?>" />
	</form>
<?else:?>
	<form  action="<?=$createUrl?>" method="post">
		<p>
			<?=Loc::getMessage("SALE_QH_SUMM_TO_PAY")?>:
			<?if(Loader::includeModule("currency")):?>
				<strong><?=CCurrencyLang::CurrencyFormat($summ, $currency, true);?></strong>
			<?else:?>
				<strong><?=$summ;?> <?=$currency?></strong>
			<?endif;?>
		</p>
		<input type="hidden" name="to" value="<?=$to?>"/>
		<input type="hidden" name="from" value="<?=$from?>"/>
		<input type="hidden" name="summ" value="<?=$summ?>"/>
		<input type="hidden" name="currency" value="<?=$currency?>"/>
		<input type="hidden" name="comm" value="<?=htmlspecialcharsbx($comment)?>"/>
		<input type="hidden" name="txn_id" value="<?=$txnId?>"/>
		<input type="hidden" name="successUrl" value="<?=$successUrl?>"/>
		<input type="hidden" name="failUrl" value="<?=$failUrl?>"/>
		<input type="hidden" name="lifetime" value="<?=$lifetime?>"/>
		<input type="submit" value="<?=Loc::getMessage("SALE_QH_DO_BILL")?>" />
	</form>
<?endif?>