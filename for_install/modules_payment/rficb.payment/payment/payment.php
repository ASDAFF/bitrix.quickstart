<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
IncludeModuleLangFile(__FILE__);

if (!CModule::IncludeModule("rficb.payment")) return;

?>
<link rel="stylesheet" type="text/css" href="/bitrix/themes/.default/rficb.css" /> 

<?
$ajax = 0;
foreach ( getallheaders() as $name => $value ) {
	if ($name == 'Bx-ajax') $ajax = 1;
}

$order_id = CSalePaySystemAction::GetParamValue("ORDER_ID");
$date = CSalePaySystemAction::GetParamValue("DATE_INSERT");
$cost = CSalePaySystemAction::GetParamValue("SHOULD_PAY");
$email = CUser::GetEmail ();

if( !($arOrder = CSaleOrder::GetByID($order_id))) return;

//$com = CRficbPayment::GetCommission($arOrder["LID"]);
$paytype = CRficbPayment::GetPayType($arOrder["LID"]);
$cart = CRficbPayment::GetPayCart($arOrder["LID"]);
$wm = CRficbPayment::GetPayWM($arOrder["LID"]);
$ym = CRficbPayment::GetPayYM($arOrder["LID"]);
$mc = CRficbPayment::GetPayMC($arOrder["LID"]);
$qiwi = CRficbPayment::GetPayQiwi($arOrder["LID"]);

$phone = CSalePaySystemAction::GetParamValue("PHONE");
$name = GetMessage("RFICB.PAYMENT_PAYMENT_FOR_ORDER", array("#DATE#" => $date,"#ORDER_ID#" => $order_id));
$key = CRficbPayment::GetKey($arOrder["LID"]);
$widget = CRficbPayment::GetWidget($arOrder["LID"]);

if ($widget == "Y" && $widgettype == 2 && $ajax ==0) { ?>

	<script type="text/javascript" src="https://partner.rficb.ru/gui/rfi_widget/js/v1.js" charset="utf-8"></script>
	<?
	$widgettype = CRficbPayment::GetWidgetType($arOrder["LID"]);
	?>

	<a class="rfi_button" data-open="widget" data-key="<?=$key?>" data-cost="<?=$cost?>" data-comment="<?=$order_id?>" data-name="<?=$name?>" data-orderid="0" data-email="<?=$email?>" href="#">
		<div style="width: 200px; height: 49px;background-image:url(https://partner.rficb.ru/gui/images/a1lite_buttons/button_small.png);"></div>
	</a><br />

	<script language="javascript" type="text/javascript">
		window.onload = function() {
			RFI.successFunction = function () {
			result.innerHTML += '<b>“спешно!</b> <br />';
		};
		RFI.errorFunction = function (reason) {
			result.innerHTML += '<b>Ћшибка</b>: ' + reason.title + '.\r\n' + reason.message + "<br />";
		};
		};
	</script>
<p id="result"></p>

<?
} 
else 
{
?>

	<script language="javascript" type="text/javascript">
		function ptype(paytype){
			document.getElementById("payment_type").value = paytype;  
		} 
	</script>

	<?
	if ($widget == "Y" && $widgettype ==1  && $ajax ==0) { ?>
	<script type="text/javascript" src="https://partner.rficb.ru/gui/js/jquery-1.11.0.min.js"></script>
	<script type="text/javascript" src="https://partner.rficb.ru/gui/js/widget_simplebutton.js"></script>
	<form method="POST" class="application alba_widget_simplebutton" accept-charset="UTF-8" action="https://partner.rficb.ru/alba/input/">
	<?
	} 
	else	
	{ 
	?>
	<form method="POST"  class="application"  accept-charset="UTF-8" action="https://partner.rficb.ru/a1lite/input" target="_blank">
	<?
	}
	?>
		<input type="hidden" name="key" value="<?echo $key?>" />
		<input type="hidden" name="cost" value="<?echo $cost?>" />
		<input type="hidden" name="name" value="<?echo $name?>" />
		<input type="hidden" name="default_email" value="<?echo $email?>" />
		<input type="hidden" name="order_id" value="<?echo $order_id?>" />
		<input type="hidden" name="comment" value="" />
		<?
		/*if ($com) {
			echo '<input type="hidden" name="commission" value="abonent" />';
			echo '<input type="hidden" name="version" value="2.0" />';
			} 
		*/
		if($paytype =='Y') {
			$i = 3;
			?>
			<input type="hidden" name="payment_type" value="spg" id="payment_type" />
			
			<div id="pay-methods">
				<div class="row">
			
				<?
				if($cart =='Y') {
				?>					
					<div class="col-xs-<?php echo $i;?> pay-method spg">
						<input type="radio" name="pay_type" id="pay-method-spg" value="spg" checked="" onclick="ptype('spg')">
						<label for="pay-method-spg"><span>Visa / MasterCard</span></label>
					</div>
				<?
				}
				
				if ($email) { 
					if ($wm =='Y') {
				?>
						<div class="col-xs-<?php echo $i;?> pay-method wm">
							<input type="radio" name="pay_type" id="pay-method-wm" value="wm" onclick="ptype('wm')">
							<label for="pay-method-wm"><span>WebMoney</span></label>
						</div> 
					<?
					}
					
					if ($ym =='Y') {
					?>
						<div class="col-xs-<?php echo $i;?> pay-method ym">
							<input type="radio" name="pay_type" id="pay-method-ym" value="ym" onclick="ptype('ym')">
							<label for="pay-method-ym"><span>Яндекс. Деньги</span></label>
						</div>
					<? 
					} 
				}
				
				if ($phone) {
					if ($mc =='Y') {
					?>
						<input type="hidden" name="phone_number" value="<?echo $phone?>" />
							<div class="col-xs-<?php echo $i;?> pay-method mc">
							<input type="radio" name="pay_type" id="pay-method-mc" value="mc" onclick="ptype('mc')">
							<label for="pay-method-mc"><span>Мобильный платёж</span></label>
						</div>
					<? 
					}
					if ($qiwi =='Y') {
					?>
						<div class="col-xs-<?php echo $i;?> pay-method qiwi">
							<input type="radio" name="pay_type" id="pay-method-qiwi" value="qiwi" onclick="ptype('qiwi')">
							<label for="pay-method-qiwi"><span>QIWI</span></label>
						</div>
					<?
					}
				} ?>
				
				</div>
			</div>
          
		<?
		}
		?>
		<?=GetMessage("RFICB.PAYMENT_FORM_SUBMIT")?>
	</form>
<?
}