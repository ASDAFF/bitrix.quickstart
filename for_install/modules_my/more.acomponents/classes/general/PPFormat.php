<?
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

class CPPFormat {
	protected static $CACHE = Array("U"=>Array(),"E"=>Array(),"G"=>Array());
	public $paramformatclass;
	protected $arFormatted;
	
	public function __construct() {
		$this->paramformatclass = new CPPFormatParamsC;
		$this->arFormatted = Array();
	}
	
	public static function GetFunctionCache() {
		return CPPFormatSF::$CACHE;
	}
	
	public function SetFormatted($arFormatted=Array()) {
		if (is_array($arFormatted) && count($arFormatted) > 0) {
			$this->arFormatted = $arFormatted;
			return true;
		}
		return false;
	}
	
	public function GetFormatted() {
		return $this->arFormatted;
	}
	public function GetDispayFields() {
		return true;
	}
}
?>
