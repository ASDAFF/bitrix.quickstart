<?php

/** @global CMain $APPLICATION */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

$module_id = 'admitad.tracking';
\Bitrix\Main\Loader::includeModule($module_id);

Loc::loadMessages(__FILE__);

if ($APPLICATION->GetGroupRight($module_id) < "S") {
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}

$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();
$admitad = new \Admitad\Tracking\Admitad\Admitad();
$admitadRevision = new \Admitad\Tracking\Admitad\AdmitadRevision();
$admitadOrder = new \Admitad\Tracking\Admitad\AdmitadOrder();

$ignoreOptions = array();

$aActionsSubTabs = array();

$aTabs = array(
	array(
		'DIV'     => 'edit1',
		'TAB'     => Loc::getMessage('ADMITAD_TRACKING_TAB_SETTINGS'),
		'TITLE'   => Loc::getMessage('ADMITAD_TRACKING_TAB_SETTINGS'),
		'OPTIONS' => array(
			array(
				'CLIENT_ID', Loc::getMessage('ADMITAD_TRACKING_TAB_SETTINGS_CLIENT_ID'),
				'',
				array('text', 50),
			),
			array(
				'CLIENT_SECRET', Loc::getMessage('ADMITAD_TRACKING_TAB_SETTINGS_CLIENT_SECRET'),
				'',
				array('text', 50),
			),
			array(
				'PARAM_NAME', Loc::getMessage('ADMITAD_TRACKING_TAB_SETTINGS_PARAM_NAME'),
				'',
				array('text', 50),
			),
		),
	),
);

$subTabControl = new CAdminViewTabControl("subTabControl", $aActionsSubTabs);
#Сохранение

if ($request->isPost() && $request->getPost('Update') && check_bitrix_sessid()) {

	$admitad
		->setClientId($request->getPost('CLIENT_ID'))
		->setClientSecret($request->getPost('CLIENT_SECRET'));
	try {
		$response = $admitad->authorizeClient();
		$data = $response->getArrayResult();
	} catch (Exception $e) {
		$admitad->revokeKeys();
		CAdminMessage::ShowMessage(Loc::getMessage('ADMITAD_TRACKING_AUTH_ERROR'));
	}

	if ($admitad->getToken() && $admitad->getRefreshToken()) {

		foreach ($request->getPostList()->toArray() as $param => $value) {
			if (preg_match('/REVISION_(?<revision>[a-zA-Z_]*)/', $param, $matches)) {
				if ($matches['revision'] == 'PATH') {
					\Admitad\Tracking\Admitad\AdmitadRevision::updateRevisionPathRule($request->getPost($param));
				}
				Option::set($module_id, 'REVISION_' . $matches['revision'], $request->getPost($param));
			}
		}

		if ($request->getPost('actions')) {
			$admitad->setConfiguration($request->getPost('actions'));
		}

		$admitad->setParamName($request->getPost('PARAM_NAME'));
	}
}


