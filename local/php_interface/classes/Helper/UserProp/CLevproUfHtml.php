<?
namespace Helper\UserProp;

use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Main\UserField\Types\StringType;

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class CLevproUfHtml extends StringType
{
	public static function GetUserTypeDescription(): array
	{
		return array(
			"USER_TYPE_ID" => "levproufhtml",
			"CLASS_NAME" => __CLASS__,
			"DESCRIPTION" => GetMessage("LEVPRO_UFHTML_PROP_NAME"),
			"BASE_TYPE" => "string",
		);
	}

	public static function GetEditFormHTML($arUserField, $arHtmlControl): string
	{
		if ($arUserField["ENTITY_VALUE_ID"] < 1 && strlen($arUserField["SETTINGS"]["DEFAULT_VALUE"]) > 0)
			$arHtmlControl["VALUE"] = htmlspecialcharsbx($arUserField["SETTINGS"]["DEFAULT_VALUE"]);
		if ($arUserField["SETTINGS"]["ROWS"] < 8)
			$arUserField["SETTINGS"]["ROWS"] = 8;

		if ($arUserField['MULTIPLE'] == 'Y')
			$name = preg_replace("/[\[\]]/i", "_", $arHtmlControl["NAME"]);
		else
			$name = $arHtmlControl["NAME"];

		ob_start();

		\CFileMan::AddHTMLEditorFrame(
			$name,
			$arHtmlControl["VALUE"],
			$name . "_TYPE",
			strlen($arHtmlControl["VALUE"]) ? "html" : "text",
			array(
				'height' => $arUserField['SETTINGS']['ROWS'] * 10,
			)
		);

		if ($arUserField['MULTIPLE'] == 'Y')
			echo '<input type="hidden" name="' . $arHtmlControl["NAME"] . '" >';

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public static function OnBeforeSave($arUserField, $value)
	{
		if ($arUserField['MULTIPLE'] == 'Y') {
			foreach ($_POST as $key => $val) {
				if (preg_match("/" . $arUserField['FIELD_NAME'] . "_([0-9]+)_$/i", $key, $m)) {
					$value = $val;
					unset($_POST[$key]);
					break;
				}
			}
		}
		return $value;
	}
}
