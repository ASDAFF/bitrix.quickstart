<?
	namespace MHT;

	class CatalogPerPageX{
		private static $instance = null;
		public static function getInstance(){
			if(!self::$instance){
				self::$instance = new self();
			}
			return self::$instance;
		}

		private $amounts = array(
			/*1 => 100,*/
				2 => 50,
				3 => 25,
				4 => 20,
				5 => 10,
				6 => 5,
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