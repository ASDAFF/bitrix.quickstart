<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

/** @var array $arCurrentValues */

use Bitrix\Main,
	 Bitrix\Main\Loader,
	 Bitrix\Catalog,
	 Bitrix\Iblock,
	 Bitrix\Sale\Internals,
	 Bitrix\Sale\PaySystem,
	 Bitrix\Sale\Delivery;

if(!Loader::includeModule('sale'))
	return;


//echo "<pre>"; print_r($siteId);echo "</pre>";
//$tttfile = dirname(__FILE__) . '/1_txt.php';
//file_put_contents($tttfile, "<pre>" . print_r($arPersonTypes, 1) . "</pre>\n");


/* PERSON_TYPE_ID */
$arPersonTypes = array();
$personTypes   = Internals\PersonTypeTable::getList()->fetchAll();
foreach($personTypes as $personType) {
	$arPersonTypes[ $personType["ID"] ] = '[' . $personType["ID"] . '] ' . $personType["NAME"];
}


/* PAY_SYSTEM_ID */
$arPaySystems = array();
$rsPaySystems = PaySystem\Manager::getList(array(
	 'filter' => array('=ACTIVE' => 'Y'),
));
while($arPaySystem = $rsPaySystems->fetch()) {
	$arPaySystems[ $arPaySystem["ID"] ] = '[' . $arPaySystem["ID"] . '] ' . $arPaySystem["NAME"];
}


/* DELIVERY_ID */
$arDeliveries = array();
$rsDeliveries = Delivery\Services\Table::getList();
while($arDelivery = $rsDeliveries->fetch()) {
	$arDeliveries[ $arDelivery["ID"] ] = '[' . $arDelivery["ID"] . '] ' . $arDelivery["NAME"];
}


