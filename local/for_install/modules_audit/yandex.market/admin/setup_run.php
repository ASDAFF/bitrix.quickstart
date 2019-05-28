<?php

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Yandex\Market;

define('BX_SESSION_ID_CHANGE', false);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';

Loc::loadMessages(__FILE__);

if (!$USER->IsAdmin())
{
	$APPLICATION->AuthForm(Loc::getMessage('YANDEX_MARKET_ACCESS_DENIED'));

	return;
}
else if (!Main\Loader::includeModule('yandex.market'))
{
	\CAdminMessage::ShowMessage([
		'TYPE' => 'ERROR',
		'MESSAGE' => Loc::getMessage('YANDEX_MARKET_MODULE_NOT_INSTALLED')
	]);

	return;
}

$request = Main\Context::getCurrent()->getRequest();
$actionMessage = '';

// action process

$requestAction = $request->get('action');

if ($requestAction)
{
	$response = [
		'status' => 'error',
		'message' => null
	];

	try
	{
		if (!check_bitrix_sessid())
		{
			throw new Main\SystemException('ADMIN_SETUP_RUN_ACTION_SESSION_EXPIRED');
		}

		session_write_close(); // release session

		/** @var \Yandex\Market\Export\Setup\Model $setup */
		$setupId = (int)$request->getPost('SETUP_ID');
		$setup = Market\Export\Setup\Model::loadById($setupId);
		$initTimestamp = $request->getPost('INIT_TIME');
		$initTime = (
			$initTimestamp !== null
				? Main\Type\DateTime::createFromTimestamp($initTimestamp)
				: new Main\Type\DateTime()
		);

		$processor = new Market\Export\Run\Processor($setup, [
			'step' => $request->getPost('STEP'),
			'stepOffset' => $request->getPost('STEP_OFFSET'),
			'stepTotalCount' => $request->getPost('STEP_TOTAL_COUNT'),
			'progressCount' => true,
			'timeLimit' => $request->getPost('TIME_LIMIT'),
			'initTime' => $initTime,
			'usePublic' => false
		]);

		switch ($requestAction)
		{
			case 'run':

				if ($request->getPost('STEP') === null) // is first request
				{
					if ($setup->hasFullRefresh())
					{
						$setup->handleRefresh(false);
					}
				}

				$processResult = $processor->run();

				if ($processResult->isFinished())
				{
					if ($setup->hasFullRefresh())
					{
						$setup->handleRefresh(true);
					}

					if ($setup->isAutoUpdate())
					{
						$setup->handleChanges(true);
					}

					$adminMessage = new CAdminMessage(array(
						'MESSAGE' => Market\Config::getLang('ADMIN_SETUP_RUN_ACTION_RUN_SUCCESS_TITLE'),
						'DETAILS' => Market\Config::getLang('ADMIN_SETUP_RUN_ACTION_RUN_SUCCESS_DETAILS', [
							'#URL#' => $setup->getFileRelativePath()
						]),
						'TYPE' => 'OK',
						'HTML' => true
					));

					$response['status'] = 'ok';
					$response['message'] = $adminMessage->Show();

					$response['message'] .= '<div class="b-admin-text-message">';
					$response['message'] .= '<input type="text" value="' . htmlspecialcharsbx($setup->getFileUrl()) . '" size="50" /> ';
					$response['message'] .= '<button class="adm-btn js-plugin-click" type="button" data-plugin="Ui.Input.CopyClipboard">' . Market\Config::getLang('ADMIN_SETUP_RUN_ACTION_RUN_COPY_LINK') . '</button>';
					$response['message'] .= '</div>';

					// log

					$queryLog = Market\Logger\Table::getList([
						'filter' => [
							'=ENTITY_TYPE' => [
								Market\Logger\Table::ENTITY_TYPE_EXPORT_RUN_ROOT,
								Market\Logger\Table::ENTITY_TYPE_EXPORT_RUN_OFFER,
								Market\Logger\Table::ENTITY_TYPE_EXPORT_RUN_CATEGORY,
								Market\Logger\Table::ENTITY_TYPE_EXPORT_RUN_CURRENCY,
							],
							'=ENTITY_PARENT' => $setupId
					 	],
						'select' => [ 'ENTITY_PARENT' ],
						'limit' => 1,
					]);

					if ($queryLog->fetch())
					{
						$logUrl = 'yamarket_log.php?' . http_build_query([
							'lang' => LANGUAGE_ID,
							'set_filter' => 'Y',
							'find_setup' => $setupId
						]);

						$response['message'] .=
							PHP_EOL
							. '<div class="b-admin-text-message">'
							. Market\Config::getLang('ADMIN_SETUP_RUN_ACTION_RUN_SUCCESS_LOG', [
								'#URL#' => htmlspecialcharsbx($logUrl)
							])
							. '</div>';
					}

					// publish note

					$response['message'] .= BeginNote('style="position: relative; top: -15px;"');
					$response['message'] .= Market\Config::getLang('ADMIN_SETUP_RUN_ACTION_RUN_SUCCESS_PUBLISH');
					$response['message'] .= EndNote();
				}
				else if ($processResult->isSuccess())
				{
					$readyCount = $processResult->getStepReadyCount();
					$readyCountMessage = '';

					if ($readyCount !== null)
					{
						$readyCountMessage ='<br />' . Market\Config::getLang('ADMIN_SETUP_RUN_ACTION_RUN_PROGRESS_READY_COUNT', [
							'#COUNT#' => (int)$readyCount
						]);
					}

					$adminMessage = new CAdminMessage(array(
						'TYPE' => 'PROGRESS',
						'MESSAGE' => Market\Config::getLang('ADMIN_SETUP_RUN_ACTION_RUN_PROGRESS_TITLE'),
						'DETAILS' => '#PROGRESS_BAR#' . $readyCountMessage,
						'HTML' => true,
						'PROGRESS_TOTAL' => 100,
						'PROGRESS_VALUE' => $processResult->getProgressPercent(),
					));

					$response['status'] = 'progress';
					$response['message'] = $adminMessage->Show();
					$response['state'] = [
						'STEP' => $processResult->getStep(),
						'STEP_OFFSET' => $processResult->getStepOffset(),
						'STEP_TOTAL_COUNT' => $processResult->getStepTotalCount(),
						'sessid' => bitrix_sessid(),
						'INIT_TIME' => $initTime->getTimestamp()
					];
				}
				else
				{
					$errorMessage = $processResult->hasErrors()
						? implode('<br />', $processResult->getErrorMessages())
						: Market\Config::getLang('ADMIN_SETUP_RUN_ACTION_RUN_ERROR_UNDEFINED');

					$adminMessage = new CAdminMessage(array(
						'TYPE' => 'ERROR',
						'MESSAGE' => Market\Config::getLang('ADMIN_SETUP_RUN_ACTION_RUN_ERROR_TITLE'),
						'DETAILS' => $errorMessage,
						'HTML' => true,
					));
					
					$response['status'] = 'error';
					$response['message'] = $adminMessage->Show();
				}

			break;

			case 'stop':			

				$processor->clear(true);

				if ($setup->hasFullRefresh())
				{
					$setup->handleRefresh(false);
				}

				if ($setup->isAutoUpdate())
				{
					$setup->handleChanges(false);
				}

				$response['status'] = 'ok';

			break;

			default:
				throw new Main\SystemException(
					Market\Config::getLang('ADMIN_SETUP_RUN_ACTION_NOT_FOUND')
				);
			break;
		}
	}
	catch (Main\SystemException $exception)
	{
		$adminMessage = new CAdminMessage(array(
			'TYPE' => 'ERROR',
			'MESSAGE' => $exception->getMessage()
		));

		$response['status'] = 'error';
		$response['message'] = $adminMessage->Show();
	}

	if ($request->isAjaxRequest())
	{
		$APPLICATION->RestartBuffer();
		echo Market\Utils::jsonEncode($response, JSON_UNESCAPED_UNICODE);
		die();
	}
	else
	{
		$actionMessage = $response['message'];
	}
}

