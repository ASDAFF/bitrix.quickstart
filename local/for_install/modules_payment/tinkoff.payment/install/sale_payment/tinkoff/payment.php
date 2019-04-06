<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<?
include(GetLangFileName(dirname(__FILE__)."/", "/tinkoff.php"));

include(dirname(__FILE__)."/sdk/tinkoff_autoload.php");

$shouldPay = (strlen(CSalePaySystemAction::GetParamValue("SHOULD_PAY")) > 0) ? CSalePaySystemAction::GetParamValue("SHOULD_PAY") : $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"];
$orderID = (strlen(CSalePaySystemAction::GetParamValue("ORDER_ID")) > 0) ? CSalePaySystemAction::GetParamValue("ORDER_ID") : $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"];

$bankHandler = new Tinkoff(CSalePaySystemAction::GetParamValue("TERMINAL_ID"), CSalePaySystemAction::GetParamValue("SHOP_SECRET_WORD"), CSalePaySystemAction::GetParamValue("TINKOFF_PAYMENT_URL"));

$readyToPay = true;

//log
$log_pay = $shouldPay * 100;
$log = '['.date('D M d H:i:s Y',time()).'] ';
$log.= 'OrderId='.$orderID .'; Price='.$log_pay;
$log.= "\n";
file_put_contents(dirname(__FILE__)."/tinkoff.log", $log, FILE_APPEND);

try {
    $payment = $bankHandler->initPayment(array(
        'amount' => $shouldPay * 100,
        'orderId' => $orderID,
    ));
    //log
    $log = '['.date('D M d H:i:s Y',time()).'] ';
    $log.= 'OrderId='.$orderID ." good payment";
    $log.= "\n";
    file_put_contents(dirname(__FILE__)."/tinkoff.log", $log, FILE_APPEND);
} catch (TinkoffException $e) {
    $readyToPay = false;

    //log
    $log = '['.date('D M d H:i:s Y',time()).'] ';
    $log.= 'OrderId='.$orderID ." ". $e->getMessage();
    $log.= "\n";
    file_put_contents(dirname(__FILE__)."/tinkoff.log", $log, FILE_APPEND);
}
?>

<?php if($readyToPay): ?>
    <FORM ACTION="<?php echo $payment['url']; ?>" METHOD="GET" target="_blank">
        <?php foreach($payment['params'] as $name => $value): ?>
            <INPUT TYPE="HIDDEN" NAME="<?php echo $name; ?>" VALUE="<?php echo $value; ?>">
        <?php endforeach; ?>

        <INPUT TYPE="SUBMIT" VALUE="<?echo GetMessage("SALE_TINKOFF_PAYBUTTON_NAME")?>">
    </FORM>

    <p align="justify">
        <b><?php echo GetMessage("PAYMENT_DESCRIPTION"); ?></b>
    </p>
<?php else: ?>
    <b><?php echo GetMessage("SALE_TINKOFF_UNAVAILABLE"); ?></b>
<?php endif; ?>