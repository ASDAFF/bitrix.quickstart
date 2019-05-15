<?
/*
 * class CIWebGatesInfo
 */

class CIWebGatesInfo {
	
	/*
	 * __construct()
	 * @param $gateName
	 */
	
	private $gate = '';
	public $arParams = array();
	
	
	function __construct($gateName) {
		$this->gate = $gateName;
	}
	
	private function getGatesOptionNamesList() {
		if($this->gate) {
			include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/');
		} else {
			
		}
	}
	
}

?>