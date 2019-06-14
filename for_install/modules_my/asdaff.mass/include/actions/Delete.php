<?
class CWDA_Delete extends CWDA_Plugin {
	CONST GROUP = 'GENERAL';
	CONST CODE = 'DELETE';
	CONST NAME = 'Удаление элементов';
	//
	static function GetDescription() {
		$Descr = 'Плагин для удаления элементов инфоблока.';
		if (!CWDA::IsUtf()) {
			$Descr = CWDA::ConvertCharset($Descr);
		}
		return $Descr;
	}
	static function AddHeadData() {
		//
	}
	static function ShowSettings($IBlockID=false) {
	}
	static function Process($ElementID, $arElement, $Params) {
		$bResult = false;
		if(CIBlockElement::Delete($ElementID)) {
			CWDA::Log('Delete element #'.$ElementID, self::CODE);
			$bResult = true;
		}
		return $bResult;
	}
}
?>