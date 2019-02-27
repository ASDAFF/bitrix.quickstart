<?
global $MESS;

$MESS['PAYANYWAY_SBERBANK_TITLE'] 	= 'Sberbank';
	
$MESS['PAW_INVOICE_CREATED_TTL']	= 'Invoice was created.'; 
$MESS['PAW_INVOICE_ERROR_TTL']		= 'Error occured during creating invoice.';
$MESS['PAW_INVOICE_CREATED']		= "<h3>Contract number for Sberbank is: %transaction%</h3>
									   <p>Transaction is registered. Please proceed payment with Sberbank using following <b>MONETA.RU</b> account number:</p>
									   <p>%transaction%</p>
									   <p>Or click on <a href='https://online.sberbank.ru/PhizIC/private/payments/servicesPayments/edit.do?recipient=113368&amp;field(_TCM_IDENT_WlsZid1)=%transaction%'>link</a> to pay by SberbankOnline.</p>
										<p>Total amount: %amount%</p>
										<p>External commission: %fee%</p>";
?>