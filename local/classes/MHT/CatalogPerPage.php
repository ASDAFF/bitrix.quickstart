<?
	namespace MHT;

	class CatalogPerPage{
		private static $instance = null;
		public static function getInstance(){
			if(!self::$instance){
				self::$instance = new self();
			}
			return self::$instance;
		}

		private $amounts = array(
			/*1 => 96,*/
				2 => 48,
				3 => 24,
				4 => 12,
				5 => 8,
				6 => 4,
			),
			$default = 3,
			$current = null,
			$sessionIndex = 'MHT_CatalogPerPage_amount';

		private function __construct(){
			if(isset($_SESSION[$this->sessionIndex])){
				$this->current = $_SESSION[$this->sessionIndex];
			}
			else{
				$this->current = $this->default;
			}
		}

		function get(){
			return $this->amounts[$this->current];
		}

		function set($n){
			if(!isset($this->amounts[$n])){
				$n = $this->default;
			}
			$this->current = $_SESSION[$this->sessionIndex] = $n;
		}

		function getOptions(){
			$html = '';
			foreach($this->amounts as $value => $name){
				$html .= '<option value="'.$value.'"'.($value == $this->current ? ' selected="selected"' : '').'>'.$name.'</option>';
			}
			return $html;
		}
	}
?>