<?if ( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true )
{
	die();
}
include dirname(__FILE__) . "/PayU_Bitrix.cls.php";
include( GetLangFileName(dirname(__FILE__) . "/", "/.description.php") );

if ( isset( $arResult['ORDER_ID'] ) )
{
	$ORDER_ID = $arResult['ORDER_ID'];

} else if ( isset( $arResult['ID'] ) ) {

    $ORDER_ID = $arResult['ID'];

} else {

    $ORDER_ID = (int) $_GET['ORDER_ID'];
}

#------------------------------------------------
# Recive all items data
#------------------------------------------------

$arBasketItems = array();

$dbBasketItems = CSaleBasket::GetList(array(
	"NAME" => "ASC",
	"ID"   => "ASC"
), array(
	"LID"      => SITE_ID,
	"ORDER_ID" => $ORDER_ID
), false, false, array(
	"ID",
	"NAME",
	"CALLBACK_FUNC",
	"MODULE",
	"PRODUCT_ID",
	"QUANTITY",
	"DELAY",
	"VAT_RATE",
	"CAN_BUY",
	"PRICE",
	"WEIGHT"
));

while ( $arItems = $dbBasketItems->Fetch() )
{
	if ( strlen($arItems["CALLBACK_FUNC"]) > 0 )
	{
		CSaleBasket::UpdatePrice($arItems["ID"], $arItems["CALLBACK_FUNC"], $arItems["MODULE"], $arItems["PRODUCT_ID"], $arItems["QUANTITY"]);
		$arItems = CSaleBasket::GetByID($arItems["ID"]);
	}
	$arBasketItems[] = $arItems;
}

if (count($arBasketItems) == 0) {
	$basket = CSaleBasket::GetList(array("ID" => "ASC"), array("ORDER_ID" => $ORDER_ID));
}

#--------------------------------------------
$arOrder = CSaleOrder::GetByID($ORDER_ID);
$db_res  = CSaleOrderPropsValue::GetList(( $b = "" ), ( $o = "" ), array( "ORDER_ID" => $ORDER_ID ));

if ($arOrder["PAYED"] == "Y") {
	die();
}

while ( $ar_res = $db_res->Fetch() )
{
	$arCurOrderProps[( strlen($ar_res["CODE"]) > 0 ) ? $ar_res["CODE"] : $ar_res["ID"]] = $ar_res["VALUE"];
}

$option = array(
	'merchant'    => CSalePaySystemAction::GetParamValue("MERCHANT"),
	'secretkey'   => CSalePaySystemAction::GetParamValue("SECURE_KEY"),
	'debug'       => CSalePaySystemAction::GetParamValue("DEBUG_MODE"),
	'encoding'    => SITE_CHARSET,
	'isWinEncode' => strpos(strtolower(SITE_CHARSET), 'utf' ) ? false : true
);



$lu     = CSalePaySystemAction::GetParamValue("LU_URL");
if ( !empty($lu) )
{
	$option['luUrl'] = $lu;
}

$orderID = "PayuOrder_" . $ORDER_ID . "_" . CSaleBasket::GetBasketUserID() . "_" . md5("payuOrder_" . time());


$forSend = array(
	'AUTOMODE'        => CSalePaySystemAction::GetParamValue("AUTOMODE"), # 1 or 0
	'ORDER_REF'       => $orderID, # Uniqe order
	'ORDER_DATE'      => date("Y-m-d H:i:s"), # Date of paying ( Y-m-d H:i:s )
	'ORDER_SHIPPING'  => $arOrder['PRICE_DELIVERY'],
	'PRICES_CURRENCY' => CSalePaySystemAction::GetParamValue("PRICE_CURRENCY"), # Currency
	'DISCOUNT'        => $arOrder['DISCOUNT_VALUE'],
	'LANGUAGE'        => CSalePaySystemAction::GetParamValue("LANGUAGE"),
);

if ( $forSend['DISCOUNT'] == 0 )
{
	unset( $forSend['DISCOUNT'] );
}

$backref = CSalePaySystemAction::GetParamValue("BACK_REF");
if ( !empty( $backref ) )
{
	$forSend['BACK_REF'] = $backref;
}

$useVat = CSalePaySystemAction::GetParamValue("USE_VAT");
$vatRate = CSalePaySystemAction::GetParamValue("VAT_RATE");

if (empty($useVat)) {
    $useVat = 'NET';
}

foreach ( $arBasketItems as $val )
{
	if ($val['VAT_RATE'] != '0.00') {
		$useVat = 'GROSS';
		$vatRate = '19';
	}
	
	$forSend['ORDER_PNAME'][] = $val['NAME'];
	$forSend['ORDER_PCODE'][] = $val['PRODUCT_ID'];
	$forSend['ORDER_PINFO'][] = "";
	$forSend['ORDER_PRICE'][] = $val['PRICE'];
	$forSend['ORDER_QTY'][]   = $val['QUANTITY'];
	$forSend['ORDER_VAT'][]   = $vatRate;
    $forSend['ORDER_PRICE_TYPE'][] = $useVat;
}
?>
<?
global $USER;
$userData = $USER->GetByID($USER->GetID())->Fetch();

$user_phone = !empty($userData['PERSONAL_PHONE'])?$userData['PERSONAL_PHONE']:$userData['PERSONAL_MOBILE'];

if(!empty($arCurOrderProps['PHONE']))
	$user_phone = $arCurOrderProps['PHONE'];

$arExtFields = array(
	array(
		'name'  => 'AUTOMODE',
		'type'  => 'hidden',
		'value' => 1
	),
	array(
		'name'  => 'BILL_FNAME',
		'type'  => 'hidden',
		'attributes'    => 'class="PayU_Fields_Automode_Item"',
		'value' => $USER->GetFirstName(),
	),
	array(
		'name'  => 'BILL_LNAME',
		'type'  => 'hidden',
		'value' => $USER->GetLastName(),
	),
	array(
		'name'  => 'BILL_EMAIL',
		'type'  => 'hidden',
		'value' => $USER->GetEmail(),
	),
	array(
		'name'  => 'BILL_PHONE',
		'type'  => 'hidden',
		'value' => preg_replace('/[^\d]+/','',$user_phone),
	),
);

$pay = PayU_Bitrix::getInst()
                  ->setOptions($option)
                  ->setData($forSend)
                  ->LU();
if(CSalePaySystemAction::GetParamValue('AUTOMODE') == 'Y')
	echo $pay->getForm($arExtFields);
else
	echo $pay->getForm();
?>
<style>
	input.goPayment {
		display: block;
		width: 300px;
		height: 50px;
		font-size: 18px;
		text-align: center;
	}
</style>