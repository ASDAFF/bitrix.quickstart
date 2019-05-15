<?
abstract class CWD_Reviews2_FieldTypes_All {
	const ModuleID = 'webdebug.reviews';
	abstract function GetName();
	abstract function GetCode();
	abstract function GetSort();
	abstract function ShowSettings($arSavedValues);
	abstract function Show($Value, $arParams);
	function _GetMessage($strResult, $Values=false) {
		if (CWD_Reviews2::IsUtf8()) {
			$strResult = $GLOBALS['APPLICATION']->ConvertCharset($strResult, 'CP1251', 'UTF-8');
		}
		if (is_array($Values) && !empty($Values)) {
			$strResult = call_user_func_array('sprintf',array_merge(array($strResult),$Values));
		}
		return $strResult;
	}
}
?>