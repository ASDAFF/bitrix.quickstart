<?
	namespace Webprofy;

	class Session{
		private $index;
		function __construct($name){
			$this->index = 'Step_'.$name;
		}

		function clear(){
			unset($_SESSION[$this->index]);
		}

		function get($index, $default){
			$a = $_SESSION[$this->index];
			if(isset($a[$index])){
				return $a[$index];
			}
			$this->set($index, $default);
			return $default;
		}

		function set($index, $value){
			$_SESSION[$this->index][$index] = $value;
			return $this;
		}
	}