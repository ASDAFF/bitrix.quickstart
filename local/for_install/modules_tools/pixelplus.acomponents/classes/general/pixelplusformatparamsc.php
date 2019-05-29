<?
class CPixelPlusFormatParamsC {
	protected $arFormatParams;
	
	public function __construct() {
		$this->arFormatParams = Array();
	}
	public function GetParams() {
		return $this->arFormatParams;
	}
	public function SetParams($arFormatParams) {
		$this->arFormatParams = $arFormatParams;
	}
	public function ClearParams() {
		$this->arFormatParams = Array();
	}
	public function AddParam($pid,$arValue) {
		$this->arFormatParams[$pid] = $arValue;
	}
	public function RemoveParam($pid) {
		unset($this->arFormatParams[$pid]);
	}
	public function GetParam($pid) {
		if ($this->arFormatParams[$pid]) {
			return $this->arFormatParams[$pid];
		}
		return false;
	}
	
	static function FormatCheck($fid,&$arParams) {
		if ($fid == "RESIZE") {
			if (!$arParams['arSize']) {
				return false;
			} else {
				$arParams['arSize']['width'] = intval($arParams['arSize']['width']);
				$arParams['arSize']['height'] = intval($arParams['arSize']['height']);
			}
			if (!$arParams['resizeType']) $arParams['resizeType'] = BX_RESIZE_IMAGE_PROPORTIONAL;
			if (!$arParams['bInitSizes']) $arParams['bInitSizes'] = false;			
		}
		return true;
	}
}
?>