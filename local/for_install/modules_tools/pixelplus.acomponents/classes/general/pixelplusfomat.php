<?
class CPixelPlusFormat {
	protected static $CACHE = Array("U"=>Array(),"E"=>Array(),"G"=>Array());
	public $paramformatclass;
	protected $arFormatted;
	
	public function __construct() {
		$this->paramformatclass = new CPixelPlusFormatParamsC;
		$this->arFormatted = Array();
	}
	
	public static function GetFunctionCache() {
		return CPixelPlusFormatSF::$CACHE;
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
