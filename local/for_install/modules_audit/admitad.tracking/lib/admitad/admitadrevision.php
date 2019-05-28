<?php

namespace Admitad\Tracking\Admitad;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class AdmitadRevision
{
	public function getStatuses()
	{
		Loader::includeModule('sale');
		$statuses = \Bitrix\Sale\OrderStatus::getAllStatusesNames();
		foreach ($statuses as $key => $status) {
			$statuses[$key] = '[' . $key . '] ' . $status;
		}

		return $statuses;
	}

	public function getPersonTypes()
	{
		$instance = new \CSalePersonType();
		$query = $instance->GetList();
		$result = array();

		while ($item = $query->Fetch()) {
			$result[$item['ID']] = $item['NAME'];
		}

		return $result;
	}

	public static function addRevisionPathRule()
	{
		return \CUrlRewriter::Add(
			array(
				"CONDITION" => "#^/admitad/admitad.xml#",
				"RULE"      => "",
				"ID"        => "",
				"PATH"      => "/admitad/payments-revision.php",
			)
		);
	}

	public static function updateRevisionPathRule($path)
	{
		return \CUrlRewriter::Update(array(
			"SITE_ID" => SITE_ID,
			"CONDITION" => "#^" . Option::get(Admitad::MODULE_ID, 'REVISION_PATH') . "#",
		),
			array(
				"CONDITION" => "#^" . $path . "#",
				"ID"        => "",
				"PATH"      => "/admitad/payments-revision.php",
				"RULE"      => "",
			)
		);
	}
}