// admin page

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';

// load form data

$requestSetup = (int)$request->get('id');
$setupList = [];

$querySetup = Market\Export\Setup\Table::getList([
	'select' => [ 'ID', 'NAME' ]
]);

while ($setup = $querySetup->fetch())
{
	$setupList[] = $setup;
}

if (empty($setupList))
{
	\CAdminMessage::ShowMessage([
		'TYPE' => 'ERROR',
		'MESSAGE' => Market\Config::getLang('ADMIN_SETUP_RUN_SETUP_LIST_EMPTY')
	]);

	return;
}

// form display

$APPLICATION->SetTitle(Market\Config::getLang('ADMIN_SETUP_RUN_TITLE'));

CJSCore::Init([ 'jquery' ]);

$APPLICATION->SetAdditionalCSS('/bitrix/css/yandex.market/base.css');

$APPLICATION->AddHeadScript('/bitrix/js/yandex.market/utils.js');
$APPLICATION->AddHeadScript('/bitrix/js/yandex.market/plugin/base.js');
$APPLICATION->AddHeadScript('/bitrix/js/yandex.market/plugin/manager.js');
$APPLICATION->AddHeadScript('/bitrix/js/yandex.market/ui/admin/exportform.js');
$APPLICATION->AddHeadScript('/bitrix/js/yandex.market/ui/input/copyclipboard.js');

