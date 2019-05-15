<?php
/**
 * Bitrix vars
 *
 * @var CDatabase $DB
 * @var CUser     $USER
 * @var CMain     $APPLICATION
 *
 */

define('PUBLIC_AJAX_MODE', true);
define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('DisableEventsCheck', true);
define('BX_SECURITY_SHOW_MESSAGE', true);

if($_SERVER['REQUEST_METHOD'] != 'POST' || !$_POST['API_AUTH_AJAX'] || !preg_match('/^[A-Za-z0-9_]{2}$/', $_POST['siteId']))
	die();

define('SITE_ID', htmlspecialchars($_POST['siteId']));
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

global $APPLICATION, $USER;

$APPLICATION->RestartBuffer();
header('Content-Type: text/html; charset=' . LANG_CHARSET);
?>
	<div class="api_tab" id="api_auth_tab">
		<div class="api_tab_nav">
			<div class="api_active">
				<a class="api_tab_anchor" href="#api_auth_login">Вход</a>
			</div>
			<div>
				<a class="api_tab_anchor" href="#api_auth_register">Регистрация</a>
			</div>
		</div>
		<div class="api_tab_panel">
			<div id="api_auth_login" class="api_active"><? $APPLICATION->IncludeComponent('api:auth.login', ''); ?></div>
			<div id="api_auth_register"><? $APPLICATION->IncludeComponent('api:auth.register', ''); ?></div>
			<div id="api_auth_restore"><? $APPLICATION->IncludeComponent('api:auth.restore', ''); ?></div>
		</div>
	</div>
<?
die();
?>