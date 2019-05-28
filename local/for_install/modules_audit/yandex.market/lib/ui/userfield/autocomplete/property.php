<?php

namespace Yandex\Market\Ui\UserField\Autocomplete;

use Yandex\Market;

abstract class Property
{
	abstract public static function GetUserTypeDescription();

	/**
	 * @return \Yandex\Market\Ui\UserField\Autocomplete\Provider
	 */
	abstract public static function getDataProvider();

	/**
	 * @return string
	 */
	abstract public static function getLangKey();

	public static function PrepareSettings($property)
	{
		return [];
	}

	public static function GetSettingsHTML($property, $controlName, &$settings)
	{
		$settings = [
			'HIDE' => ['ROW_COUNT', 'COL_COUNT','MULTIPLE_CNT']
		];

		return '';
	}

	public static function GetPropertyFieldHtml($property, $value, $controlName)
	{
		global $APPLICATION;

		$controlId = preg_replace('/[^a-zA-Z0-9_]/i', 'x', $controlName['VALUE'] . '_' . mt_rand(0, 10000));
		$autoCompleteValue = null;
		$dataProvider = static::getDataProvider();
		$langKey = static::getLangKey();
		$result = '';

		if ($controlName['MODE'] !== 'iblock_element_admin')
		{
			if (isset($property['MULTIPLE']) && $property['MULTIPLE'] === 'Y')
			{
				$extractedValueList = static::extractPropertyValueList($value);
				$autoCompleteValueList = $dataProvider::getValueForAutoCompleteMulti($property, $extractedValueList);
				$autoCompleteValue = implode(PHP_EOL, $autoCompleteValueList);
			}
			else
			{
				$extractedValue = static::extractPropertyValue($value);
				$autoCompleteValue = $dataProvider::getValueForAutoComplete($property, $extractedValue);
			}

			ob_start();

			$APPLICATION->IncludeComponent(
				'bitrix:main.lookup.input',
				'ym_userfield',
				array(
					'CONTROL_ID' => $controlId,
					'INPUT_NAME' => $controlName['VALUE'],
					'INPUT_NAME_STRING' => 'inp_'.$controlName['VALUE'],
					'INPUT_VALUE_STRING' => $autoCompleteValue,
					'START_TEXT' => Market\Config::getLang($langKey . 'INPUT_INVITE'),
					'MULTIPLE' => $property['MULTIPLE'],
					'FILTER' => 'Y',
					'PROVIDER' => $dataProvider
				),
				null,
				[ 'HIDE_ICONS' => 'Y' ]
			);

			$result = ob_get_clean();
		}
		else
		{
			$dataList = $dataProvider::getList();
			$isMultiple = ($property['MULTIPLE'] === 'Y');
			$valueMap = [];

			if ($isMultiple)
			{
				if (is_array($value))
				{
					foreach ($value as $valueOne)
					{
						if (isset($valueOne['VALUE']))
						{
							$valueMap[$valueOne['VALUE']] = true;
						}
					}
				}
			}
			else if (isset($value['VALUE']))
			{
				$valueMap[$value['VALUE']] = true;
			}

			$result = '<select name="'.$controlName['VALUE'].'" ' . ($isMultiple ? 'multiple' : '') . '>';

			if ($property['IS_REQUIRED'] !== 'Y')
			{
				$result .= '<option value="">'.htmlspecialcharsbx(GetMessage('MAIN_NO')).'</option>';
			}

			foreach ($dataList as $row)
			{
				$isSelected = isset($valueMap[$row['ID']]);

				$result .= '<option value="'.$row["ID"].'"'.($isSelected ? ' selected': '').'>' . (str_repeat('.', $row['DEPTH_LEVEL'])) . $row["NAME"] . '</option>';
			}

			$result .= '</select>';
		}

		return $result;
	}

	public static function GetPropertyFieldHtmlMulty($property, $value, $controlName)
	{
		$controlName['VALUE'] .= '[]';
		$property['MULTIPLE'] = 'Y';

		return self::GetPropertyFieldHtml($property, $value, $controlName);
	}

	public static function GetAdminListViewHTML($property, $value, $controlName)
	{
		$dataProvider = static::getDataProvider();
		$extractedValueList = static::extractPropertyValueList($value);
		$autoCompleteValueList = $dataProvider::getValueForAutoCompleteMulti($property, $extractedValueList);

		return implode(' / ', $autoCompleteValueList);
	}

	public static function GetAdminFilterHTML($property, $controlName)
	{
		global $APPLICATION;

		$dataProvider = static::getDataProvider();
		$langKey = static::getLangKey();
		$isMainUiFilter = (isset($controlName['FORM_NAME']) && $controlName['FORM_NAME'] == 'main-ui-filter');
		$inputName = $controlName['VALUE'] . ($isMainUiFilter ? '' : '[]');
		$requestValueList = static::getFilterRequestValue($controlName);
		$extractedValueList = static::extractPropertyValueList($requestValueList);
		$autoCompleteValueList = $dataProvider::getValueForAutoCompleteMulti($property, $extractedValueList);
		$autoCompleteValue = implode(PHP_EOL, $autoCompleteValueList);

		ob_start();

		$control_id = $APPLICATION->IncludeComponent(
			'bitrix:main.lookup.input',
			'ym_userfield',
			[
				'INPUT_NAME' => $inputName,
				'INPUT_NAME_STRING' => 'inp_'.$controlName['VALUE'],
				'INPUT_VALUE_STRING' => $autoCompleteValue,
				'START_TEXT' => Market\Config::getLang($langKey . 'INPUT_INVITE'),
				'MULTIPLE' => $isMainUiFilter ? 'N' : 'Y',
				'MAX_WIDTH' => '200',
				'MIN_HEIGHT' => '24',
				'MAIN_UI_FILTER' => ($isMainUiFilter ? 'Y' : 'N'),
				'FILTER' => 'Y',
				'PROVIDER' => $dataProvider
			],
			null,
			[ 'HIDE_ICONS' => 'Y' ]
		);

		?>
		<script type="text/javascript">
			var arClearHiddenFields = arClearHiddenFields;
			if (!!arClearHiddenFields)
			{
				indClearHiddenFields = arClearHiddenFields.length;
				arClearHiddenFields[indClearHiddenFields] = 'jsMLI_<? echo $control_id; ?>';
			}
		</script>
		<?

		return ob_get_clean();
	}

	public static function GetPublicFilterHTML($property, $htmlControl)
	{
		return static::GetAdminFilterHTML($property, $htmlControl);
	}

	protected static function extractPropertyValue($value)
	{
		return isset($value['VALUE']) ? $value['VALUE'] : $value;
	}

	protected static function extractPropertyValueList($valueList)
	{
		$result = [];
		$valueList = (array)$valueList;

		foreach ($valueList as $value)
		{
			$result[] = static::extractPropertyValue($value);
		}

		return $result;
	}

	protected static function getFilterRequestValue($controlName)
	{
		$requestValueList = [];

		if (isset($_REQUEST[$controlName['VALUE']]) && (is_array($_REQUEST[$controlName['VALUE']]) || (int)$_REQUEST[$controlName['VALUE']] > 0))
		{
			$requestValueList = (array)$_REQUEST[$controlName['VALUE']];
		}
		else if (isset($GLOBALS[$controlName['VALUE']]) && (is_array($GLOBALS[$controlName['VALUE']]) || (int)$GLOBALS[$controlName['VALUE']] > 0))
		{
			$requestValueList = (array)$GLOBALS[$controlName['VALUE']];
		}

		return $requestValueList;
	}
}