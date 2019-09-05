<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if (is_array($arResult["ERRORS"])) {
	foreach ($arResult["ERRORS"] as $key => &$error) {
		if (intval($key) == 0 && $key !== 0) {
			$error = str_replace(
				"#FIELD_NAME#",
				"&quot;" . GetMessage("REGISTER_FIELD_" . $key) . "&quot;",
				$error
			);
		}
	}
	unset($error);
}