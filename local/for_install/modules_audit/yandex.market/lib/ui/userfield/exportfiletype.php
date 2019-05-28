<?php

namespace Yandex\Market\Ui\UserField;

use Yandex\Market;
use Bitrix\Main;

Main\Localization\Loc::loadMessages(__FILE__);

class ExportFileType extends \CUserTypeString
{
	function GetAdminListViewHTML($arUserField, $arHtmlControl)
	{
		$result = '';
		$rowId = !empty($arUserField['ROW']['ID']) ? (int)$arUserField['ROW']['ID'] : null;
		$fileName = Market\Export\Setup\Model::normalizeFileName($arHtmlControl['VALUE'], $rowId);

		if ($fileName !== null)
		{
			$filePath = BX_ROOT . '/catalog_export/' . $fileName;

			if (file_exists($_SERVER['DOCUMENT_ROOT'] . $filePath))
			{
				$result = '<img class="b-log-icon" src="/bitrix/images/yandex.market/green.gif" width="14" height="14" alt="" />';
				$result .= '<a href="' . htmlspecialcharsbx($filePath) . '" target="_blank">' . htmlspecialcharsbx($fileName) . '</a>';
			}
			else
			{
				$result = '<img class="b-log-icon" src="/bitrix/images/yandex.market/red.gif" width="14" height="14" alt="" />';
				$result .= Market\Config::getLang('UI_USER_FIELD_EXPORT_FILE_TYPE_FILE_NOT_FOUND');

				if ($rowId)
				{
					$result .=
						'<a href="yamarket_setup_run.php?lang=' . LANGUAGE_ID . '&id=' . $rowId . '">'
						. Market\Config::getLang('UI_USER_FIELD_EXPORT_FILE_TYPE_RUN_EXPORT')
						. '</a>';
				}
				else
				{
					$result = $fileName;
				}
			}
		}

		return $result;
	}
}