Market\Metrika::reachGoal('generate_YML');

$tabs = [
	[ 'DIV' => 'common', 'TAB' => Market\Config::getLang('ADMIN_SETUP_RUN_TAB_COMMON') ]
];

$tabControl = new CAdminTabControl('YANDEX_MARKET_ADMIN_SETUP_RUN', $tabs, true, true);

?>
<form class="js-plugin" action="<?= $APPLICATION->GetCurPage() . '?lang=' . LANGUAGE_ID; ?>" method="post" data-plugin="Ui.Admin.ExportForm">
	<div class="js-export-form__message">
		<?= $actionMessage; ?>
	</div>
	<div class="b-admin-text-message is--hidden js-export-form__timer-holder">
		<?= Market\Config::getLang('ADMIN_SETUP_RUN_TIMER_LABEL'); ?>:
		<span class="js-export-form__timer">00:00</span>
	</div>
	<?
	$tabControl->Begin();

	echo bitrix_sessid_post();

	// common tab

	$tabControl->BeginNextTab([ 'showTitle' => false ]);

	?>
	<tr>
		<td width="40%" align="right"><?= Market\Config::getLang('ADMIN_SETUP_RUN_FIELD_SETUP_ID'); ?>:</td>
		<td width="60%">
			<select name="SETUP_ID">
				<?
				foreach ($setupList as $setup)
				{
					?>
					<option value="<?= $setup['ID']; ?>" <?= (int)$setup['ID'] === $requestSetup ? 'selected' : ''; ?>>[<?= $setup['ID']; ?>] <?= $setup['NAME']; ?></option>
					<?
				}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td width="40%" align="right"><?= Market\Config::getLang('ADMIN_SETUP_RUN_FIELD_TIME_LIMIT'); ?>:</td>
		<td>
			<input type="text" name="TIME_LIMIT" value="30" size="2" />
			<?= Market\Config::getLang('ADMIN_SETUP_RUN_FIELD_TIME_LIMIT_UNIT'); ?>
			<?= Market\Config::getLang('ADMIN_SETUP_RUN_FIELD_TIME_LIMIT_SLEEP'); ?>
			<input type="text" name="TIME_SLEEP" value="3" size="2" />
			<?= Market\Config::getLang('ADMIN_SETUP_RUN_FIELD_TIME_LIMIT_UNIT'); ?>
		</td>
	</tr>
	<?

	// buttons

	$tabControl->Buttons();

	?>
	<input type="button" class="adm-btn adm-btn-save js-export-form__run-button" value="<?= Market\Config::getLang('ADMIN_SETUP_RUN_BUTTON_START'); ?>" />
	<input type="button" class="adm-btn js-export-form__stop-button" value="<?= Market\Config::getLang('ADMIN_SETUP_RUN_BUTTON_STOP'); ?>" disabled />
	<?

	$tabControl->End();
	?>
</form>
<?
$jsLang = [
	'YANDEX_MARKET_INPUT_COPY_CLIPBOARD_SUCCESS' => Market\Config::getLang('ADMIN_SETUP_RUN_CLIPBOARD_SUCCESS'),
	'YANDEX_MARKET_INPUT_COPY_CLIPBOARD_FAIL' => Market\Config::getLang('ADMIN_SETUP_RUN_CLIPBOARD_FAIL'),
	'YANDEX_MARKET_EXPORT_FORM_QUERY_ERROR_TITLE' => Market\Config::getLang('ADMIN_SETUP_RUN_QUERY_ERROR_TITLE'),
	'YANDEX_MARKET_EXPORT_FORM_QUERY_ERROR_TEXT' => Market\Config::getLang('ADMIN_SETUP_RUN_QUERY_ERROR_TEXT'),
];
?>
<script>
	BX.message(<?= Market\Utils::jsonEncode($jsLang, JSON_UNESCAPED_UNICODE); ?>);
</script>
<?

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';