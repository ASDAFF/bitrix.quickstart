<?
IncludeModuleLangFile(__FILE__);

abstract class CWDA_Plugin {
	final function WDA_PLUGIN(){return true;}
	//
	static function GetGroup(){
		return static::GROUP;
	}
	static function GetCode() {
		return static::CODE;
	}
	static function GetName(){
		if (CWDA::IsUtf()) {
			return static::NAME;
		} else {
			return CWDA::ConvertCharset(static::NAME);
		}
	}
	//
	abstract static function AddHeadData();
	abstract static function ShowSettings($IBlockID=false);
	abstract static function Process($ItemID, $arElement, $Params);
}
?>