$arComponentParameters = array(
	 "GROUPS"     => array(
			"MESS_NEW" => array(
				 "NAME" => GetMessage("GROUPS_MESS_NEW"),
			),
	 ),
	 "PARAMETERS" => array(
		 //NEW
		 "ORDER_USER_ID"  => array(
				"NAME"   => GetMessage("ORDER_USER_ID"),
				"TYPE"   => "STRING",
				"COLS"   => 60,
				"PARENT" => "BASE",
		 ),
		 "PERSON_TYPE_ID" => array(
				"NAME"              => GetMessage("PERSON_TYPE_ID"),
				"VALUES"            => $arPersonTypes,
				"TYPE"              => "LIST",
				"MULTIPLE"          => "N",
				"DEFAULT"           => "",
				"ADDITIONAL_VALUES" => "N",
				//'REFRESH'           => 'Y',
				"PARENT"            => "BASE",
		 ),
		 "PAY_SYSTEM_ID"  => array(
				"NAME"              => GetMessage("PAY_SYSTEM_ID"),
				"VALUES"            => $arPaySystems,
				"TYPE"              => "LIST",
				"MULTIPLE"          => "N",
				"DEFAULT"           => "",
				"ADDITIONAL_VALUES" => "N",
				"PARENT"            => "BASE",
		 ),
		 "DELIVERY_ID"    => array(
				"NAME"              => GetMessage("DELIVERY_ID"),
				"VALUES"            => $arDeliveries,
				"TYPE"              => "LIST",
				"MULTIPLE"          => "N",
				"DEFAULT"           => "",
				"ADDITIONAL_VALUES" => "N",
				"PARENT"            => "BASE",
		 ),

		 "MESS_SUBMIT_TEXT_DEFAULT"    => array(
				"NAME"    => GetMessage("MESS_SUBMIT_TEXT_DEFAULT"),
				"DEFAULT" => GetMessage("MESS_SUBMIT_TEXT_DEFAULT_VALUE"),
				"TYPE"    => "STRING",
				"COLS"    => 60,
				"PARENT"  => "MESS_NEW",
		 ),
		 "MESS_SUBMIT_TEXT_AJAX"       => array(
				"NAME"    => GetMessage("MESS_SUBMIT_TEXT_AJAX"),
				"DEFAULT" => GetMessage("MESS_SUBMIT_TEXT_AJAX_VALUE"),
				"TYPE"    => "STRING",
				"COLS"    => 60,
				"PARENT"  => "MESS_NEW",
		 ),
		 "MESS_BACK_LINK"              => array(
				"NAME"    => GetMessage("MESS_BACK_LINK"),
				"DEFAULT" => GetMessage("MESS_BACK_LINK_VALUE"),
				"TYPE"    => "STRING",
				"COLS"    => 60,
				"PARENT"  => "MESS_NEW",
		 ),
		 "MESS_COST_TEXT"              => array(
				"NAME"    => GetMessage("MESS_COST_TEXT"),
				"DEFAULT" => GetMessage("MESS_COST_TEXT_VALUE"),
				"TYPE"    => "STRING",
				"COLS"    => 60,
				"PARENT"  => "MESS_NEW",
		 ),
		 "MESS_ORDER_PROP_BLOCK_TITLE" => array(
				"NAME"    => GetMessage("MESS_ORDER_PROP_BLOCK_TITLE"),
				"DEFAULT" => GetMessage("MESS_ORDER_PROP_BLOCK_TITLE_VALUE"),
				"TYPE"    => "STRING",
				"COLS"    => 60,
				"PARENT"  => "MESS_NEW",
		 ),
		 "MESS_BASKET_BLOCK_TITLE"     => array(
				"NAME"    => GetMessage("MESS_BASKET_BLOCK_TITLE"),
				"DEFAULT" => GetMessage("MESS_BASKET_BLOCK_TITLE_VALUE"),
				"TYPE"    => "STRING",
				"COLS"    => 60,
				"PARENT"  => "MESS_NEW",
		 ),
		 "MESS_BASKET_SHOW"            => array(
				"NAME"    => GetMessage("MESS_BASKET_SHOW"),
				"DEFAULT" => GetMessage("MESS_BASKET_SHOW_VALUE"),
				"TYPE"    => "STRING",
				"COLS"    => 60,
				"PARENT"  => "MESS_NEW",
		 ),
		 "MESS_BASKET_HIDE"            => array(
				"NAME"    => GetMessage("MESS_BASKET_HIDE"),
				"DEFAULT" => GetMessage("MESS_BASKET_HIDE_VALUE"),
				"TYPE"    => "STRING",
				"COLS"    => 60,
				"PARENT"  => "MESS_NEW",
		 ),
		 "MESS_PROP_COMMENT"           => array(
				"NAME"    => GetMessage("MESS_PROP_COMMENT"),
				"DEFAULT" => GetMessage("MESS_PROP_COMMENT_VALUE"),
				"TYPE"    => "STRING",
				"COLS"    => 60,
				"PARENT"  => "MESS_NEW",
		 ),
		 "MESS_PROP_COMMENT_LINK"      => array(
				"NAME"    => GetMessage("MESS_PROP_COMMENT_LINK"),
				"DEFAULT" => GetMessage("MESS_PROP_COMMENT_LINK_VALUE"),
				"TYPE"    => "STRING",
				"COLS"    => 60,
				"PARENT"  => "MESS_NEW",
		 ),
		 "MESS_PRIVACY_POLICY"         => array(
				"NAME"    => GetMessage("MESS_PRIVACY_POLICY"),
				"DEFAULT" => GetMessage("MESS_PRIVACY_POLICY_VALUE"),
				"TYPE"    => "STRING",
				"COLS"    => 60,
				"PARENT"  => "MESS_NEW",
		 ),

		 //OLD
		 "TEMPLATE_LOCATION"           => array(
				"NAME"              => GetMessage("TEMPLATE_LOCATION"),
				"TYPE"              => "LIST",
				"MULTIPLE"          => "N",
				"VALUES"            => GetMessage('TEMPLATE_LOCATION_VALUES'),
				"DEFAULT"           => "popup",
				"ADDITIONAL_VALUES" => "N",
				"PARENT"            => "BASE",
		 ),
		 "ALLOW_USER_PROFILES"         => array(
				"NAME"    => GetMessage("ALLOW_USER_PROFILES"),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "N",
				"REFRESH" => "Y",
				"PARENT"  => "BASE",
		 ),
		 "ALLOW_NEW_PROFILE"           => array(
				"NAME"    => GetMessage("ALLOW_NEW_PROFILE"),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "N",
				"HIDDEN"  => $arCurrentValues['ALLOW_USER_PROFILES'] !== 'Y' ? 'Y' : 'N',
				"PARENT"  => "BASE",
		 ),
		 "ALLOW_AUTO_REGISTER"         => array(
				"NAME"    => GetMessage("SOA_ALLOW_AUTO_REGISTER"),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "N",
				"PARENT"  => "BASE",
		 ),
		 "DISABLE_BASKET_REDIRECT"     => array(
				"NAME"    => GetMessage('SOA_DISABLE_BASKET_REDIRECT2'),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "Y",
		 ),
		 "SET_TITLE"                   => array(),
	 ),
);
?>
<style type="text/css">
	.bxcompprop-content-table textarea{
		-webkit-box-sizing: border-box !important; -moz-box-sizing: border-box !important; box-sizing: border-box !important;
		width: 90% !important;
		min-height: 60px !important;
	}
</style>
