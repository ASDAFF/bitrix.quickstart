<?php

namespace Yandex\Market\Ui\Update\Check;

use Yandex\Market;
use Bitrix\Main;

Main\Localization\Loc::loadMessages(__FILE__);

class Agent extends Market\Reference\Agent\Regular
{
	const NOTIFY_TAG = 'YANDEX_MARKET_UPDATE_CHECK';

	public static function getDefaultParams()
	{
		return [
			'interval' => 3600 * 24 * 7
		];
	}

	public static function run()
	{
		$moduleName = Market\Config::getModuleName();
		$currentVersion = static::getCurrentVersion($moduleName);
		$newVersion = static::getNewVersion($moduleName);

		if (static::compareVersion($newVersion, $currentVersion) > 0)
		{
			static::addNotify($moduleName, $newVersion);
		}
	}

	protected function addNotify($moduleName, $version)
	{
		if (Market\Config::getOption('ui_update_check_agent_last_version') !== $version)
		{
			Market\Config::setOption('ui_update_check_agent_last_version', $version);

			$message = Market\Config::getLang('UI_UPDATE_CHECK_AGENT_NOTIFY', [
				'#MODULE_NAME#' => $moduleName,
				'#VERSION#' => $version,
				'#LINK#' => '/bitrix/admin/update_system_partner.php?' . http_build_query([
					'tabControl_active_tab' => 'tab2',
					'addmodule' => $moduleName,
					'lang' => LANGUAGE_ID
				])
			]);

			\CAdminNotify::Add([
				'MESSAGE' => $message,
				'MODULE_ID' => $moduleName,
				'TAG' => static::NOTIFY_TAG
			]);

			Event::listenModuleUpdate();
		}
	}

	protected function compareVersion($versionA, $versionB)
	{
		$versionAParts = explode('.', $versionA);
		$versionBParts = explode('.', $versionB);
		$result = 0;

		foreach ($versionAParts as $index => $partA)
		{
			$partAInteger = (int)$partA;
			$partB = isset($versionBParts[$index]) ? $versionBParts[$index] : null;
			$partBInteger = (int)$partB;

			if ($partAInteger < $partBInteger)
			{
				$result = -1;
				break;
			}
			else if ($partAInteger > $partBInteger)
			{
				$result = 1;
				break;
			}
		}

		return $result;
	}

	protected function getCurrentVersion($moduleName)
	{
		return Main\ModuleManager::getVersion($moduleName);
	}

	protected function getNewVersion($moduleName)
	{
		$result = null;

		if (static::loadUpdater())
		{
			$errorMessage = '';

			$updateList = \CUpdateClientPartner::GetUpdatesList($errorMessage, false, 'Y', [ $moduleName ]);

			if (!empty($updateList['MODULE']) && is_array($updateList['MODULE']))
			{
				foreach ($updateList['MODULE'] as $update)
				{
					$isTargetModule = (isset($update['@']['ID']) && $update['@']['ID'] === $moduleName);
					$hasNewVersion = (isset($update['#']['VERSION']) && is_array($update['#']['VERSION']));

					if ($isTargetModule && $hasNewVersion)
					{
						foreach ($update['#']['VERSION'] as $updateVersion)
						{
							if (isset($updateVersion['@']['ID']))
							{
								$result = $updateVersion['@']['ID'];
							}
						}

						break;
					}
				}
			}
		}

		return $result;
	}

	protected function loadUpdater()
	{
		$path = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/update_client_partner.php';
		$result = false;

		if (file_exists($path))
		{
			require_once $path;
			$result = class_exists('\CUpdateClientPartner');
		}

		return $result;
	}
}