if ($admitad->getToken() && $admitad->getRefreshToken()) {
	$campaign = $admitad->getAdvertiserInfo();
	$admitad->setCampaignCode($campaign['campaign_code'])
		->setPostbackKey($campaign['postback_key']);
	$orderTypes = \Admitad\Tracking\Admitad\AdmitadOrder::getOrderTypes();
	$sections = \Admitad\Tracking\Admitad\AdmitadOrder::getIBlockSections();
	$configuration = \Bitrix\Main\Web\Json::decode(\Admitad\Tracking\Admitad\Admitad::getConfiguration());
	foreach ($orderTypes as $type => $title) {
		$params = array();
		foreach ($campaign['actions'] as $action) {
			if (empty($action['tariffs'])) {
				continue;
			}

			$actionConfig = isset($configuration[$type][$action['action_code']]) ? $configuration[$type][$action['action_code']] : array(
				'type'    => null,
				'tariffs' => array(),
			);

			$params[] = $action['action_name'] . ' (<b>' . $action['action_code'] . '</b>)';
			$params[] = array(
				'actions[' . $type . '][' . $action['action_code'] . '][type]', Loc::getMessage('ADMITAD_TRACKING_TAB_ACTIONS_ACTION_TYPE'),
				$actionConfig['type'],
				array(
					'selectbox',
					array(
						0 => Loc::getMessage('ADMITAD_TRACKING_TAB_ACTIONS_ACTION_TYPE_INACTIVE'),
						1 => Loc::getMessage('ADMITAD_TRACKING_TAB_ACTIONS_ACTION_TYPE_SALE'),
					),
				),
			);
			foreach ($action['tariffs'] as $tariff) {
				$tariffConfig = isset($actionConfig['tariffs'][$tariff['tariff_code']]) ? $actionConfig['tariffs'][$tariff['tariff_code']] : array(
					'categories' => array(),
				);
				$params[] = array(
					'actions[' . $type . '][' . $action['action_code'] . '][tariffs][' . $tariff['tariff_code'] . '][categories]', $tariff['tariff_name'] . ' (<b>' . $tariff['tariff_code'] . '</b>)',
					implode(',', array_values($tariffConfig['categories'])),
					array(
						'multiselectbox',
						$sections,
					),
				);
			}
		}
		$aActionsSubTabs[] = array(
			"DIV"     => "type" . $type,
			"TAB"     => $title,
			"TITLE"   => $title,
			'OPTIONS' => $params,
		);
	}

	$subTabControl = new CAdminViewTabControl("subTabControl", $aActionsSubTabs);

	array_push($aTabs, array(
		"DIV"   => "edit2",
		"TAB"   => Loc::getMessage("ADMITAD_TRACKING_TAB_ACTIONS"),
		"TITLE" => Loc::getMessage("ADMITAD_TRACKING_TAB_ACTIONS"),
	));

	array_push($aTabs, array(
		"DIV"   => "edit3",
		"TAB"   => Loc::getMessage("ADMITAD_TRACKING_TAB_REVISION"),
		"TITLE" => Loc::getMessage("ADMITAD_TRACKING_TAB_REVISION"),
		"OPTIONS" => array(
			array('REVISION_PATH', Loc::getMessage("ADMITAD_TRACKING_TAB_REVISION_PATH"), '/admitad/admitad.xml', array('text', '100%')),
			array('REVISION_LOGIN', Loc::getMessage("ADMITAD_TRACKING_TAB_REVISION_LOGIN"), '', array('text', '100%')),
			array('REVISION_PASSWORD', Loc::getMessage("ADMITAD_TRACKING_TAB_REVISION_PASSWORD"), '', array('text', '100%')),
			array('REVISION_STATUS_APPROVED', Loc::getMessage("ADMITAD_TRACKING_TAB_REVISION_STATUS_APPROVED"), '', array('selectbox', $admitadRevision->getStatuses())),
			array('REVISION_STATUS_DECLINED', Loc::getMessage("ADMITAD_TRACKING_TAB_REVISION_STATUS_DECLINED"), '', array('selectbox', $admitadRevision->getStatuses())),
//			array('REVISION_PERSON_TYPE', Loc::getMessage("ADMITAD_TRACKING_TAB_REVISION_PERSON_TYPE"), '', array('selectbox', $admitadRevision->getPersonTypes())),
		),
	));
}

$tabControl = new CAdminTabControl('tabControl', $aTabs);

?>

<? $tabControl->Begin(); ?>
<form method='post' action='<? echo $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($request['mid']) ?>&amp;lang=<?= $request['lang'] ?>' name='admitad_tracking_settings' ENCTYPE="multipart/form-data">
<? $tabControl->BeginNextTab(); ?>
	<? __AdmSettingsDrawList($module_id, $aTabs[0]['OPTIONS']); ?>
	<?php if (isset($aTabs[1])) : ?>
		<? $tabControl->BeginNextTab(); ?>
		<tr>
			<td colspan="2">
				<?php $subTabControl->Begin(); ?>
				<?php foreach ($aActionsSubTabs as $aActionsSubTab): ?>
					<?php $subTabControl->BeginNextTab(); ?>
					<?php if ($aActionsSubTab['OPTIONS']): ?>
						<table class="adm-detail-content-table edit-table">
							<? __AdmSettingsDrawList($module_id, $aActionsSubTab['OPTIONS']); ?>
						</table>
					<?php endif; ?>
				<?php endforeach; ?>
				<?php $subTabControl->End(); ?>
			</td>
		</tr>
	<?php endif; ?>
	<?php if (isset($aTabs[2])) : ?>
		<? $tabControl->BeginNextTab(); ?>
		<? __AdmSettingsDrawList($module_id, $aTabs[2]['OPTIONS']); ?>
	<?php endif; ?>
	<? $tabControl->Buttons(); ?>

	<input type="submit" name="Update" value="<? echo GetMessage('MAIN_SAVE') ?>">
	<!--	<input type="reset" name="reset" value="--><? // echo GetMessage('MAIN_RESET') ?><!--">-->
	<?= bitrix_sessid_post(); ?>
</form>
<? $tabControl->End(); ?>
