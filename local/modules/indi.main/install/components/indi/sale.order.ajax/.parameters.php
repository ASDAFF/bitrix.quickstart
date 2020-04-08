<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$propsGroups = array();
$paySystems = array();
if (CModule::IncludeModule('sale')) {
	$propsGroupsRecordset = CSaleOrderPropsGroup::GetList(
		array(
			'SORT' => 'ASC',
		),
		array(),
		false,
		false,
		array(
			'ID',
			'NAME',
		)
	);
	while ($propsGroup = $propsGroupsRecordset->GetNext()) {
		$propsGroups[$propsGroup['ID']] = $propsGroup['NAME'];
	}
	
	$paySystemsRecordset = CSalePaySystem::GetList(
		array(
			'SORT' => 'ASC',
			'PSA_NAME' => 'ASC'
		),
		array(
			'ACTIVE' => 'Y',
			'PSA_HAVE_PAYMENT' => 'Y',
		)
	);
	while ($paySystem = $paySystemsRecordset->Fetch()) {
		$paySystems[$paySystem['ID']] = $paySystem['NAME'];
	}
}

$arComponentParameters = array(
	'PARAMETERS' => array(
		'SET_TITLE' => array(),
		'DELIVERY_TO_PAYSYSTEM' => array(
			'NAME' => GetMessage('SBB_DELIVERY_PAYSYSTEM'),
			'TYPE' => 'LIST',
			'VALUES' => array(
				'd2p' => GetMessage('SBB_TITLE_PD'),
				'p2d' => GetMessage('SBB_TITLE_DP')
			),
			'PARENT' => 'BASE',
		),
		'DELIVERY_NO_AJAX' => array(
			'NAME' => GetMessage('SOA_DELIVERY_NO_AJAX'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'PARENT' => 'BASE',
		),
		'PAY_FROM_ACCOUNT' => array(
			'NAME'=>GetMessage('SOA_ALLOW_PAY_FROM_ACCOUNT'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT'=>'Y',
			'PARENT' => 'BASE',
		),
		'ONLY_FULL_PAY_FROM_ACCOUNT' => array(
			'NAME'=>GetMessage('SOA_ONLY_FULL_PAY_FROM_ACCOUNT'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT'=>'N',
			'PARENT' => 'BASE',
		),
		'USE_PREPAYMENT' => array(
			'NAME' => GetMessage('SBB_USE_PREPAYMENT'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'ADDITIONAL_VALUES'=>'N',
			'PARENT' => 'BASE',
		),
		'SEND_NEW_USER_NOTIFY' => array(
			'NAME'=>GetMessage('SOA_SEND_NEW_USER_NOTIFY'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT'=>'Y',
			'PARENT' => 'BASE',
		),
		'PATH_TO_BASKET' => array(
			'NAME' => GetMessage('SOA_PATH_TO_BASKET'),
			'TYPE' => 'STRING',
			'DEFAULT' => '/',
			'PARENT' => 'ADDITIONAL_SETTINGS',
		),
		'DISABLE_BASKET_REDIRECT' => array(
			'NAME' => GetMessage('SOA_DISABLE_BASKET_REDIRECT'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'PARENT' => 'ADDITIONAL_SETTINGS',
		),
		'PATH_TO_CATALOG' => array(
			'NAME' => GetMessage('SOA_PATH_TO_CATALOG'),
			'TYPE' => 'STRING',
			'DEFAULT' => '/catalog/',
			'PARENT' => 'ADDITIONAL_SETTINGS',
		),
		'PATH_TO_PAYMENT' => array(
			'NAME' => GetMessage('SOA_PATH_TO_PAYMENT'),
			'TYPE' => 'STRING',
			'DEFAULT' => '/',
			'PARENT' => 'ADDITIONAL_SETTINGS',
		),
		'PATH_TO_PERSONAL' => array(
			'NAME' => GetMessage('SOA_PATH_TO_PERSONAL'),
			'TYPE' => 'STRING',
			'DEFAULT' => '/',
			'PARENT' => 'ADDITIONAL_SETTINGS',
		),
		'PATH_TO_ORDERS_LIST' => array(
			'NAME' => GetMessage('SOA_PATH_TO_ORDERS_LIST'),
			'TYPE' => 'STRING',
			'DEFAULT' => '/',
			'PARENT' => 'ADDITIONAL_SETTINGS',
		),
		'DELIVERY_GROUPS' => array(
			'NAME' => GetMessage('SOA_DELIVERY_GROUPS'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'Y',
			'COLS' => 25,
			'SIZE' => 7,
			'VALUES' => $propsGroups,
			'ADDITIONAL_VALUES' => 'N',
			'PARENT' => 'ADDITIONAL_SETTINGS',
		),
		'PAY_SYSTEMS_ONLINE' => array(
			'NAME' => GetMessage('SOA_PAY_SYSTEMS_ONLINE'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'Y',
			'COLS' => 25,
			'SIZE' => 7,
			'VALUES' => $paySystems,
			'ADDITIONAL_VALUES' => 'N',
			'PARENT' => 'ADDITIONAL_SETTINGS',
		),
	)
);